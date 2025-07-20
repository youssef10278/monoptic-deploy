import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        VitePWA({
            registerType: 'autoUpdate',
            includeAssets: ['favicon.svg', 'pwa-192x192.png', 'pwa-512x512.png'],
            manifest: {
                name: 'Monoptic - Gestion Optique',
                short_name: 'Monoptic',
                description: 'Application de gestion pour magasins d\'optique',
                theme_color: '#3B82F6',
                background_color: '#ffffff',
                display: 'standalone',
                scope: '/',
                start_url: '/',
                icons: [
                    {
                        src: 'favicon.svg',
                        sizes: 'any',
                        type: 'image/svg+xml',
                        purpose: 'any'
                    },
                    {
                        src: 'favicon.svg',
                        sizes: 'any',
                        type: 'image/svg+xml',
                        purpose: 'maskable'
                    }
                ],
                screenshots: [
                    {
                        src: 'screenshot-desktop.svg',
                        sizes: '1280x720',
                        type: 'image/svg+xml',
                        form_factor: 'wide',
                        label: 'Monoptic - Interface Desktop'
                    },
                    {
                        src: 'screenshot-mobile.svg',
                        sizes: '640x1136',
                        type: 'image/svg+xml',
                        form_factor: 'narrow',
                        label: 'Monoptic - Interface Mobile'
                    }
                ]
            },
            workbox: {
                globPatterns: ['**/*.{js,css,html,ico,png,svg}']
            }
        })
    ],
    // Configuration minimale pour éviter les conflits d'URL
    base: './',
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['vue', 'vue-router'],
                    charts: ['chart.js', 'vue-chartjs'],
                },
            },
        },
        chunkSizeWarningLimit: 1000,
    },
});
