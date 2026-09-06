<?php

namespace App\Services\Media;

use Illuminate\Support\Facades\Storage;

/**
 * Makes small JPEG thumbnails for product images.
 *
 * Product pictures are uploaded at full camera resolution (~500 KB each) but
 * the app only ever draws them at 68x68 and 56x56. Serving the originals meant
 * downloading roughly 4.4 MB to paint nine thumbnails, which is why pictures
 * took seconds to appear.
 *
 * Uses plain GD, which is available on the shared hosting, so this needs no
 * extra Composer dependency.
 */
class ThumbnailGenerator
{
    /** Long edge in pixels. 68pt at 3x device pixel ratio is ~204px. */
    public const MAX_EDGE = 240;

    public const QUALITY = 82;

    public const DIRECTORY = 'products/thumbs';

    /** Storage path of the thumbnail for a source image, generated or not. */
    public static function pathFor(string $sourcePath): string
    {
        $name = pathinfo($sourcePath, PATHINFO_FILENAME);

        return self::DIRECTORY.'/'.$name.'.jpg';
    }

    public static function exists(string $sourcePath): bool
    {
        return Storage::disk('public')->exists(self::pathFor($sourcePath));
    }

    /**
     * Creates the thumbnail unless it already exists.
     *
     * @return string|null Storage path of the thumbnail, or null when the
     *                     source is missing or cannot be decoded.
     */
    public static function generate(string $sourcePath, bool $force = false): ?string
    {
        $disk = Storage::disk('public');
        $target = self::pathFor($sourcePath);

        if (! $force && $disk->exists($target)) {
            return $target;
        }

        if (! $disk->exists($sourcePath) || ! function_exists('imagecreatetruecolor')) {
            return null;
        }

        $source = self::decode($disk->get($sourcePath));

        if (! $source) {
            return null;
        }

        $width = imagesx($source);
        $height = imagesy($source);

        if ($width < 1 || $height < 1) {
            return null;
        }

        $scale = min(1, self::MAX_EDGE / max($width, $height));
        $newWidth = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));

        $thumb = imagecreatetruecolor($newWidth, $newHeight);

        // Flatten transparency onto white so PNGs do not turn black as JPEG.
        $white = imagecolorallocate($thumb, 255, 255, 255);
        imagefilledrectangle($thumb, 0, 0, $newWidth, $newHeight, $white);
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        ob_start();
        imagejpeg($thumb, null, self::QUALITY);
        $data = (string) ob_get_clean();

        if ($data === '') {
            return null;
        }

        $disk->put($target, $data);

        return $target;
    }

    private static function decode(string $contents): \GdImage|false
    {
        try {
            return @imagecreatefromstring($contents);
        } catch (\Throwable) {
            return false;
        }
    }
}
