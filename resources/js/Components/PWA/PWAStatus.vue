<template>
    <div class="pwa-status">
        <!-- Indicateur de statut de connexion -->
        <div
            v-if="!isOnline"
            class="fixed top-0 left-0 right-0 bg-yellow-500 text-white text-center py-2 z-50"
        >
            <div class="flex items-center justify-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <span>Mode hors ligne - Certaines fonctionnalités peuvent être limitées</span>
            </div>
        </div>

        <!-- Notification de mise à jour disponible -->
        <div
            v-if="updateAvailable"
            class="fixed bottom-4 left-4 bg-blue-600 text-white p-4 rounded-lg shadow-lg max-w-sm z-50"
        >
            <div class="flex items-start space-x-3">
                <svg class="w-6 h-6 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                <div class="flex-1">
                    <h4 class="font-medium">Mise à jour disponible</h4>
                    <p class="text-sm text-blue-100 mt-1">Une nouvelle version de l'application est prête.</p>
                    <div class="flex space-x-2 mt-3">
                        <button
                            @click="updateApp"
                            class="bg-white text-blue-600 px-3 py-1 rounded text-sm font-medium hover:bg-blue-50"
                        >
                            Mettre à jour
                        </button>
                        <button
                            @click="dismissUpdate"
                            class="text-blue-100 px-3 py-1 rounded text-sm hover:text-white"
                        >
                            Plus tard
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const isOnline = ref(navigator.onLine);
const updateAvailable = ref(false);
let updateSW = null;

onMounted(() => {
    // Écouter les changements de statut de connexion
    window.addEventListener('online', () => {
        isOnline.value = true;
        console.log('Connexion rétablie');
    });

    window.addEventListener('offline', () => {
        isOnline.value = false;
        console.log('Connexion perdue - Mode hors ligne activé');
    });

    // Écouter les mises à jour du service worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.addEventListener('message', (event) => {
            if (event.data && event.data.type === 'SKIP_WAITING') {
                updateAvailable.value = true;
            }
        });
    }

    // Intégration avec vite-plugin-pwa
    if (window.__PWA_UPDATE_AVAILABLE__) {
        updateAvailable.value = true;
        updateSW = window.__PWA_UPDATE_SW__;
    }
});

const updateApp = () => {
    if (updateSW) {
        updateSW(true);
    } else {
        // Fallback : recharger la page
        window.location.reload();
    }
    updateAvailable.value = false;
};

const dismissUpdate = () => {
    updateAvailable.value = false;
};
</script>

<style scoped>
.pwa-status {
    /* Styles pour les animations si nécessaire */
}
</style>
