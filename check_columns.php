<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$results = \Illuminate\Support\Facades\DB::select('DESCRIBE legal_opinions_library');

echo "Columns for legal_opinions_library:\n";
foreach ($results as $r) {
    echo " - " . $r->Field . " (" . $r->Type . ")\n";
}
