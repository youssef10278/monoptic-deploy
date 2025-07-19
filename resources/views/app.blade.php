<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Monoptic') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/js/app.js'])

        <script>
            // DEBUG: Log toutes les URLs générées
            console.log('=== DEBUG URLS ===');
            console.log('Current URL:', window.location.href);
            console.log('Base URL:', window.location.origin);

            // Fix double protocol in asset URLs - VERSION AMÉLIORÉE
            document.addEventListener('DOMContentLoaded', function() {
                console.log('=== FIXING URLS ===');

                // Fix links
                const links = document.querySelectorAll('link[href]');
                links.forEach(link => {
                    const originalHref = link.href;
                    if (originalHref.includes('https//')) {
                        link.href = originalHref.replace(/https\/\//g, 'https://');
                        console.log('Fixed link:', originalHref, '->', link.href);
                    }
                });

                // Fix scripts
                const scripts = document.querySelectorAll('script[src]');
                scripts.forEach(script => {
                    const originalSrc = script.src;
                    if (originalSrc.includes('https//')) {
                        const newSrc = originalSrc.replace(/https\/\//g, 'https://');
                        console.log('Fixed script:', originalSrc, '->', newSrc);

                        const newScript = document.createElement('script');
                        newScript.src = newSrc;
                        newScript.type = script.type || 'text/javascript';
                        script.parentNode.replaceChild(newScript, script);
                    }
                });

                console.log('=== URL FIXING COMPLETE ===');
            });
        </script>
    </head>
    <body class="font-sans antialiased">
        <div id="app">
            <router-view></router-view>
        </div>
    </body>
</html>
