<?php
// Diagnostic complet des URLs

echo "🔍 DIAGNOSTIC COMPLET DES URLS\n";
echo "================================\n\n";

// 1. Variables d'environnement
echo "📝 VARIABLES D'ENVIRONNEMENT:\n";
echo "APP_URL: " . ($_ENV['APP_URL'] ?? 'NOT SET') . "\n";
echo "ASSET_URL: " . ($_ENV['ASSET_URL'] ?? 'NOT SET') . "\n";
echo "MIX_ASSET_URL: " . ($_ENV['MIX_ASSET_URL'] ?? 'NOT SET') . "\n";
echo "VITE_APP_URL: " . ($_ENV['VITE_APP_URL'] ?? 'NOT SET') . "\n";
echo "\n";

// 2. Variables Railway
echo "🚂 VARIABLES RAILWAY:\n";
echo "RAILWAY_ENVIRONMENT: " . ($_ENV['RAILWAY_ENVIRONMENT'] ?? 'NOT SET') . "\n";
echo "RAILWAY_PROJECT_NAME: " . ($_ENV['RAILWAY_PROJECT_NAME'] ?? 'NOT SET') . "\n";
echo "PORT: " . ($_ENV['PORT'] ?? 'NOT SET') . "\n";
echo "\n";

// 3. Headers HTTP
echo "🌐 HEADERS HTTP:\n";
if (isset($_SERVER['HTTP_HOST'])) {
    echo "HTTP_HOST: " . $_SERVER['HTTP_HOST'] . "\n";
}
if (isset($_SERVER['SERVER_NAME'])) {
    echo "SERVER_NAME: " . $_SERVER['SERVER_NAME'] . "\n";
}
if (isset($_SERVER['REQUEST_SCHEME'])) {
    echo "REQUEST_SCHEME: " . $_SERVER['REQUEST_SCHEME'] . "\n";
}
if (isset($_SERVER['HTTPS'])) {
    echo "HTTPS: " . $_SERVER['HTTPS'] . "\n";
}
echo "\n";

// 4. Test de parsing d'URL
echo "🔧 TEST PARSING URL:\n";
$testUrl = $_ENV['APP_URL'] ?? 'https://monoptic-deploy-production.up.railway.app';
echo "URL à tester: " . $testUrl . "\n";
$parsed = parse_url($testUrl);
if ($parsed) {
    echo "Scheme: " . ($parsed['scheme'] ?? 'MISSING') . "\n";
    echo "Host: " . ($parsed['host'] ?? 'MISSING') . "\n";
    echo "Port: " . ($parsed['port'] ?? 'DEFAULT') . "\n";
    echo "Path: " . ($parsed['path'] ?? '/') . "\n";
} else {
    echo "❌ ERREUR: URL invalide!\n";
}
echo "\n";

// 5. Test de reconstruction d'URL
echo "🔨 TEST RECONSTRUCTION URL:\n";
if ($parsed && isset($parsed['scheme'], $parsed['host'])) {
    $reconstructed = $parsed['scheme'] . '://' . $parsed['host'];
    if (isset($parsed['port']) && $parsed['port'] != 80 && $parsed['port'] != 443) {
        $reconstructed .= ':' . $parsed['port'];
    }
    echo "URL reconstruite: " . $reconstructed . "\n";
    echo "Match original: " . ($reconstructed === rtrim($testUrl, '/') ? '✅ OUI' : '❌ NON') . "\n";
}
echo "\n";

// 6. Vérifier le manifest Vite
echo "📦 MANIFEST VITE:\n";
$manifestPath = 'public/build/manifest.json';
if (file_exists($manifestPath)) {
    $manifest = json_decode(file_get_contents($manifestPath), true);
    if ($manifest) {
        echo "✅ Manifest trouvé avec " . count($manifest) . " entrées\n";
        
        // Chercher des URLs problématiques
        $manifestContent = file_get_contents($manifestPath);
        if (strpos($manifestContent, 'https//') !== false) {
            echo "❌ PROBLÈME: Double protocole trouvé dans le manifest!\n";
            $lines = explode("\n", $manifestContent);
            foreach ($lines as $i => $line) {
                if (strpos($line, 'https//') !== false) {
                    echo "Ligne " . ($i + 1) . ": " . trim($line) . "\n";
                }
            }
        } else {
            echo "✅ Aucun double protocole dans le manifest\n";
        }
    } else {
        echo "❌ Manifest invalide (JSON corrompu)\n";
    }
} else {
    echo "❌ Manifest non trouvé - Assets pas encore buildés\n";
}

echo "\n🏁 DIAGNOSTIC TERMINÉ\n";
?>
