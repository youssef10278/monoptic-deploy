<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <div
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out"
            :class="
                sidebarOpen
                    ? 'translate-x-0'
                    : '-translate-x-full lg:translate-x-0'
            "
        >
            <div class="flex items-center justify-center h-16 bg-blue-600">
                <h1 class="text-white text-xl font-bold">Monoptic</h1>
            </div>

            <nav class="mt-8">
                <div class="px-4 space-y-1">
                    <!-- Tableau de bord -->
                    <router-link
                        to="/dashboard"
                        class="flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200"
                        :class="{
                            'bg-blue-100 text-blue-700':
                                $route.name === 'DashboardHome',
                        }"
                    >
                        <svg
                            class="w-5 h-5 mr-3"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"
                            ></path>
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"
                            ></path>
                        </svg>
                        Tableau de bord
                    </router-link>

                    <!-- Séparateur -->
                    <div class="py-2">
                        <div class="border-t border-gray-200"></div>
                    </div>

                    <!-- Lien POS proéminent -->
                    <router-link
                        to="/pos"
                        class="flex items-center px-4 py-3 text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors duration-200 font-medium shadow-md mb-3"
                        :class="{ 'bg-green-700': $route.name === 'POSIndex' }"
                    >
                        <svg
                            class="w-5 h-5 mr-3"
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
                        Point de Vente
                    </router-link>

                    <!-- Section Gestion -->
                    <div class="space-y-1">
                        <router-link
                            to="/clients"
                            class="flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200"
                            :class="{
                                'bg-blue-100 text-blue-700':
                                    $route.name === 'ClientsIndex',
                            }"
                        >
                            <svg
                                class="w-5 h-5 mr-3"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"
                                ></path>
                            </svg>
                            Clients
                        </router-link>

                        <router-link
                            to="/stock"
                            class="flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200"
                            :class="{
                                'bg-blue-100 text-blue-700':
                                    $route.name === 'StockIndex',
                            }"
                        >
                            <svg
                                class="w-5 h-5 mr-3"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                ></path>
                            </svg>
                            Stock
                        </router-link>
                    </div>

                    <!-- Séparateur -->
                    <div class="py-2">
                        <div class="border-t border-gray-200"></div>
                    </div>

                    <!-- Section Ventes & Finances -->
                    <div class="space-y-1">
                        <router-link
                            to="/sales"
                            class="flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200"
                            :class="{
                                'bg-blue-100 text-blue-700':
                                    $route.name === 'SalesIndex',
                            }"
                        >
                            <svg
                                class="w-5 h-5 mr-3"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                ></path>
                            </svg>
                            Historique des Ventes
                        </router-link>

                        <router-link
                            to="/credits"
                            class="flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200"
                            :class="{
                                'bg-blue-100 text-blue-700':
                                    $route.name === 'CreditsIndex',
                            }"
                        >
                            <svg
                                class="w-5 h-5 mr-3"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"
                                ></path>
                            </svg>
                            Gestion des Crédits
                        </router-link>
                    </div>

                    <!-- Séparateur -->
                    <div class="py-2">
                        <div class="border-t border-gray-200"></div>
                    </div>

                    <!-- Section Rapports -->
                    <div class="space-y-1">
                        <router-link
                            to="/reports"
                            class="flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200"
                            :class="{
                                'bg-blue-100 text-blue-700':
                                    $route.name === 'ReportsIndex',
                            }"
                        >
                            <svg
                                class="w-5 h-5 mr-3"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                ></path>
                            </svg>
                            Rapports & Analyses
                        </router-link>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Main content -->
        <div class="lg:ml-64">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-4 py-4">
                    <div class="flex items-center">
                        <button
                            @click="sidebarOpen = !sidebarOpen"
                            class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
                        >
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                ></path>
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Boutons POS (affichés seulement sur la page POS) -->
                        <div
                            v-if="$route.name === 'POSIndex'"
                            class="flex items-center space-x-3"
                        >
                            <button
                                @click="resetPOSSale"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 transition-all duration-200"
                            >
                                <svg
                                    class="w-4 h-4 mr-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                    ></path>
                                </svg>
                                Nouvelle Vente
                            </button>
                            <router-link
                                to="/sales"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 hover:shadow-md transition-all duration-200"
                            >
                                <svg
                                    class="w-4 h-4 mr-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                    ></path>
                                </svg>
                                Historique
                            </router-link>
                        </div>

                        <!-- User menu -->
                        <div class="relative">
                            <button
                                @click="userMenuOpen = !userMenuOpen"
                                class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center"
                                    >
                                        <span
                                            class="text-white text-sm font-medium"
                                            >{{ userInitials }}</span
                                        >
                                    </div>
                                    <div class="hidden md:block text-left">
                                        <p
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{ user?.name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ user?.role }}
                                        </p>
                                    </div>
                                    <svg
                                        class="w-4 h-4 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 9l-7 7-7-7"
                                        ></path>
                                    </svg>
                                </div>
                            </button>

                            <!-- Dropdown menu -->
                            <div
                                v-show="userMenuOpen"
                                @click.away="userMenuOpen = false"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
                            >
                                <router-link
                                    to="/settings/optician"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    >Configuration Optique</router-link
                                >
                                <a
                                    href="#"
                                    @click.prevent="openChangePasswordModal"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    >Changer le mot de passe</a
                                >
                                <hr class="my-1" />
                                <button
                                    @click="handleLogout"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Se déconnecter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="p-6">
                <router-view />
            </main>
        </div>

        <!-- Overlay for mobile sidebar -->
        <div
            v-show="sidebarOpen"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
        ></div>

        <!-- Modal de changement de mot de passe -->
        <div
            v-if="showChangePasswordModal"
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div
                class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
            >
                <!-- Background overlay -->
                <div
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    @click="closeChangePasswordModal"
                ></div>

                <!-- Modal panel -->
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                >
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mt-3 text-center sm:mt-0 sm:text-left w-full"
                            >
                                <h3
                                    class="text-lg leading-6 font-medium text-gray-900 mb-4"
                                    id="modal-title"
                                >
                                    Changer le mot de passe
                                </h3>

                                <!-- Formulaire de changement de mot de passe -->
                                <form
                                    @submit.prevent="changePassword"
                                    class="space-y-4"
                                >
                                    <div>
                                        <label
                                            for="current_password"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Mot de passe actuel
                                        </label>
                                        <input
                                            type="password"
                                            id="current_password"
                                            v-model="
                                                passwordForm.current_password
                                            "
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                            required
                                        />
                                        <p
                                            v-if="
                                                passwordForm.errors
                                                    .current_password
                                            "
                                            class="mt-1 text-sm text-red-600"
                                        >
                                            {{
                                                passwordForm.errors
                                                    .current_password
                                            }}
                                        </p>
                                    </div>

                                    <div>
                                        <label
                                            for="new_password"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Nouveau mot de passe
                                        </label>
                                        <input
                                            type="password"
                                            id="new_password"
                                            v-model="passwordForm.password"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                            required
                                        />
                                        <p
                                            v-if="passwordForm.errors.password"
                                            class="mt-1 text-sm text-red-600"
                                        >
                                            {{ passwordForm.errors.password }}
                                        </p>
                                    </div>

                                    <div>
                                        <label
                                            for="password_confirmation"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Confirmer le nouveau mot de passe
                                        </label>
                                        <input
                                            type="password"
                                            id="password_confirmation"
                                            v-model="
                                                passwordForm.password_confirmation
                                            "
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                            required
                                        />
                                        <p
                                            v-if="
                                                passwordForm.errors
                                                    .password_confirmation
                                            "
                                            class="mt-1 text-sm text-red-600"
                                        >
                                            {{
                                                passwordForm.errors
                                                    .password_confirmation
                                            }}
                                        </p>
                                    </div>

                                    <!-- Message de succès/erreur -->
                                    <div
                                        v-if="passwordMessage"
                                        class="p-3 rounded-md"
                                        :class="passwordMessageClass"
                                    >
                                        {{ passwordMessage }}
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div
                        class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"
                    >
                        <button
                            type="submit"
                            @click="changePassword"
                            :disabled="passwordForm.processing"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                        >
                            <span v-if="passwordForm.processing"
                                >Changement...</span
                            >
                            <span v-else>Changer le mot de passe</span>
                        </button>
                        <button
                            type="button"
                            @click="closeChangePasswordModal"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();

