<?php

namespace App\Services;

trait ImageCompressor
{
    /**
     * Papka ichidagi rasmlarni siqish (recursive)
     */
    private function compressImagesInDir(string $dir): void
    {
        if (!is_dir($dir))
            return;

        // Barcha fayllar va papkalarni olish
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($items as $item) {
            if ($item->isFile()) {
                $ext = strtolower($item->getExtension());
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $this->overwriteWithCompressedImage($item->getPathname());
                }
            }
        }
    }

    /**
     * Rasmni siqib ustidan yozish
     */
    private function overwriteWithCompressedImage(string $path): void
    {
        try {
            $info = @getimagesize($path);
            if (!$info)
                return;

            $mime = $info['mime'];
            $width = $info[0];
            $height = $info[1];

            // Target size limits
            $maxWidth = 600; // Enough for document reading

            // Agar rasm kichik bo'lsa tegmash
            if ($width <= $maxWidth && filesize($path) < 100 * 1024) {
                return;
            }

            if ($width > $maxWidth) {
                $newWidth = $maxWidth;
                $newHeight = ($height / $width) * $newWidth;
            } else {
                $newWidth = $width;
                $newHeight = $height;
            }

            $image = null;
            if ($mime == 'image/jpeg') {
                $image = @imagecreatefromjpeg($path);
            } elseif ($mime == 'image/png') {
                $image = @imagecreatefrompng($path);
            }

            if (!$image)
                return;

            $bg = imagecreatetruecolor($newWidth, $newHeight);
            $white = imagecolorallocate($bg, 255, 255, 255);
            imagefilledrectangle($bg, 0, 0, $newWidth, $newHeight, $white);

            // Resize
            imagecopyresampled($bg, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            // Save as JPG (even if it was PNG) to save space, unless transparency critical (but for docs usually fine)
            // Quality 50
            // Naming it same as original path but if original was PNG, we should probably output JPG?
            // But HTML refers to original name. So if original was .png, saving as .png but compressed?
            // PNG compression is lossless (0-9). JPG is lossy.
            // Converting PNG -> JPG and saving as .png file is invalid but browsers/viewers might handle it? 
            // Better to keep extension logic or update HTML.
            // Since updating HTML is hard (already done), let's keep format but optimize.

            if ($mime == 'image/png') {
                // Convert to JPG content but save with .png extension? No, that's risky for strict parsers.
                // Save as PNG with max compression
                // imagepng($image, $path, $quality, $filters)
                // quality 0-9. 9 is max compression.
                // But PNG is still large for photos.
                // If it is a photo, JPG is better.
                // DOMPDF uses simple image handling.

                // Let's try to convert to JPG and update the path? 
                // But HTML has the old path.
                // If we save JPG content into .png file, DOMPDF might choke or check mime type.
                // Let's stick to simple resize for PNG and max compression.

                // However, converting to JPG is the request for SIZE.
                // Let's force JPG conversion and update the HTML link? 
                // No, HTML string is already separate.

                // Alternative: Save as JPG, and update HTML in `convertWordToHtml`.
                // But `compressImagesInDir` is generic.

                // Let's just resize and save. For PNG, we can't do quality 50 like JPG.
                // We can reduce colors (imagetruecolortopalette).

                // Actually, best bet: save as JPEG.
                // Rename file to .jpg.
                // But we need to update HTML references if we rename.

                // Let's stick to: Rename file to .jpg.
                // The caller (`convertWordToHtml`) iterates and fixes paths? 
                // No, `convertWordToHtml` does preg_replace later.
                // It iterates `src` in HTML.
                // So if we rename files NOW, the HTML `src` which points to `image.png` will fail.

                // Strategy:
                // 1. Resize only.
                // 2. Save as original format.
                // 3. For JPEG, use quality 50.
                // 4. For PNG, use quality 9 (compression level).

                if ($mime == 'image/jpeg') {
                    imagejpeg($bg, $path, 50);
                } elseif ($mime == 'image/png') {
                    // Reduce colors to make it smaller?
                    // imagetruecolortopalette($bg, false, 255);
                    imagepng($bg, $path, 9);
                }
            } else {
                imagejpeg($bg, $path, 50);
            }

            imagedestroy($image);
            imagedestroy($bg);

        } catch (\Exception $e) {
            // Ignore errors
        }
    }
}
