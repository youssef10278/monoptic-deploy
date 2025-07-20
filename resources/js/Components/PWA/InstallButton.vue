<template>
    <div v-if="showInstallButton" class="fixed bottom-4 right-4 z-50">
        <button
            @click="installPWA"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-lg flex items-center space-x-2 transition-all duration-200 transform hover:scale-105"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>Installer l'app</span>
        </button>
    </div>

    <!-- Notification d'installation réussie -->
    <div
        v-if="showSuccessNotification"
        class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg"
    >
        <div class="flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>Application installée avec succès !</span>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const showInstallButton = ref(false);
const showSuccessNotification = ref(false);
let deferredPrompt = null;

onMounted(() => {
    // Écouter l'événement beforeinstallprompt
    window.addEventListener('beforeinstallprompt', (e) => {
        // Empêcher l'affichage automatique du prompt
        e.preventDefault();
        // Stocker l'événement pour l'utiliser plus tard
        deferredPrompt = e;
        // Afficher notre bouton d'installation personnalisé
        showInstallButton.value = true;
    });

    // Écouter l'événement appinstalled
    window.addEventListener('appinstalled', () => {
        console.log('PWA installée avec succès');
        showInstallButton.value = false;
        showSuccessNotification.value = true;
        
        // Masquer la notification après 3 secondes
        setTimeout(() => {
            showSuccessNotification.value = false;
        }, 3000);
    });

    // Vérifier si l'app est déjà installée
    if (window.matchMedia('(display-mode: standalone)').matches) {
        showInstallButton.value = false;
    }
});

const installPWA = async () => {
    if (!deferredPrompt) {
        return;
    }

    // Afficher le prompt d'installation
    deferredPrompt.prompt();

    // Attendre la réponse de l'utilisateur
    const { outcome } = await deferredPrompt.userChoice;
    
    if (outcome === 'accepted') {
        console.log('Utilisateur a accepté l\'installation');
    } else {
        console.log('Utilisateur a refusé l\'installation');
    }

    // Réinitialiser deferredPrompt
    deferredPrompt = null;
    showInstallButton.value = false;
};
</script>
