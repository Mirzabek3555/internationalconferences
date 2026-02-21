<?php
// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Country;
use App\Models\Conference;

echo "Checking Countries...\n";
foreach (Country::all() as $country) {
    if (!mb_check_encoding($country->name, 'UTF-8'))
        echo "Invalid UTF-8 in Country ID {$country->id} Name\n";
    if (!mb_check_encoding($country->name_en, 'UTF-8'))
        echo "Invalid UTF-8 in Country ID {$country->id} Name EN\n";
    if (!mb_check_encoding($country->conference_name ?? '', 'UTF-8'))
        echo "Invalid UTF-8 in Country ID {$country->id} Conf Name\n";
}

echo "Checking Conferences...\n";
foreach (Conference::all() as $conference) {
    if (!mb_check_encoding($conference->title, 'UTF-8'))
        echo "Invalid UTF-8 in Conference ID {$conference->id} Title\n";
}

echo "Done.\n";
