const fs = require('fs');
const { createCanvas } = require('canvas');

function createIcon(size) {
    const canvas = createCanvas(size, size);
    const ctx = canvas.getContext('2d');
    
    // Créer le gradient
    const gradient = ctx.createLinearGradient(0, 0, size, size);
    gradient.addColorStop(0, '#3B82F6');
    gradient.addColorStop(1, '#1E40AF');
    
    // Fond avec coins arrondis
    ctx.fillStyle = gradient;
    ctx.fillRect(0, 0, size, size);
    
    // Cercle (lunettes)
    ctx.fillStyle = 'rgba(255, 255, 255, 0.9)';
    ctx.beginPath();
    ctx.arc(size * 0.5, size * 0.4, size * 0.125, 0, 2 * Math.PI);
    ctx.fill();
    
    // Rectangle (monture)
    ctx.fillStyle = 'rgba(255, 255, 255, 0.9)';
    ctx.fillRect(size * 0.33, size * 0.45, size * 0.33, size * 0.125);
    
    // Lettre M
    ctx.fillStyle = 'white';
    ctx.font = `bold ${size * 0.083}px Arial`;
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText('M', size * 0.5, size * 0.84);
    
    return canvas;
}

// Créer les icônes
const icon192 = createIcon(192);
const icon512 = createIcon(512);

// Sauvegarder
fs.writeFileSync('public/pwa-192x192.png', icon192.toBuffer('image/png'));
fs.writeFileSync('public/pwa-512x512.png', icon512.toBuffer('image/png'));

console.log('Icônes PWA créées avec succès !');