// État réactif
const sidebarOpen = ref(false);
const userMenuOpen = ref(false);
const user = ref(null);
const showChangePasswordModal = ref(false);
const passwordMessage = ref("");
const passwordForm = ref({
    current_password: "",
    password: "",
    password_confirmation: "",
    processing: false,
    errors: {},
});

// Computed properties
const userInitials = computed(() => {
    if (!user.value?.name) return "U";
    return user.value.name
        .split(" ")
        .map((word) => word.charAt(0))
        .join("")
        .toUpperCase()
        .slice(0, 2);
});

const passwordMessageClass = computed(() => ({
    "bg-green-100 text-green-800 border border-green-200":
        passwordMessage.value.includes("succès"),
    "bg-red-100 text-red-800 border border-red-200":
        passwordMessage.value.includes("erreur") ||
        passwordMessage.value.includes("Erreur"),
    "bg-blue-100 text-blue-800 border border-blue-200":
        !passwordMessage.value.includes("succès") &&
        !passwordMessage.value.includes("erreur") &&
        !passwordMessage.value.includes("Erreur"),
}));

// Charger les données utilisateur au montage
onMounted(() => {
    const userData = localStorage.getItem("user_data");
    if (userData) {
        user.value = JSON.parse(userData);
    }
});

// Fonction de déconnexion
const handleLogout = async () => {
    try {
        // Fermer le menu utilisateur
        userMenuOpen.value = false;

        // Appeler l'API de déconnexion
        await axios.post("/api/logout");

        // Supprimer les données locales
        localStorage.removeItem("auth_token");
        localStorage.removeItem("user_data");

        // Rediriger vers la page de connexion
        router.push("/login");
    } catch (error) {
        console.error("Erreur lors de la déconnexion:", error);

        // Même en cas d'erreur, on supprime les données locales et on redirige
        localStorage.removeItem("auth_token");
        localStorage.removeItem("user_data");
        router.push("/login");
    }
};
// Méthode pour réinitialiser la vente POS
const resetPOSSale = () => {
    // Émettre un événement global pour réinitialiser le POS
    window.dispatchEvent(new CustomEvent("reset-pos-sale"));
};

