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
                <h1 class="text-white text-xl font-bold">Monopti Admin</h1>
            </div>

            <nav class="mt-8">
                <div class="px-4 space-y-2">
                    <!-- Lien principal du Super Admin -->
                    <router-link
                        to="/super-admin/dashboard"
                        class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200"
                        :class="{
                            'bg-blue-100 text-blue-700':
                                $route.name === 'SuperAdminDashboardIndex',
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
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                            ></path>
                        </svg>
                        Tableau de Bord
                    </router-link>

                    <!-- Séparateur -->
                    <div class="border-t border-gray-200 my-4"></div>

                    <!-- Informations Super Admin -->
                    <div class="px-4 py-3 bg-blue-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg
                                    class="w-8 h-8 text-blue-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                    ></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-blue-900">
                                    Super Administrateur
                                </p>
                                <p class="text-xs text-blue-700">
                                    Gestion de la plateforme
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques rapides -->
                    <div v-if="stats" class="px-4 py-3 bg-gray-50 rounded-lg">
                        <h4
                            class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2"
                        >
                            Statistiques
                        </h4>
                        <div class="space-y-1">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tenants</span>
                                <span class="font-medium text-gray-900">{{
                                    stats.total_tenants || 0
                                }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Actifs</span>
                                <span class="font-medium text-green-600">{{
                                    stats.active_tenants || 0
                                }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">MRR</span>
                                <span class="font-medium text-blue-600">{{
                                    formatPrice(stats.mrr || 0)
                                }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Mobile menu button -->
        <div class="lg:hidden">
            <button
                @click="toggleSidebar"
                class="fixed top-4 left-4 z-50 p-2 rounded-md bg-blue-600 text-white shadow-lg"
            >
                <svg
                    class="w-6 h-6"
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

        <!-- Main content -->
        <div class="lg:ml-64">
            <!-- Top navigation -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <div class="flex items-center">
                            <button
                                @click="toggleSidebar"
                                class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100"
                            >
                                <svg
                                    class="w-6 h-6"
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
                            <h1
                                class="ml-4 text-xl font-semibold text-gray-900"
                            >
                                Administration
                            </h1>
                        </div>

                        <!-- User menu -->
                        <div class="flex items-center space-x-4">
                            <!-- Notifications -->
                            <button
                                class="p-2 text-gray-400 hover:text-gray-500 relative"
                            >
                                <svg
                                    class="w-6 h-6"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 17h5l-5 5v-5zM10.5 3.5a6 6 0 0 1 6 6v2l1.5 1.5v1h-13v-1L6.5 11.5v-2a6 6 0 0 1 6-6z"
                                    ></path>
                                </svg>
                            </button>

                            <!-- User dropdown -->
                            <div class="relative">
                                <button
                                    @click="toggleUserMenu"
                                    class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                >
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center"
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
                                                Super Admin
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
                                    class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
                                    @click.away="userMenuOpen = false"
                                >
                                    <a
                                        href="#"
                                        @click.prevent="openChangePasswordModal"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    >
                                        Changer le mot de passe
                                    </a>
                                    <div class="border-t border-gray-100"></div>
                                    <button
                                        @click="handleLogout"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    >
                                        Déconnexion
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <router-view />
                </div>
            </main>
        </div>

        <!-- Mobile sidebar overlay -->
        <div
            v-show="sidebarOpen"
            class="fixed inset-0 z-40 lg:hidden"
            @click="sidebarOpen = false"
        >
            <div class="absolute inset-0 bg-gray-600 opacity-75"></div>
        </div>

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
const stats = ref(null);
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
    if (!user.value?.name) return "SA";
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

// Fonctions
const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

const toggleUserMenu = () => {
    userMenuOpen.value = !userMenuOpen.value;
};

const formatPrice = (price) => {
    if (!price) return "0 MAD";
    return new Intl.NumberFormat("fr-MA", {
        style: "currency",
        currency: "MAD",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    })
        .format(price)
        .replace("€", "MAD");
};

// Charger les informations utilisateur
const loadUserInfo = async () => {
    try {
        const response = await axios.get("/api/user");
        if (response.data.success) {
            user.value = response.data.data.user;
        }
    } catch (error) {
        console.error(
            "Erreur lors du chargement des informations utilisateur:",
            error
        );
    }
};

// Charger les statistiques rapides
const loadStats = async () => {
    try {
        const response = await axios.get("/api/super-admin/dashboard");
        if (response.data.success) {
            stats.value = response.data.data.kpis;
        }
    } catch (error) {
        console.error("Erreur lors du chargement des statistiques:", error);
    }
};

// Gérer la déconnexion
const handleLogout = async () => {
    try {
        await axios.post("/api/logout");
        localStorage.removeItem("auth_token");
        localStorage.removeItem("user_data");
        router.push("/login");
    } catch (error) {
        console.error("Erreur lors de la déconnexion:", error);
        // Forcer la déconnexion même en cas d'erreur
        localStorage.removeItem("auth_token");
        localStorage.removeItem("user_data");
        router.push("/login");
    }
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

// Charger les données au montage
onMounted(() => {
    loadUserInfo();
    loadStats();
});
</script>
