<?php
echo "PHP Version: " . phpversion() . "\n";
echo "Loaded Configuration File: " . php_ini_loaded_file() . "\n";
echo "GD Extension Loaded: " . (extension_loaded('gd') ? 'YES' : 'NO') . "\n";

if (!extension_loaded('gd')) {
    echo "\nWARNING: GD is NOT loaded.\n";
    echo "Please open '" . php_ini_loaded_file() . "'\n";
    echo "Find ';extension=gd' and remove the semicolon.\n";
} else {
    echo "\nSUCCESS: GD is loaded!\n";
}
