<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class FlushProductViewCounters extends Command
{
    protected $signature = 'products:flush-view-counters';

    protected $description = 'Flush buffered Redis product view counters into PostgreSQL.';

    public function handle(): int
    {
        $lock = Cache::lock('lock:products:flush-view-counters', 55);

        if (! $lock->get()) {
            return self::SUCCESS;
        }

        $processingKey = 'product:view:deltas:processing:' . Str::uuid();

        try {
            // Rename is atomic: requests arriving during the flush start writing
            // to a fresh hash and cannot be lost by a read/delete race.
            $redis = Redis::connection();
            if (! $redis->exists('product:view:deltas')) {
                return self::SUCCESS;
            }

            $redis->rename('product:view:deltas', $processingKey);
            $deltas = $redis->hgetall($processingKey);

            if (! $deltas) {
                $redis->del($processingKey);
                return self::SUCCESS;
            }

            $ids = array_map('intval', array_keys($deltas));
            $existingIds = Product::query()
                ->whereIn('id', $ids)
                ->pluck('id')
                ->all();

            $existingLookup = array_fill_keys($existingIds, true);
            $cases = [];
            $bindings = [];
            $placeholders = [];

            foreach ($deltas as $id => $delta) {
                $id = (int) $id;
                if (! isset($existingLookup[$id])) {
                    continue;
                }

                $cases[] = 'WHEN ? THEN ?';
                $bindings[] = $id;
                $bindings[] = (int) $delta;
                $placeholders[] = '?';
            }

            if ($cases) {
                DB::update(
                    'UPDATE products
                     SET view_count = view_count + CASE id ' . implode(' ', $cases) . ' END
                     WHERE id IN (' . implode(',', $placeholders) . ')',
                    [...$bindings, ...array_keys(array_filter($existingLookup, static fn ($value, $id) => in_array((int) $id, $ids, true), ARRAY_FILTER_USE_BOTH))]
                );
            }

            $redis->del($processingKey);
            return self::SUCCESS;
        } finally {
            optional($lock)->release();
        }
    }
}
