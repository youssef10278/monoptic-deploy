<template>
    <div class="min-h-screen bg-gray-50">
        <!-- En-tête des produits -->
        <div class="bg-white shadow-sm border-b border-gray-200 p-4 md:p-6">
            <div
                class="flex flex-col md:flex-row md:justify-between md:items-center space-y-4 md:space-y-0"
            >
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-900">
                        Gestion du Stock
                    </h1>
                    <p class="text-gray-600 mt-1 text-sm md:text-base">
                        Gérez votre stock directement -
                        {{ products.length }} produit(s)
                    </p>
                </div>

                <!-- Boutons d'action -->
                <div
                    class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3"
                >
                    <!-- Bouton Ajouter une monture -->
                    <button
                        @click="openAddFrameModal"
                        :disabled="!frameCategory"
                        class="inline-flex items-center justify-center px-4 py-3 md:py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 w-full sm:w-auto disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg
                            class="w-5 h-5 mr-2"
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
                        Monture
                    </button>

                    <!-- Bouton Ajouter un accessoire -->
                    <button
                        @click="openAddAccessoryModal"
                        class="inline-flex items-center justify-center px-4 py-3 md:py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 w-full sm:w-auto"
                    >
                        <svg
                            class="w-5 h-5 mr-2"
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
                        Accessoire
                    </button>
                </div>
            </div>
        </div>

        <!-- Message de succès -->
        <div
            v-if="successMessage"
            class="mx-4 md:mx-6 mt-4 p-4 bg-green-50 border border-green-200 rounded-md"
        >
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg
                        class="h-5 w-5 text-green-400"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"
                        ></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ successMessage }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div
            class="mx-4 md:mx-6 mt-6 bg-white rounded-lg shadow-sm border border-gray-200"
        >
            <!-- Header des filtres avec bouton toggle sur mobile -->
            <div class="md:hidden p-4 border-b border-gray-200">
                <button
                    @click="showFilters = !showFilters"
                    class="flex items-center justify-between w-full text-left"
                >
                    <span class="text-sm font-medium text-gray-700"
                        >Filtres</span
                    >
                    <svg
                        :class="showFilters ? 'rotate-180' : ''"
                        class="w-4 h-4 text-gray-500 transition-transform"
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
                </button>
            </div>

            <!-- Contenu des filtres -->
            <div
                :class="showFilters || !isMobile ? 'block' : 'hidden'"
                class="p-4"
            >
                <div class="flex flex-col md:flex-row md:flex-wrap gap-4">
                    <!-- Toggle vue liste/grille sur mobile -->
                    <div
                        v-if="isMobile"
                        class="flex items-center space-x-2 md:hidden"
                    >
                        <span class="text-sm font-medium text-gray-700"
                            >Vue:</span
                        >
                        <button
                            @click="viewMode = 'grid'"
                            :class="
                                viewMode === 'grid'
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-gray-200 text-gray-700'
                            "
                            class="px-3 py-1 rounded text-xs font-medium transition-colors"
                        >
                            Grille
                        </button>
                        <button
                            @click="viewMode = 'list'"
                            :class="
                                viewMode === 'list'
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-gray-200 text-gray-700'
                            "
                            class="px-3 py-1 rounded text-xs font-medium transition-colors"
                        >
                            Liste
                        </button>
                    </div>
                    <!-- Filtre par catégorie -->
                    <div class="flex-1 md:min-w-48">
                        <label
                            for="category-filter"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Catégorie
                        </label>
                        <select
                            id="category-filter"
                            v-model="selectedCategoryId"
                            @change="filterProducts"
                            class="block w-full px-3 py-3 md:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base md:text-sm"
                        >
                            <option value="">Toutes les catégories</option>
                            <option
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.id"
                            >
                                {{ category.name }} ({{
                                    category.products_count || 0
                                }})
                            </option>
                        </select>
                    </div>

                    <!-- Filtre par nom -->
                    <div class="flex-1 md:min-w-48">
                        <label
                            for="search"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Rechercher
                        </label>
                        <input
                            id="search"
                            v-model="searchQuery"
                            @input="filterProducts"
                            type="text"
                            placeholder="Nom, marque, référence..."
                            class="block w-full px-3 py-3 md:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base md:text-sm"
                        />
                    </div>

                    <!-- Filtre par stock -->
                    <div class="flex-1 md:min-w-48">
                        <label
                            for="stock-filter"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Niveau de stock
                        </label>
                        <select
                            id="stock-filter"
                            v-model="stockFilter"
                            @change="filterProducts"
                            class="block w-full px-3 py-3 md:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base md:text-sm"
                        >
                            <option value="">Tous les niveaux</option>
                            <option value="in_stock">En stock</option>
                            <option value="low_stock">Stock faible</option>
                            <option value="out_of_stock">
                                Rupture de stock
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenu des produits -->
        <div class="mx-4 md:mx-6 mt-6 pb-6">
            <div v-if="isLoadingProducts" class="flex justify-center py-16">
                <svg
                    class="animate-spin h-8 w-8 text-blue-600"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    ></circle>
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 7 14 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    ></path>
                </svg>
            </div>

            <div
                v-else-if="filteredProducts.length === 0"
                class="text-center py-16"
            >
                <svg
                    class="mx-auto h-16 w-16 text-gray-400"
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
                <h3 class="mt-4 text-lg font-medium text-gray-900">
                    Aucun produit trouvé
                </h3>
                <p class="mt-2 text-gray-500">
                    {{
                        searchQuery || selectedCategoryId || stockFilter
                            ? "Aucun produit ne correspond à vos critères de recherche."
                            : "Commencez par ajouter des produits à votre stock."
                    }}
                </p>
            </div>

            <!-- Grille/Liste des produits -->
            <div
                v-else
                :class="
                    viewMode === 'list' && isMobile
                        ? 'space-y-3'
                        : 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6'
                "
            >
                <div
                    v-for="product in filteredProducts"
                    :key="product.id"
                    class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200"
                >
                    <div class="p-3 md:p-4">
                        <!-- En-tête du produit -->
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1 min-w-0">
                                <h3
                                    class="text-sm md:text-base font-semibold text-gray-900 truncate"
                                >
                                    {{ product.name }}
                                </h3>
                                <p
                                    v-if="product.brand"
                                    class="text-xs text-gray-500 mt-1"
                                >
                                    {{ product.brand }}
                                </p>
                            </div>
                            <div class="flex space-x-2 ml-2 flex-shrink-0">
                                <button
                                    @click="editProduct(product)"
                                    class="p-2 md:p-1 text-gray-400 hover:text-blue-600 rounded-md hover:bg-blue-50 transition-colors"
                                    title="Modifier"
                                >
                                    <svg
                                        class="w-5 h-5 md:w-4 md:h-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                        ></path>
                                    </svg>
                                </button>
                                <button
                                    @click="confirmDeleteProduct(product)"
                                    class="p-2 md:p-1 text-gray-400 hover:text-red-600 rounded-md hover:bg-red-50 transition-colors"
                                    title="Supprimer"
                                >
                                    <svg
                                        class="w-5 h-5 md:w-4 md:h-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                        ></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Informations du produit -->
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Prix:</span>
                                <span
                                    class="text-sm font-medium text-gray-900"
                                    >{{
                                        formatPrice(product.selling_price)
                                    }}</span
                                >
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500"
                                    >Stock:</span
                                >
                                <span
                                    class="text-sm font-medium"
                                    :class="
                                        getStockColorClass(product.quantity)
                                    "
                                >
                                    {{ product.quantity || 0 }}
                                </span>
                            </div>

                            <div
                                v-if="product.productCategory"
                                class="flex justify-between items-center"
                            >
                                <span class="text-xs text-gray-500"
                                    >Catégorie:</span
                                >
                                <span class="text-xs text-gray-700">{{
                                    product.productCategory.name
                                }}</span>
                            </div>

                            <!-- Badge de statut du stock -->
                            <div class="mt-3">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="
                                        getStockBadgeClass(product.quantity)
                                    "
                                >
                                    {{ getStockStatus(product.quantity) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals pour ajout direct au stock -->
        <AddFrameToStockModal
            :is-open="showAddFrameModal"
            :frame-category="frameCategory"
            @close="closeAddFrameModal"
            @success="handleAddToStockSuccess"
        />

        <AddAccessoryToStockModal
            :is-open="showAddAccessoryModal"
            :accessory-category="accessoryCategory"
            @close="closeAddAccessoryModal"
            @success="handleAddToStockSuccess"
        />

        <!-- Modal de formulaire de produit (existante) -->
        <ProductForm
            :show="showProductForm"
            :product="editingProduct"
            :categories="categories"
            :selected-category-id="selectedCategoryId"
            @close="closeProductForm"
            @success="handleProductSuccess"
        />

        <!-- Modal de formulaire de catégorie (existante) -->
        <CategoryForm
            :show="showCategoryForm"
            :category="editingCategory"
            @close="closeCategoryForm"
            @success="handleCategorySuccess"
        />

        <!-- Menu d'action flottant sur mobile -->
        <div v-if="isMobile" class="fixed bottom-6 right-6 z-50">
            <div class="relative">
                <!-- Bouton principal -->
                <button
                    @click="showFloatingMenu = !showFloatingMenu"
                    :class="showFloatingMenu ? 'rotate-45' : ''"
                    class="bg-blue-600 hover:bg-blue-700 text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg transition-all duration-300 transform hover:scale-110"
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
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                        ></path>
                    </svg>
                </button>

                <!-- Menu d'options -->
                <div
                    v-if="showFloatingMenu"
                    class="absolute bottom-16 right-0 flex flex-col space-y-3"
                >
                    <button
                        @click="
                            openAddFrameModal();
                            showFloatingMenu = false;
                        "
                        :disabled="!frameCategory"
                        class="bg-green-600 hover:bg-green-700 text-white rounded-full w-12 h-12 flex items-center justify-center shadow-lg transition-all duration-300 transform hover:scale-110 disabled:opacity-50 disabled:cursor-not-allowed"
                        :title="
                            frameCategory
                                ? 'Ajouter une monture'
                                : 'Créez d\'abord une catégorie Montures'
                        "
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
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                            ></path>
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                            ></path>
                        </svg>
                    </button>

                    <button
                        @click="
                            openAddAccessoryModal();
                            showFloatingMenu = false;
                        "
                        class="bg-purple-600 hover:bg-purple-700 text-white rounded-full w-12 h-12 flex items-center justify-center shadow-lg transition-all duration-300 transform hover:scale-110"
                        title="Ajouter un accessoire"
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
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                            ></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import axios from "axios";
import CategoryForm from "./CategoryForm.vue";
import ProductForm from "./ProductForm.vue";
import AddFrameToStockModal from "../../Components/Stock/AddFrameToStockModal.vue";
import AddAccessoryToStockModal from "../../Components/Stock/AddAccessoryToStockModal.vue";
import { formatPrice } from "../../utils/currency.js";

// État réactif
const categories = ref([]);
const products = ref([]);
const isLoadingCategories = ref(true);
const isLoadingProducts = ref(true);
const successMessage = ref("");

// Filtres
const selectedCategoryId = ref("");
const searchQuery = ref("");
const stockFilter = ref("");

// Modals
const showCategoryForm = ref(false);
const showProductForm = ref(false);
const editingCategory = ref(null);
const editingProduct = ref(null);

// État pour les nouvelles modals
const showAddFrameModal = ref(false);
const showAddAccessoryModal = ref(false);

// Computed properties
const filteredProducts = computed(() => {
    let filtered = products.value;

    // Filtre par catégorie
    if (selectedCategoryId.value) {
        filtered = filtered.filter(
            (product) =>
                product.product_category_id === selectedCategoryId.value
        );
    }

    // Filtre par recherche
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (product) =>
                product.name.toLowerCase().includes(query) ||
                (product.brand &&
                    product.brand.toLowerCase().includes(query)) ||
                (product.reference &&
                    product.reference.toLowerCase().includes(query))
        );
    }

    // Filtre par stock
    if (stockFilter.value) {
        filtered = filtered.filter((product) => {
            const quantity = product.quantity || 0;
            switch (stockFilter.value) {
                case "in_stock":
                    return quantity > 5;
                case "low_stock":
                    return quantity > 0 && quantity <= 5;
                case "out_of_stock":
                    return quantity === 0;
                default:
                    return true;
            }
        });
    }

    return filtered;
});