// Fonctions pour le modal de changement de mot de passe
const openChangePasswordModal = () => {
    showChangePasswordModal.value = true;
    userMenuOpen.value = false;
    // Réinitialiser le formulaire
    passwordForm.value = {
        current_password: "",
        password: "",
        password_confirmation: "",
        processing: false,
        errors: {},
    };
    passwordMessage.value = "";
};

const closeChangePasswordModal = () => {
    showChangePasswordModal.value = false;
    passwordForm.value = {
        current_password: "",
        password: "",
        password_confirmation: "",
        processing: false,
        errors: {},
    };
    passwordMessage.value = "";
};

const changePassword = async () => {
    if (passwordForm.value.processing) return;

    // Validation côté client
    passwordForm.value.errors = {};

    if (!passwordForm.value.current_password) {
        passwordForm.value.errors.current_password =
            "Le mot de passe actuel est requis";
        return;
    }

    if (!passwordForm.value.password) {
        passwordForm.value.errors.password =
            "Le nouveau mot de passe est requis";
        return;
    }

    if (passwordForm.value.password.length < 8) {
        passwordForm.value.errors.password =
            "Le mot de passe doit contenir au moins 8 caractères";
        return;
    }

    if (
        passwordForm.value.password !== passwordForm.value.password_confirmation
    ) {
        passwordForm.value.errors.password_confirmation =
            "Les mots de passe ne correspondent pas";
        return;
    }

    passwordForm.value.processing = true;
    passwordMessage.value = "";

    try {
        const response = await axios.put("/api/user/password", {
            current_password: passwordForm.value.current_password,
            password: passwordForm.value.password,
            password_confirmation: passwordForm.value.password_confirmation,
        });

        passwordMessage.value = "Mot de passe changé avec succès !";

        // Fermer le modal après 2 secondes
        setTimeout(() => {
            closeChangePasswordModal();
        }, 2000);
    } catch (error) {
        console.error("Erreur changement mot de passe:", error);

        if (error.response?.data?.errors) {
            passwordForm.value.errors = error.response.data.errors;
        } else if (error.response?.data?.message) {
            passwordMessage.value = "Erreur: " + error.response.data.message;
        } else {
            passwordMessage.value = "Erreur lors du changement de mot de passe";
        }
    } finally {
        passwordForm.value.processing = false;
    }
};
</script>
