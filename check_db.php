<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
echo "LegalOpinions count: " . \App\Models\LegalOpinionLibrary::count() . "\n";
echo "FaqResponses count: " . \App\Models\FaqResponse::count() . "\n";
