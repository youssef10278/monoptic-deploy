<template>
    <!-- Modal overlay -->
    <div
        v-if="show"
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
                @click="closeModal"
            ></div>

            <!-- Modal panel -->
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full"
            >
                <form @submit.prevent="submitForm">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="w-full">
                                <h3
                                    class="text-lg leading-6 font-medium text-gray-900 mb-4"
                                    id="modal-title"
                                >
                                    {{
                                        isEditing
                                            ? "Modifier le produit"
                                            : "Nouveau produit"
                                    }}
                                </h3>

                                <div
                                    class="grid grid-cols-1 gap-4 sm:grid-cols-2"
                                >
                                    <!-- Nom -->
                                    <div class="sm:col-span-2">
                                        <label
                                            for="name"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Nom du produit
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            id="name"
                                            v-model="form.name"
                                            type="text"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            placeholder="Ex: Monture Ray-Ban RB3025"
                                        />
                                    </div>

                                    <!-- Marque -->
                                    <div>
                                        <label
                                            for="brand"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Marque
                                        </label>
                                        <input
                                            id="brand"
                                            v-model="form.brand"
                                            type="text"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            placeholder="Ex: Ray-Ban, Oakley..."
                                        />
                                    </div>

                                    <!-- Référence -->
                                    <div>
                                        <label
                                            for="reference"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Référence
                                        </label>
                                        <input
                                            id="reference"
                                            v-model="form.reference"
                                            type="text"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            placeholder="REF-001"
                                        />
                                    </div>

                                    <!-- Code-barres -->
                                    <div>
                                        <label
                                            for="barcode"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Code-barres
                                        </label>
                                        <input
                                            id="barcode"
                                            v-model="form.barcode"
                                            type="text"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            placeholder="1234567890123"
                                        />
                                    </div>

                                    <!-- Catégorie -->
                                    <div class="sm:col-span-2">
                                        <label
                                            for="product_category_id"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Catégorie
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            id="product_category_id"
                                            v-model="form.product_category_id"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        >
                                            <option value="">
                                                Sélectionnez une catégorie
                                            </option>
                                            <option
                                                v-for="category in categories"
                                                :key="category.id"
                                                :value="category.id"
                                            >
                                                {{ category.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Prix d'achat -->
                                    <div>
                                        <label
                                            for="purchase_price"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Prix d'achat (MAD)
                                        </label>
                                        <input
                                            id="purchase_price"
                                            v-model="form.purchase_price"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            placeholder="0.00"
                                        />
                                    </div>

                                    <!-- Prix de vente -->
                                    <div>
                                        <label
                                            for="selling_price"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Prix de vente (MAD)
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            id="selling_price"
                                            v-model="form.selling_price"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            placeholder="0.00"
                                        />
                                    </div>

                                    <!-- Quantité -->
                                    <div class="sm:col-span-2">
                                        <label
                                            for="quantity"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Quantité en stock
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            id="quantity"
                                            v-model="form.quantity"
                                            type="number"
                                            min="0"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            placeholder="0"
                                        />
                                    </div>
                                </div>

                                <!-- Messages d'erreur -->
                                <div
                                    v-if="errorMessage"
                                    class="mt-4 rounded-md bg-red-50 p-4"
                                >
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg
                                                class="h-5 w-5 text-red-400"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3
                                                class="text-sm font-medium text-red-800"
                                            >
                                                {{ errorMessage }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div
                        class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"
                    >
                        <button
                            type="submit"
                            :disabled="isLoading"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="isLoading" class="flex items-center">
                                <svg
                                    class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
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
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>
                                {{
                                    isEditing
                                        ? "Modification..."
                                        : "Création..."
                                }}
                            </span>
                            <span v-else>
                                {{ isEditing ? "Modifier" : "Créer" }}
                            </span>
                        </button>
                        <button
                            type="button"
                            @click="closeModal"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import axios from "axios";

// Props
const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    product: {
        type: Object,
        default: null,
    },
    categories: {
        type: Array,
        default: () => [],
    },
    selectedCategoryId: {
        type: Number,
        default: null,
    },
});

// Emits
const emit = defineEmits(["close", "success"]);

// État réactif
const isLoading = ref(false);
const errorMessage = ref("");

// Formulaire
const form = ref({
    name: "",
    brand: "",
    reference: "",
    purchase_price: "",
    selling_price: "",
    quantity: 0,
    barcode: "",
    product_category_id: "",
});

// Computed
const isEditing = computed(() => !!props.product);

// Réinitialiser le formulaire (déclarée AVANT le watcher)
const resetForm = () => {
    form.value = {
        name: "",
        brand: "",
        reference: "",
        purchase_price: "",
        selling_price: "",
        quantity: 0,
        barcode: "",
        product_category_id: props.selectedCategoryId || "",
    };
    errorMessage.value = "";
};

// Watcher pour remplir le formulaire lors de l'édition
watch(
    () => props.product,
    (newProduct) => {
        if (newProduct) {
            form.value = {
                name: newProduct.name || "",
                brand: newProduct.brand || "",
                reference: newProduct.reference || "",
                purchase_price: newProduct.purchase_price || "",
                selling_price: newProduct.selling_price || "",
                quantity: newProduct.quantity || 0,
                barcode: newProduct.barcode || "",
                product_category_id:
                    newProduct.product_category_id ||
                    props.selectedCategoryId ||
                    "",
            };
        } else {
            resetForm();
        }
    },
    { immediate: true }
);

// Watcher pour la catégorie sélectionnée
watch(
    () => props.selectedCategoryId,
    (newCategoryId) => {
        if (newCategoryId && !props.product) {
            form.value.product_category_id = newCategoryId;
        }
    }
);

// Fermer le modal
const closeModal = () => {
    resetForm();
    emit("close");
};

// Soumettre le formulaire
const submitForm = async () => {
    if (isLoading.value) return;

    isLoading.value = true;
    errorMessage.value = "";

    try {
        // Préparer les données en convertissant les types
        const formData = {
            ...form.value,
            product_category_id:
                parseInt(form.value.product_category_id) || null,
            purchase_price: parseFloat(form.value.purchase_price) || null,
            selling_price: parseFloat(form.value.selling_price) || 0,
            quantity: parseInt(form.value.quantity) || 0,
        };

        let response;

        if (isEditing.value) {
            // Modification
            response = await axios.put(
                `/api/products/${props.product.id}`,
                formData
            );
        } else {
            // Création
            response = await axios.post("/api/products", formData);
        }

        if (response.data.success) {
            emit("success", {
                message: isEditing.value
                    ? "Produit modifié avec succès"
                    : "Produit créé avec succès",
                product: response.data.data,
            });
            closeModal();
        }
    } catch (error) {
        console.error("Erreur lors de la soumission:", error);

        if (error.response?.data?.errors) {
            // Erreurs de validation
            const errors = error.response.data.errors;
            const errorMessages = Object.values(errors).flat();
            errorMessage.value = errorMessages.join(", ");
        } else if (error.response?.data?.message) {
            errorMessage.value = error.response.data.message;
        } else {
            errorMessage.value = `Erreur lors de la ${
                isEditing.value ? "modification" : "création"
            } du produit`;
        }
    } finally {
        isLoading.value = false;
    }
};
</script>
