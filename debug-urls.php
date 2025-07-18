<?php
// Script de debug pour vérifier les URLs générées

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG URLs ===\n";
echo "APP_URL: " . env('APP_URL') . "\n";
echo "ASSET_URL: " . env('ASSET_URL') . "\n";
echo "MIX_ASSET_URL: " . env('MIX_ASSET_URL') . "\n";
echo "\n";

echo "=== URLs générées ===\n";
echo "asset('css/landing.css'): " . asset('css/landing.css') . "\n";
echo "asset('images/logo.png'): " . asset('images/logo.png') . "\n";
echo "url('/'): " . url('/') . "\n";
echo "\n";

echo "=== Configuration ===\n";
echo "config('app.url'): " . config('app.url') . "\n";
echo "config('app.asset_url'): " . config('app.asset_url') . "\n";

echo "\n=== Test URL Parsing ===\n";
$testUrl = env('APP_URL');
echo "Original URL: " . $testUrl . "\n";
$parsed = parse_url($testUrl);
echo "Parsed scheme: " . ($parsed['scheme'] ?? 'MISSING') . "\n";
echo "Parsed host: " . ($parsed['host'] ?? 'MISSING') . "\n";
