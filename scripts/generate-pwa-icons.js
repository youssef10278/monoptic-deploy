import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Créer des icônes SVG simples pour la PWA
const createSVGIcon = (size) => {
    return `<svg width="${size}" height="${size}" viewBox="0 0 ${size} ${size}" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:#3B82F6;stop-opacity:1" />
      <stop offset="100%" style="stop-color:#1E40AF;stop-opacity:1" />
    </linearGradient>
  </defs>
  <rect width="${size}" height="${size}" rx="20" fill="url(#grad1)"/>
  <circle cx="${size/2}" cy="${size/2 - 20}" r="${size/8}" fill="white" opacity="0.9"/>
  <rect x="${size/2 - size/6}" y="${size/2 - 10}" width="${size/3}" height="${size/8}" rx="5" fill="white" opacity="0.9"/>
  <text x="${size/2}" y="${size - 30}" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="${size/12}" font-weight="bold">M</text>
</svg>`;
};

// Créer les fichiers SVG
const publicDir = path.join(__dirname, '..', 'public');

// Icône 192x192
fs.writeFileSync(path.join(publicDir, 'pwa-192x192.svg'), createSVGIcon(192));
console.log('✅ Icône PWA 192x192 créée');

// Icône 512x512
fs.writeFileSync(path.join(publicDir, 'pwa-512x512.svg'), createSVGIcon(512));
console.log('✅ Icône PWA 512x512 créée');

// Favicon
fs.writeFileSync(path.join(publicDir, 'favicon.svg'), createSVGIcon(32));
console.log('✅ Favicon SVG créé');

console.log('🎉 Toutes les icônes PWA ont été générées !');
