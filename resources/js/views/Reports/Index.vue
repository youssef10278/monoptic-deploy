<template>
    <div class="space-y-6">
        <!-- En-tête -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">
                            Rapports & Analyses
                        </h1>
                        <p class="text-gray-600">
                            Tableau de bord des performances de votre magasin
                        </p>
                    </div>
                    <div class="flex space-x-2">
                        <button
                            @click="refreshAllData"
                            :disabled="isLoading"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                        >
                            <svg
                                class="w-4 h-4 mr-1"
                                :class="{ 'animate-spin': isLoading }"
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
                            Actualiser
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres de dates -->
        <DateFilter v-model="dateFilter" @change="onDateFilterChange" />

        <!-- KPI Cards Améliorés -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- CA du jour -->
            <EnhancedKPICard
                title="CA Aujourd'hui"
                :value="salesData?.summary?.today_revenue || 0"
                type="currency"
                icon="currency"
                color="green"
                :growth="salesData?.summary?.today_growth || null"
                subtitle="vs hier"
            />

            <!-- CA de la semaine -->
            <EnhancedKPICard
                title="CA Cette Semaine"
                :value="salesData?.summary?.week_revenue || 0"
                type="currency"
                icon="trending-up"
                color="blue"
                :growth="salesData?.summary?.week_growth || null"
                subtitle="vs semaine dernière"
            />

            <!-- CA du mois -->
            <EnhancedKPICard
                title="CA Ce Mois"
                :value="salesData?.summary?.month_revenue || 0"
                type="currency"
                icon="trending-up"
                color="purple"
                :growth="salesData?.summary?.month_growth || null"
                subtitle="vs mois dernier"
            />

            <!-- Panier moyen -->
            <EnhancedKPICard
                title="Panier Moyen"
                :value="salesData?.summary?.average_sale || 0"
                type="currency"
                icon="calculator"
                color="yellow"
                :growth="salesData?.summary?.average_sale_growth || null"
                subtitle="vs période précédente"
            />
        </div>

        <!-- Widgets principaux -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Widget Évolution des ventes -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3
                        class="text-lg leading-6 font-medium text-gray-900 mb-4"
                    >
                        Évolution des Ventes
                    </h3>
                    <div v-if="isLoadingSales" class="flex justify-center py-8">
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
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                        </svg>
                    </div>
                    <div
                        v-else-if="salesData?.evolution?.length > 0"
                        class="h-64"
                    >
                        <SalesChart
                            :data="salesData.evolution"
                            :period="selectedPeriod"
                        />
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        Aucune donnée de vente disponible
                    </div>
                </div>
            </div>

            <!-- Widget Valeur du stock -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3
                        class="text-lg leading-6 font-medium text-gray-900 mb-4"
                    >
                        Valeur du Stock
                    </h3>
                    <div v-if="isLoadingStock" class="flex justify-center py-8">
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
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                        </svg>
                    </div>
                    <div v-else class="space-y-4">
                        <div
                            class="flex justify-between items-center p-4 bg-blue-50 rounded-lg"
                        >
                            <div>
                                <div class="text-sm font-medium text-blue-900">
                                    Valeur d'achat
                                </div>
                                <div class="text-2xl font-bold text-blue-600">
                                    {{
                                        formatPrice(
                                            stockData?.purchase_value || 0
                                        )
                                    }}
                                </div>
                            </div>
                            <svg
                                class="h-8 w-8 text-blue-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 3H3m4 10v6a1 1 0 001 1h1m0 0h4m-5 0a1 1 0 001 1h1m0 0a1 1 0 001-1v-1a1 1 0 00-1-1H9a1 1 0 00-1 1v1z"
                                ></path>
                            </svg>
                        </div>

                        <div
                            class="flex justify-between items-center p-4 bg-green-50 rounded-lg"
                        >
                            <div>
                                <div class="text-sm font-medium text-green-900">
                                    Valeur de vente
                                </div>
                                <div class="text-2xl font-bold text-green-600">
                                    {{
                                        formatPrice(
                                            stockData?.selling_value || 0
                                        )
                                    }}
                                </div>
                            </div>
                            <svg
                                class="h-8 w-8 text-green-400"
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
                        </div>

                        <div
                            class="flex justify-between items-center p-4 bg-yellow-50 rounded-lg"
                        >
                            <div>
                                <div
                                    class="text-sm font-medium text-yellow-900"
                                >
                                    Marge potentielle
                                </div>
                                <div class="text-2xl font-bold text-yellow-600">
                                    {{
                                        formatPrice(
                                            stockData?.potential_profit || 0
                                        )
                                    }}
                                </div>
                            </div>
                            <svg
                                class="h-8 w-8 text-yellow-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                                ></path>
                            </svg>
                        </div>

                        <!-- Statistiques supplémentaires -->
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">
                                    {{ stockData?.total_products || 0 }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    Produits
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">
                                    {{ stockData?.total_quantity || 0 }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    Articles en stock
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="stockData?.low_stock_products > 0"
                            class="p-3 bg-red-50 rounded-lg"
                        >
                            <div class="flex items-center">
                                <svg
                                    class="h-5 w-5 text-red-400 mr-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
                                    ></path>
                                </svg>
                                <span class="text-sm text-red-800">
                                    {{ stockData.low_stock_products }}
                                    produit(s) en rupture de stock
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analyse des produits -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top 5 Ventes -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Top 5 - Produits les plus vendus
                        </h3>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg
                                class="h-4 w-4 mr-1"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                                ></path>
                            </svg>
                            Par quantité
                        </div>
                    </div>
                    <div
                        v-if="isLoadingProducts"
                        class="flex justify-center py-8"
                    >
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
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                        </svg>
                    </div>
                    <div
                        v-else-if="productData?.top_selling?.length > 0"
                        class="space-y-3"
                    >
                        <div
                            v-for="(product, index) in productData.top_selling"
                            :key="product.id"
                            class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-white rounded-lg border border-blue-100 hover:shadow-md transition-shadow duration-200"
                        >
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <span
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-full text-sm font-bold"
                                        :class="getTopSellingRankClass(index)"
                                    >
                                        {{ index + 1 }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <p
                                            class="text-sm font-medium text-gray-900 truncate"
                                        >
                                            {{ product.name }}
                                        </p>
                                        <span
                                            v-if="product.quantity <= 5"
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
                                        >
                                            Stock faible
                                        </span>
                                    </div>
                                    <div
                                        class="flex items-center space-x-4 mt-1"
                                    >
                                        <p class="text-xs text-gray-500">
                                            {{
                                                product.product_category
                                                    ?.name || "Sans catégorie"
                                            }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            Stock: {{ product.quantity }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-shrink-0 text-right">
                                <div
                                    class="text-lg font-semibold text-blue-600"
                                >
                                    {{ product.total_sold }}
                                </div>
                                <div class="text-xs text-gray-500">vendus</div>
                                <div class="text-xs text-gray-600 mt-1">
                                    {{ formatPrice(product.total_revenue) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        <svg
                            class="mx-auto h-12 w-12 text-gray-400"
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
                        <p class="mt-2 text-sm">Aucune vente enregistrée</p>
                    </div>
                </div>
            </div>

            <!-- Top 5 Rentabilité -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Top 5 - Produits les plus rentables
                        </h3>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg
                                class="h-4 w-4 mr-1"
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
                            Par marge
                        </div>
                    </div>
                    <div
                        v-if="isLoadingProducts"
                        class="flex justify-center py-8"
                    >
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
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                        </svg>
                    </div>
                    <div
                        v-else-if="productData?.top_profitable?.length > 0"
                        class="space-y-3"
                    >
                        <div
                            v-for="(
                                product, index
                            ) in productData.top_profitable"
                            :key="product.id"
                            class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-white rounded-lg border border-green-100 hover:shadow-md transition-shadow duration-200"
                        >
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <span
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-full text-sm font-bold"
                                        :class="
                                            getTopProfitableRankClass(index)
                                        "
                                    >
                                        {{ index + 1 }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <p
                                            class="text-sm font-medium text-gray-900 truncate"
                                        >
                                            {{ product.name }}
                                        </p>
                                        <span
                                            v-if="product.quantity <= 5"
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
                                        >
                                            Stock faible
                                        </span>
                                    </div>
                                    <div
                                        class="flex items-center space-x-4 mt-1"
                                    >
                                        <p class="text-xs text-gray-500">
                                            {{
                                                product.product_category
                                                    ?.name || "Sans catégorie"
                                            }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ product.total_sold }} vendus
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-shrink-0 text-right">
                                <div
                                    class="text-lg font-semibold text-green-600"
                                >
                                    {{ formatPrice(product.total_profit) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    profit total
                                </div>
                                <div class="text-xs text-gray-600 mt-1">
                                    {{
                                        formatPrice(
                                            product.average_profit_per_unit || 0
                                        )
                                    }}/unité
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        <svg
                            class="mx-auto h-12 w-12 text-gray-400"
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
                        <p class="mt-2 text-sm">Aucune donnée de rentabilité</p>
                        <p class="text-xs text-gray-400">
                            Vérifiez les prix d'achat des produits
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Top Clients -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Widget Top Clients -->
            <TopClientsWidget
                :clients="topClientsData?.top_clients || []"
                :is-loading="isLoadingTopClients"
            />

            <!-- Widget Alertes de Stock -->
            <StockAlertsWidget
                :low-stock-products="productData?.low_stock_products || []"
                :out-of-stock-products="
                    productData?.out_of_stock_products || []
                "
                :is-loading="isLoadingProducts"
            />
        </div>

        <!-- Messages d'erreur -->
        <div v-if="errorMessage" class="rounded-md bg-red-50 p-4">
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
                    <h3 class="text-sm font-medium text-red-800">
                        {{ errorMessage }}
                    </h3>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import SalesChart from "./SalesChart.vue";
import DateFilter from "../../Components/DateFilter.vue";
import EnhancedKPICard from "../../Components/Reports/EnhancedKPICard.vue";
import TopClientsWidget from "../../Components/Reports/TopClientsWidget.vue";
import StockAlertsWidget from "../../Components/Reports/StockAlertsWidget.vue";
import { formatPrice } from "../../utils/currency.js";

// État réactif
const salesData = ref(null);
const productData = ref(null);
const stockData = ref(null);
const topClientsData = ref(null);
const isLoading = ref(false);
const isLoadingSales = ref(false);
const isLoadingProducts = ref(false);
const isLoadingStock = ref(false);
const isLoadingTopClients = ref(false);
const errorMessage = ref("");

// Filtre de dates
const dateFilter = ref({
    period: "month",
    dateFrom: null,
    dateTo: null,
});

// Charger le rapport des ventes
const loadSalesReport = async () => {
    isLoadingSales.value = true;
    errorMessage.value = "";

    try {
        const params = {
            period: dateFilter.value.period,
        };

        // Ajouter les dates personnalisées si nécessaire
        if (dateFilter.value.period === "custom") {
            params.date_from = dateFilter.value.dateFrom;
            params.date_to = dateFilter.value.dateTo;
        }

        const response = await axios.get("/api/reports/sales", { params });

        if (response.data.success) {
            salesData.value = response.data.data;
        }
    } catch (error) {
        console.error(
            "Erreur lors du chargement du rapport des ventes:",
            error
        );
        if (error.response?.status === 403) {
            errorMessage.value =
                "Accès refusé. Vous devez être associé à un magasin pour voir les rapports.";
        } else {
            errorMessage.value =
                "Erreur lors du chargement du rapport des ventes";
        }
    } finally {
        isLoadingSales.value = false;
    }
};

// Charger l'analyse des produits
const loadProductAnalysis = async () => {
    isLoadingProducts.value = true;

    try {
        const response = await axios.get("/api/reports/product-analysis");

        if (response.data.success) {
            productData.value = response.data.data;
        }
    } catch (error) {
        console.error(
            "Erreur lors du chargement de l'analyse des produits:",
            error
        );
        if (error.response?.status === 403) {
            errorMessage.value =
                "Accès refusé. Vous devez être associé à un magasin pour voir les rapports.";
        } else {
            errorMessage.value =
                "Erreur lors du chargement de l'analyse des produits";
        }
    } finally {
        isLoadingProducts.value = false;
    }
};

// Charger la valeur du stock
const loadStockValue = async () => {
    isLoadingStock.value = true;

    try {
        const response = await axios.get("/api/reports/stock-value");

        if (response.data.success) {
            stockData.value = response.data.data;
        }
    } catch (error) {
        console.error(
            "Erreur lors du chargement de la valeur du stock:",
            error
        );
        if (error.response?.status === 403) {
            errorMessage.value =
                "Accès refusé. Vous devez être associé à un magasin pour voir les rapports.";
        } else {
            errorMessage.value =
                "Erreur lors du chargement de la valeur du stock";
        }
    } finally {
        isLoadingStock.value = false;
    }
};

// Charger l'analyse des top clients
const loadTopClientsAnalysis = async () => {
    isLoadingTopClients.value = true;
    errorMessage.value = "";

    try {
        const response = await axios.get("/api/reports/top-clients");

        if (response.data.success) {
            topClientsData.value = response.data.data;
        }
    } catch (error) {
        console.error(
            "Erreur lors du chargement de l'analyse des top clients:",
            error
        );
        if (error.response?.status === 403) {
            errorMessage.value =
                "Accès refusé. Vous devez être associé à un magasin pour voir les rapports.";
        } else {
            errorMessage.value =
                "Erreur lors du chargement de l'analyse des top clients";
        }
    } finally {
        isLoadingTopClients.value = false;
    }
};

// Gestionnaire de changement de filtre de dates
const onDateFilterChange = (filterData) => {
    dateFilter.value = filterData;
    // Recharger les données avec les nouveaux filtres
    loadSalesReport();
};

// Actualiser toutes les données
const refreshAllData = async () => {
    isLoading.value = true;
    await Promise.all([
        loadSalesReport(),
        loadProductAnalysis(),
        loadStockValue(),
        loadTopClientsAnalysis(),
    ]);
    isLoading.value = false;
};

// Méthodes pour les classes de rang
const getTopSellingRankClass = (index) => {
    const classes = [
        "bg-blue-100 text-blue-800 border-2 border-blue-300", // 1er
        "bg-indigo-100 text-indigo-800 border-2 border-indigo-300", // 2ème
        "bg-purple-100 text-purple-800 border-2 border-purple-300", // 3ème
        "bg-blue-100 text-blue-800", // 4ème
        "bg-gray-100 text-gray-800", // 5ème
    ];
    return classes[index] || "bg-gray-100 text-gray-800";
};

const getTopProfitableRankClass = (index) => {
    const classes = [
        "bg-green-100 text-green-800 border-2 border-green-300", // 1er
        "bg-emerald-100 text-emerald-800 border-2 border-emerald-300", // 2ème
        "bg-teal-100 text-teal-800 border-2 border-teal-300", // 3ème
        "bg-green-100 text-green-800", // 4ème
        "bg-gray-100 text-gray-800", // 5ème
    ];
    return classes[index] || "bg-gray-100 text-gray-800";
};

// La fonction formatPrice est maintenant importée depuis utils/currency.js

// Charger les données au montage du composant
onMounted(() => {
    refreshAllData();
});
</script>