// Computed properties pour les catégories spécifiques
const frameCategory = computed(() => {
    return categories.value.find(
        (cat) =>
            cat.name.toLowerCase().includes("monture") ||
            cat.name.toLowerCase().includes("frame")
    );
});

const accessoryCategory = computed(() => {
    return (
        categories.value.find(
            (cat) =>
                cat.name.toLowerCase().includes("accessoire") ||
                cat.name.toLowerCase().includes("accessory")
        ) || categories.value[0]
    ); // Fallback sur la première catégorie
});

// Méthodes
const loadCategories = async () => {
    try {
        isLoadingCategories.value = true;
        const response = await axios.get("/api/product-categories");
        if (response.data.success) {
            categories.value = response.data.data;
        }
    } catch (error) {
        console.error("Erreur lors du chargement des catégories:", error);
    } finally {
        isLoadingCategories.value = false;
    }
};

const loadAllProducts = async () => {
    try {
        isLoadingProducts.value = true;
        const response = await axios.get("/api/products");
        if (response.data.success) {
            products.value = response.data.data;
        }
    } catch (error) {
        console.error("Erreur lors du chargement des produits:", error);
    } finally {
        isLoadingProducts.value = false;
    }
};

const filterProducts = () => {
    // La logique de filtrage est gérée par le computed filteredProducts
};

