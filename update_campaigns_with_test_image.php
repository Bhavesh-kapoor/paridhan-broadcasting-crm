<?php

/**
 * Quick script to update campaigns with test image URL
 * Run: php update_campaigns_with_test_image.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Campaign;

$testImageUrl = 'https://d8iqbmvu05s9c.cloudfront.net/ajprhqgqg1otf7d5sm7u3brf27gv';

echo "Updating campaigns with test image...\n\n";

// Find campaigns without images
$campaigns = Campaign::where(function($q) {
    $q->whereNull('image')
      ->orWhere('image', '');
})->get();

$count = $campaigns->count();

if ($count === 0) {
    echo "No campaigns found without images.\n";
    exit(0);
}

echo "Found {$count} campaign(s) without images.\n";
echo "Test Image URL: {$testImageUrl}\n\n";

foreach ($campaigns as $campaign) {
    $campaign->image = $testImageUrl;
    $campaign->save();
    echo "✓ Updated campaign: {$campaign->name} (ID: {$campaign->id})\n";
}

echo "\n✅ Done! Updated {$count} campaign(s).\n";
echo "You can now send these campaigns and they will use the test image.\n";

