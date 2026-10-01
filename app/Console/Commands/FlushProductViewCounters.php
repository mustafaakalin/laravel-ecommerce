<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Throwable;

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

        $sourceKey = 'product:view:deltas';
        $processingKey = $sourceKey . ':processing:' . Str::uuid();
        $redis = Redis::connection();
        $flushed = false;

        try {
            if (! $redis->exists($sourceKey)) {
                return self::SUCCESS;
            }

            // Atomically detach the current batch. New views go into a fresh hash.
            $redis->rename($sourceKey, $processingKey);

            $deltas = $redis->hgetall($processingKey);
            if (! $deltas) {
                $flushed = true;
                return self::SUCCESS;
            }

            $ids = array_map('intval', array_keys($deltas));
            $existingIds = Product::query()
                ->whereIn('id', $ids)
                ->pluck('id')
                ->map(static fn ($id): int => (int) $id)
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

            $flushed = true;

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Product view counters could not be flushed: ' . $e->getMessage());

            // Never lose a detached batch when the database is unavailable.
            // Re-add it to the live hash so the next run can retry it.
            if ($redis->exists($processingKey)) {
                foreach ($redis->hgetall($processingKey) as $productId => $count) {
                    $redis->hIncrBy($sourceKey, $productId, (int) $count);
                }
            }

            return self::FAILURE;
        } finally {
            if ($flushed) {
                $redis->del($processingKey);
            }

            $lock->release();
        }
    }
}
