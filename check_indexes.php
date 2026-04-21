<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$results = \Illuminate\Support\Facades\DB::select('SHOW INDEX FROM legal_opinions_library');

echo "Indexes for legal_opinions_library:\n";
foreach ($results as $r) {
    echo " - " . $r->Key_name . " (Column: " . $r->Column_name . ", Index_type: " . $r->Index_type . ")\n";
}
