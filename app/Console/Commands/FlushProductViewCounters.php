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
        $redis = Redis::connection();

        try {
            if (! $redis->exists('product:view:deltas')) {
                return self::SUCCESS;
            }

            // Atomic rename prevents increments during the flush from being lost.
            $redis->rename('product:view:deltas', $processingKey);
            $deltas = $redis->hgetall($processingKey);

            if (! $deltas) {
                return self::SUCCESS;
            }

            $ids = array_map('intval', array_keys($deltas));
            $existingIds = Product::query()
                ->whereIn('id', $ids)
                ->pluck('id')
                ->map(static fn ($id) => (int) $id)
                ->all();

            if ($existingIds) {
                $cases = [];
                $bindings = [];
                $whereIds = [];

                foreach ($existingIds as $id) {
                    $cases[] = 'WHEN ? THEN ?';
                    $bindings[] = $id;
                    $bindings[] = (int) ($deltas[(string) $id] ?? $deltas[$id] ?? 0);
                    $whereIds[] = $id;
                }

                DB::update(
                    'UPDATE products
                     SET view_count = view_count + CASE id ' . implode(' ', $cases) . ' END
                     WHERE id IN (' . implode(',', array_fill(0, count($whereIds), '?')) . ')',
                    [...$bindings, ...$whereIds]
                );
            }

            return self::SUCCESS;
        } finally {
            $redis->del($processingKey);
            $lock->release();
        }
    }
}
