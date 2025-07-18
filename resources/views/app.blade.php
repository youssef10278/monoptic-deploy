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
            // Fix double protocol in asset URLs
            document.addEventListener('DOMContentLoaded', function() {
                const links = document.querySelectorAll('link[href*="https//"]');
                links.forEach(link => {
                    link.href = link.href.replace('https//monoptic-deploy-production.up.railway.app', 'https://monoptic-deploy-production.up.railway.app');
                });

                const scripts = document.querySelectorAll('script[src*="https//"]');
                scripts.forEach(script => {
                    const newSrc = script.src.replace('https//monoptic-deploy-production.up.railway.app', 'https://monoptic-deploy-production.up.railway.app');
                    if (newSrc !== script.src) {
                        const newScript = document.createElement('script');
                        newScript.src = newSrc;
                        newScript.type = script.type;
                        script.parentNode.replaceChild(newScript, script);
                    }
                });
            });
        </script>
    </head>
    <body class="font-sans antialiased">
        <div id="app">
            <router-view></router-view>
        </div>
    </body>
</html>
