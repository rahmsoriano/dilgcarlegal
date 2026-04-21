<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$query = "opinion of garcia";
$results = \App\Models\LegalOpinionLibrary::query()->search($query)->limit(5)->get();

echo "Results count for '$query': " . $results->count() . "\n";
foreach ($results as $r) {
    echo " - Title: " . $r->title . " (Score: " . ($r->score ?? 'N/A') . ")\n";
}