// Méthodes pour les catégories
const closeCategoryForm = () => {
    showCategoryForm.value = false;
    editingCategory.value = null;
};

const handleCategorySuccess = async (data) => {
    successMessage.value = data.message;
    await loadCategories();

    setTimeout(() => {
        successMessage.value = "";
    }, 3000);
};

// Méthodes pour les produits
const editProduct = (product) => {
    editingProduct.value = product;
    showProductForm.value = true;
};

const confirmDeleteProduct = async (product) => {
    if (
        confirm(
            `Êtes-vous sûr de vouloir supprimer le produit "${product.name}" ?`
        )
    ) {
        try {
            const response = await axios.delete(`/api/products/${product.id}`);
            if (response.data.success) {
                successMessage.value = "Produit supprimé avec succès";
                await loadAllProducts();
                await loadCategories();

                setTimeout(() => {
                    successMessage.value = "";
                }, 3000);
            }
        } catch (error) {
            console.error("Erreur lors de la suppression:", error);
            alert("Erreur lors de la suppression du produit");
        }
    }
};

const closeProductForm = () => {
    showProductForm.value = false;
    editingProduct.value = null;
};

const handleProductSuccess = async (data) => {
    successMessage.value = data.message;
    await loadAllProducts();
    await loadCategories();

    setTimeout(() => {
        successMessage.value = "";
    }, 3000);
};

