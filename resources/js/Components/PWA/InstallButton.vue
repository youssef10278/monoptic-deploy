<template>
    <div v-if="showInstallButton" class="fixed bottom-4 right-4 z-50">
        <button
            @click="installPWA"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-lg flex items-center space-x-2 transition-all duration-200 transform hover:scale-105"
        >
            <svg
                class="w-5 h-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                ></path>
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
            <svg
                class="w-5 h-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M5 13l4 4L19 7"
                ></path>
            </svg>
            <span>Application installée avec succès !</span>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

const showInstallButton = ref(false);
const showSuccessNotification = ref(false);
let deferredPrompt = null;

onMounted(() => {
    // Vérifier si l'app est déjà installée
    if (
        window.matchMedia &&
        window.matchMedia("(display-mode: standalone)").matches
    ) {
        showInstallButton.value = false;
        return;
    }

    // Écouter l'événement beforeinstallprompt
    window.addEventListener("beforeinstallprompt", (e) => {
        console.log("beforeinstallprompt event fired");
        e.preventDefault();
        deferredPrompt = e;
        showInstallButton.value = true;
    });

    // Écouter l'événement appinstalled
    window.addEventListener("appinstalled", () => {
        console.log("PWA installée avec succès");
        showInstallButton.value = false;
        showSuccessNotification.value = true;
        setTimeout(() => {
            showSuccessNotification.value = false;
        }, 3000);
    });

    // Afficher le bouton après un délai pour tester
    setTimeout(() => {
        if (
            !deferredPrompt &&
            !window.matchMedia("(display-mode: standalone)").matches
        ) {
            showInstallButton.value = true;
        }
    }, 2000);
});

const installPWA = async () => {
    if (deferredPrompt) {
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        console.log("User choice:", outcome);
        deferredPrompt = null;
        showInstallButton.value = false;
    } else {
        // Fallback : instructions manuelles
        alert(
            'Pour installer l\'application :\n1. Cliquez sur le menu du navigateur\n2. Sélectionnez "Installer Monoptic" ou "Ajouter à l\'écran d\'accueil"'
        );
    }
};
</script>
