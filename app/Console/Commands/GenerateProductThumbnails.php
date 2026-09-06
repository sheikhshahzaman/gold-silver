<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\Media\ThumbnailGenerator;
use Illuminate\Console\Command;

class GenerateProductThumbnails extends Command
{
    protected $signature = 'products:thumbnails {--force : Rebuild thumbnails that already exist}';

    protected $description = 'Generate small thumbnails for product images so the app does not download full-size pictures';

    public function handle(): int
    {
        $products = Product::whereNotNull('image')->get();
        $force = (bool) $this->option('force');

        $made = 0;
        $skipped = 0;
        $failed = 0;
        $savedBytes = 0;

        foreach ($products as $product) {
            if (! $force && ThumbnailGenerator::exists($product->image)) {
                $skipped++;
                continue;
            }

            $disk = \Illuminate\Support\Facades\Storage::disk('public');
            $before = $disk->exists($product->image) ? $disk->size($product->image) : 0;

            $path = ThumbnailGenerator::generate($product->image, force: $force);

            if ($path === null) {
                $failed++;
                $this->warn("  could not process: {$product->image}");
                continue;
            }

            $after = $disk->size($path);
            $savedBytes += max(0, $before - $after);
            $made++;

            $this->line(sprintf(
                '  %-46s %6.0f KB -> %5.0f KB',
                \Illuminate\Support\Str::limit($product->name, 44),
                $before / 1024,
                $after / 1024,
            ));
        }

        $this->newLine();
        $this->info(sprintf(
            'Generated %d, skipped %d, failed %d. Saved %.1f MB per full page load.',
            $made,
            $skipped,
            $failed,
            $savedBytes / 1048576,
        ));

        return self::SUCCESS;
    }
}
