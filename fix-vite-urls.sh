#!/bin/bash

echo "🔧 Fixing Vite asset URLs..."

# Build assets first
npm run build

# Fix double protocol in manifest files
if [ -f "public/build/manifest.json" ]; then
    echo "📝 Fixing manifest.json..."
    sed -i 's|https//|https://|g' public/build/manifest.json
fi

# Fix any CSS files with double protocol
find public/build -name "*.css" -exec sed -i 's|https//|https://|g' {} \;

# Fix any JS files with double protocol  
find public/build -name "*.js" -exec sed -i 's|https//|https://|g' {} \;

echo "✅ Vite URLs fixed!"
