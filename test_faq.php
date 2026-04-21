<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$faqMatcher = app(\App\Services\FaqResponseMatcher::class);

// Let's see what's in the FAQ
$faqs = \App\Models\FaqResponse::all();
echo "Total FAQs: " . $faqs->count() . "\n";
foreach ($faqs as $f) {
    echo " - Inquiry: " . $f->inquiry . "\n";
    $match = $faqMatcher->findBestMatch($f->inquiry);
    echo "   Match found: " . ($match ? 'YES' : 'NO') . "\n";
}
