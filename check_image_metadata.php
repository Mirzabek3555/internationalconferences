<?php
$files = [
    'public/images/isoc_logo.png',
    'public/images/logo.png',
    'public/images/isc-logo-full.svg',
    'public/images/isc-header-globe.svg'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "File: $file\n";
        $info = getimagesize($file);
        if ($info) {
            echo "Dimensions: {$info[0]}x{$info[1]}\n";
            echo "Mime: {$info['mime']}\n";
        } else {
            echo "Not an image or SVG.\n";
        }
    } else {
        echo "File not found: $file\n";
    }
    echo "-------------------\n";
}
