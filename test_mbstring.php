<?php
echo "mbstring loaded: " . (extension_loaded('mbstring') ? 'YES' : 'NO') . "\n";
echo "Str::limit test: " . \Illuminate\Support\Str::limit('O\'zbekiston', 5) . "\n";
// Create a broken UTF8 string to see if json_encode fails
$broken = substr("O'zbekiñ", 0, 8); // ñ is 2 bytes. 8 chars includes half of ñ?
// O ' z b e k i ñ
// 1 1 1 1 1 1 1 2 = 9 bytes.
// substr(..., 0, 8) keeps first byte of ñ.
echo "Broken string created.\n";
try {
    echo json_encode(['data' => $broken], JSON_THROW_ON_ERROR);
} catch (\Exception $e) {
    echo "JSON Encode Error: " . $e->getMessage() . "\n";
}