// Méthodes pour les nouvelles modals
const openAddFrameModal = () => {
    showAddFrameModal.value = true;
};

const closeAddFrameModal = () => {
    showAddFrameModal.value = false;
};

const openAddAccessoryModal = () => {
    showAddAccessoryModal.value = true;
};

const closeAddAccessoryModal = () => {
    showAddAccessoryModal.value = false;
};

const handleAddToStockSuccess = async (data) => {
    successMessage.value = data.message;

    await loadCategories();
    await loadAllProducts();

    setTimeout(() => {
        successMessage.value = "";
    }, 3000);
};

// Méthodes utilitaires pour le stock
const getStockColorClass = (quantity) => {
    if (quantity === 0) return "text-red-600";
    if (quantity <= 5) return "text-yellow-600";
    return "text-green-600";
};

const getStockBadgeClass = (quantity) => {
    if (quantity === 0) return "bg-red-100 text-red-800";
    if (quantity <= 5) return "bg-yellow-100 text-yellow-800";
    return "bg-green-100 text-green-800";
};

const getStockStatus = (quantity) => {
    if (quantity === 0) return "Rupture";
    if (quantity <= 5) return "Stock faible";
    return "En stock";
};

// Mobile responsive
const showFilters = ref(false);
const showFloatingMenu = ref(false);
const viewMode = ref("grid"); // 'grid' ou 'list'
const isMobile = ref(false);

// Détecter si on est sur mobile
const checkMobile = () => {
    isMobile.value = window.innerWidth < 768;
};

// Initialisation
onMounted(async () => {
    await Promise.all([loadCategories(), loadAllProducts()]);

    // Détecter la taille d'écran
    checkMobile();
    window.addEventListener("resize", checkMobile);
});

// Nettoyage
onUnmounted(() => {
    window.removeEventListener("resize", checkMobile);
});
</script>

<style scoped>
/* Améliorations mobile pour la gestion de stock */
@media (max-width: 768px) {
    /* Améliorer la zone tactile des boutons */
    button {
        min-height: 44px;
    }

    /* Optimiser les inputs pour mobile */
    input[type="text"],
    select {
        font-size: 16px; /* Évite le zoom sur iOS */
    }

    /* Améliorer l'affichage des cards produits */
    .product-card {
        padding: 12px;
    }

    /* Optimiser les modals sur mobile */
    .modal-content {
        margin: 10px;
        max-height: calc(100vh - 20px);
    }
}

/* Animation pour l'accordéon des filtres */
.filter-toggle {
    transition: all 0.3s ease-in-out;
}

/* Améliorer la visibilité des badges de stock */
.stock-badge {
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 6px;
}

/* Hover states pour les boutons d'action */
.action-button {
    transition: all 0.2s ease-in-out;
}

.action-button:hover {
    transform: scale(1.05);
}
</style>
