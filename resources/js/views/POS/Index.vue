<template>
  <div class="min-h-screen flex flex-col bg-gray-50 pos-container">
    <!-- Navigation mobile par onglets -->
    <div class="md:hidden bg-white border-b border-gray-200 mobile-nav-sticky">
      <div class="flex">
        <button
          @click="mobileActiveTab = 'products'"
          :class="mobileActiveTab === 'products' ? 'bg-blue-50 text-blue-600 border-blue-600' : 'text-gray-500 border-transparent'"
          class="flex-1 py-3 px-4 text-sm font-medium border-b-2 transition-colors"
        >
          <svg class="w-4 h-4 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
          Produits
        </button>
        <button
          @click="mobileActiveTab = 'cart'"
          :class="mobileActiveTab === 'cart' ? 'bg-blue-50 text-blue-600 border-blue-600' : 'text-gray-500 border-transparent'"
          class="flex-1 py-3 px-4 text-sm font-medium border-b-2 transition-colors relative"
        >
          <svg class="w-4 h-4 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
          </svg>
          Panier
          <span v-if="cartItems.length > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
            {{ cartItems.length }}
          </span>
        </button>
      </div>
    </div>

    <!-- Interface principale responsive -->
    <div class="flex-1 flex p-3 md:p-6 gap-3 md:gap-6">
      <!-- Colonne de gauche - Client et Recherche de produits -->
      <div
        :class="mobileActiveTab === 'products' || !isMobile ? 'flex' : 'hidden'"
        class="flex-1 bg-white rounded-lg shadow-sm flex-col md:flex pos-scrollable pos-mobile-scroll"
      >
        <!-- Section Client intégrée -->
        <div class="p-3 md:p-4 border-b border-gray-200">
          <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 mb-4">
            <h2 class="text-sm font-medium text-gray-700 flex-shrink-0">Client</h2>
            <div class="flex-1 relative">
              <div class="relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input
                  v-model="clientSearchInput"
                  @input="searchClientsLive"
                  @focus="searchClientsLive"
                  @blur="hideSearchResults"
                  type="text"
                  placeholder="Rechercher ou ajouter un client"
                  class="w-full pl-10 pr-4 py-2.5 md:py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
              </div>
              <!-- Résultats de recherche -->
              <div v-if="clientSearchResults.length > 0 && clientSearchInput" class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                <div
                  v-for="client in clientSearchResults"
                  :key="client.id"
                  @click="selectClient(client)"
                  class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0"
                >
                  <div class="text-sm font-medium text-gray-900">{{ client.name }}</div>
                  <div class="text-xs text-gray-500">{{ client.phone }}</div>
                </div>
              </div>
            </div>

            <!-- Bouton ajouter client -->
            <button
              @click="showNewClientForm = true"
              class="w-10 h-10 md:w-8 md:h-8 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors duration-200 flex-shrink-0"
              title="Ajouter un nouveau client"
            >
              <svg class="w-5 h-5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
            </button>

            <!-- Client sélectionné ou Client de passage -->
            <div v-if="selectedClient" class="flex items-center text-sm bg-blue-50 px-3 py-2 rounded-lg">
              <span class="font-medium text-blue-900">{{ selectedClient.name }}</span>
              <button
                @click="selectedClient = null"
                class="ml-2 text-blue-400 hover:text-blue-600"
                title="Retirer le client"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
            <div v-else class="text-sm text-blue-600 font-medium bg-blue-50 px-3 py-2 rounded-lg">Client de passage</div>
          </div>
        </div>

        <!-- Section Recherche de Produits -->
        <div class="p-3 md:p-4 border-b border-gray-200">
          <h2 class="text-base font-medium text-gray-900 mb-3 md:mb-4">Recherche de Produits</h2>

          <!-- Barre de recherche principale -->
          <div class="relative mb-3 md:mb-4">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input
              v-model="productSearch"
              type="text"
              placeholder="Rechercher un produit..."
              class="w-full pl-10 pr-4 py-3 md:py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              @input="searchProducts"
              @keydown.enter="quickAddProduct"
            >
          </div>

          <!-- Boutons d'action -->
          <div class="grid grid-cols-2 md:flex md:space-x-3 gap-2 md:gap-0 mb-3 md:mb-4">
            <button
              @click="filterProducts('frame')"
              class="px-3 py-2.5 md:px-4 md:py-2 text-sm font-medium rounded-lg text-white bg-purple-600 hover:bg-purple-700 transition-colors duration-200"
            >
              Montures
            </button>
            <button
              @click="filterProducts('accessory')"
              class="px-3 py-2.5 md:px-4 md:py-2 text-sm font-medium rounded-lg text-white bg-orange-600 hover:bg-orange-700 transition-colors duration-200"
            >
              Accessoires
            </button>
            <button
              @click="showAddLenses = true"
              class="px-3 py-2.5 md:px-4 md:py-2 text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200"
            >
              + Verres
            </button>
            <button
              @click="showAddContactLenses = true"
              class="px-3 py-2.5 md:px-4 md:py-2 text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 transition-colors duration-200"
            >
              + Lentilles
            </button>
          </div>
        </div>

        <!-- Section Produits trouvés -->
        <div class="flex-1 overflow-hidden flex flex-col">
          <div class="p-3 md:p-4 border-b border-gray-200">
            <h3 class="text-sm font-medium text-gray-900">Produits trouvés</h3>
          </div>

          <div class="flex-1 pos-scrollable p-4">
            <div v-if="filteredProducts.length === 0" class="text-center text-gray-500 py-8">
              <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <p class="text-sm">Aucun produit trouvé</p>
            </div>

            <div v-else class="space-y-3">
              <div
                v-for="product in filteredProducts"
                :key="product.id"
                class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200"
              >
                <div class="flex items-center flex-1">
                  <!-- Pastille colorée selon le type -->
                  <div
                    :class="getProductColorClass(product)"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs font-medium mr-3 flex-shrink-0"
                  >
                    {{ getProductInitial(product) }}
                  </div>

                  <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-medium text-gray-900 truncate">{{ product.name }}</h4>
                    <p class="text-xs text-gray-500">
                      Réf: {{ product.reference || 'N/A' }} / Stock: {{ product.quantity }}
                    </p>
                  </div>
                </div>

                <div class="flex items-center space-x-3">
                  <span class="text-sm font-medium text-gray-900">{{ formatPrice(product.selling_price) }}</span>
                  <button
                    @click="quickAddProductToCart(product)"
                    class="px-3 py-1 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700 transition-colors duration-200"
                  >
                    Ajouter
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Colonne de droite - Panier -->
      <div
        :class="mobileActiveTab === 'cart' || !isMobile ? 'flex' : 'hidden'"
        class="w-full md:w-80 bg-white rounded-lg shadow-sm flex-col pos-scrollable pos-mobile-scroll"
      >
        <!-- En-tête du panier -->
        <div class="p-3 md:p-4 border-b border-gray-200">
          <div class="flex items-center">
            <svg class="w-4 h-4 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <h2 class="text-base font-medium text-gray-900">Panier ({{ cartItems.length }} articles)</h2>
          </div>
        </div>

        <!-- Articles du panier -->
        <div class="flex-1 flex flex-col min-h-0">

          <div class="flex-1 pos-scrollable p-3 md:p-4">
            <div v-if="cartItems.length === 0" class="flex flex-col items-center justify-center text-gray-500 py-8">
              <svg class="h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
              </svg>
              <p class="text-sm text-gray-500 font-medium">Panier vide</p>
              <p class="text-xs text-gray-400 mt-1">Ajoutez des articles pour commencer</p>
            </div>

            <div v-else class="space-y-2">
              <div
                v-for="(item, index) in cartItems"
                :key="index"
                class="flex items-start p-2.5 border-b border-gray-100 last:border-b-0 gap-2.5"
              >
                <!-- Pastille colorée selon le type -->
                <div
                  :class="getCartItemColorClass(item)"
                  class="w-6 h-6 rounded flex items-center justify-center text-white text-xs font-medium flex-shrink-0 mt-0.5"
                >
                  {{ getCartItemInitial(item) }}
                </div>

                <!-- Informations du produit avec largeur limitée -->
                <div class="flex-1 min-w-0 max-w-[140px]">
                  <h4
                    class="text-sm font-medium text-gray-900 leading-tight mb-1 cursor-help"
                    :title="item.description"
                  >
                    {{ item.description.length > 28 ? item.description.substring(0, 28) + '...' : item.description }}
                  </h4>
                  <p class="text-xs text-gray-500">{{ formatPrice(item.price) }}</p>
                </div>

                <!-- Contrôles à droite avec largeur fixe -->
                <div class="flex items-center space-x-1.5 flex-shrink-0">
                  <!-- Contrôles quantité -->
                  <button
                    @click="updateQuantity(index, item.quantity - 1)"
                    :disabled="item.quantity <= 1"
                    class="w-5 h-5 rounded border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-50 disabled:opacity-50"
                  >
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                  </button>
                  <span class="text-xs font-medium text-gray-900 min-w-[1rem] text-center">{{ item.quantity }}</span>
                  <button
                    @click="updateQuantity(index, item.quantity + 1)"
                    class="w-5 h-5 rounded border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-50"
                  >
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                  </button>

                  <!-- Bouton suppression -->
                  <button
                    @click="removeItem(index)"
                    class="w-5 h-5 text-red-400 hover:text-red-600 flex items-center justify-center ml-1"
                    title="Supprimer cet article"
                  >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Section Totaux et Paiement -->
        <div class="border-t border-gray-200 p-3 space-y-3 flex-shrink-0">
          <!-- Totaux -->
          <div class="border-t border-gray-200 pt-2">
            <div class="flex justify-between text-base font-bold">
              <span>Total</span>
              <span class="text-blue-600">{{ formatPrice(totalAmount) }}</span>
            </div>
          </div>

          <!-- Boutons de paiement -->
          <div class="space-y-2.5">
            <div class="grid grid-cols-2 gap-2">
              <button
                @click="selectPaymentMode('complete')"
                :class="paymentMode === 'complete' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                class="px-2.5 py-1.5 rounded text-sm font-medium transition-colors duration-200"
              >
                Paiement Complet
              </button>
              <button
                @click="selectPaymentMode('partial')"
                :class="paymentMode === 'partial' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                class="px-2.5 py-1.5 rounded text-sm font-medium transition-colors duration-200"
              >
                Paiement Partiel
              </button>
            </div>

            <!-- Interface Paiement Partiel -->
            <div v-if="paymentMode === 'partial'" class="space-y-3">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Montant d'avance</label>
                <div class="relative">
                  <input
                    v-model.number="advanceAmount"
                    type="number"
                    min="0"
                    :max="totalAmount"
                    step="0.01"
                    placeholder="0.00"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  >
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 text-sm">€</span>
                  </div>
                </div>
              </div>

              <!-- Détails du reste à payer -->
              <div class="bg-orange-50 rounded-lg p-3 border border-orange-200">
                <div class="space-y-1 text-sm">
                  <div class="flex justify-between">
                    <span class="text-orange-800">Total à payer:</span>
                    <span class="font-medium text-orange-900">{{ formatPrice(totalAmount) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-orange-800">Avance:</span>
                    <span class="font-medium text-orange-900">{{ formatPrice(advanceAmount || 0) }}</span>
                  </div>
                  <div class="border-t border-orange-200 pt-1">
                    <div class="flex justify-between">
                      <span class="font-medium text-orange-800">Reste à payer:</span>
                      <span class="font-bold text-orange-900">{{ formatPrice(Math.max(0, totalAmount - (advanceAmount || 0))) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Actions finales -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
              <button
                @click="saveDraft"
                :disabled="isSaving || cartItems.length === 0"
                class="px-3 py-3 md:px-2.5 md:py-1.5 border border-gray-300 text-gray-700 bg-white rounded text-sm font-medium hover:bg-gray-50 disabled:opacity-50 transition-colors duration-200"
              >
                Sauvegarder en Devis
              </button>
              <button
                @click="handlePayment"
                :disabled="isSaving || cartItems.length === 0 || !paymentMode"
                class="px-3 py-3 md:px-2.5 md:py-1.5 bg-green-600 text-white rounded text-sm font-medium hover:bg-green-700 disabled:opacity-50 transition-colors duration-200"
              >
                <span v-if="isSaving">Finalisation...</span>
                <span v-else-if="paymentMode === 'complete'">Valider Complet</span>
                <span v-else-if="paymentMode === 'partial'">Valider Partiel</span>
                <span v-else>Valider Paiement</span>
              </button>
            </div>
        </div>
      </div>
    </div>

    <!-- Bouton panier flottant sur mobile -->
    <div
      v-if="isMobile && mobileActiveTab === 'products' && cartItems.length > 0"
      class="fixed bottom-6 right-6 z-50"
    >
      <button
        @click="mobileActiveTab = 'cart'"
        class="bg-blue-600 hover:bg-blue-700 text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg transition-all duration-300 transform hover:scale-110"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
        </svg>
        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center font-bold cart-badge">
          {{ cartItems.length }}
        </span>
      </button>
    </div>
  </div>

  <!-- Modal de confirmation d'impression -->
  <PrintConfirmationModal
    :show="showPrintConfirmationModal"
    :operation-type="confirmationData.operationType"
    :operation-id="confirmationData.operationId"
    :client-name="confirmationData.clientName"
    :item-count="confirmationData.itemCount"
    :total-amount="confirmationData.totalAmount"
    :paid-amount="confirmationData.paidAmount"
    :remaining-amount="confirmationData.remainingAmount"
    :sale-data="confirmationData.saleData"
    @close="closePrintConfirmationModal"
    @confirm="handlePrintConfirmation"
  />

  <!-- Modal d'impression -->
  <PrintModal
    :show="showPrintModal"
    :type="printType"
    :data="printData"
    @close="closePrintModal"
    @printed="handlePrintSuccess"
  />

  <!-- Modals pour ajouter des articles -->
    <AddFrameModal
      v-if="showAddFrame"
      @close="showAddFrame = false"
      @add="addFrameToCart"
    />

    <AddLensesModal
      v-if="showAddLenses"
      @close="showAddLenses = false"
      @add="addLensesToCart"
    />

    <ContactLensSearchModal
      v-if="showAddContactLenses"
      @close="showAddContactLenses = false"
      @add="addContactLensesToCart"
    />

    <AddAccessoryModal
      v-if="showAddAccessory"
      @close="showAddAccessory = false"
      @add="addAccessoryToCart"
    />

    <!-- Modal nouveau client -->
    <NewClientModal
      v-if="showNewClientForm"
      @close="showNewClientForm = false"
      @created="handleClientCreated"
    />

    <!-- Modal de recherche de client -->
    <div v-if="showClientSearch" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showClientSearch = false"></div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="flex justify-between items-start mb-6">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                Rechercher un Client
              </h3>
              <button type="button" @click="showClientSearch = false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>

            <!-- Recherche de client -->
            <div class="mb-6">
              <div class="relative">
                <input
                  v-model="clientSearch"
                  type="text"
                  placeholder="Nom, téléphone ou email..."
                  class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                  @input="searchClients"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Liste des clients -->
            <div v-if="filteredClients.length > 0" class="mb-6">
              <h4 class="text-sm font-medium text-gray-700 mb-3">Clients trouvés</h4>
              <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-md">
                <div
                  v-for="client in filteredClients"
                  :key="client.id"
                  @click="selectClientAndContinue(client)"
                  class="p-3 border-b border-gray-200 cursor-pointer hover:bg-gray-50"
                  :class="{ 'bg-blue-50 border-blue-200': selectedClient?.id === client.id }"
                >
                  <div class="flex justify-between items-start">
                    <div class="flex-1">
                      <h5 class="text-sm font-medium text-gray-900">{{ client.name }}</h5>
                      <p class="text-sm text-gray-500">{{ client.phone }}</p>
                      <p class="text-sm text-gray-500">{{ client.email }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-else-if="clientSearch && clientSearch.length > 2" class="text-center py-8 text-gray-500">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <p class="mt-2">Aucun client trouvé</p>
              <p class="text-sm">Essayez avec d'autres termes de recherche</p>
            </div>
          </div>

          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              @click="showClientSearch = false"
              class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
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
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import axios from 'axios'
import PrintModal from '../../Components/POS/PrintModal.vue'
import PrintConfirmationModal from '../../Components/POS/PrintConfirmationModal.vue'
import AddFrameModal from '../../Components/POS/AddFrameModal.vue'
import AddLensesModal from '../../Components/POS/AddLensesModal.vue'
import ContactLensSearchModal from '../../Components/POS/ContactLensSearchModal.vue'
import AddAccessoryModal from '../../Components/POS/AddAccessoryModal.vue'
import NewClientModal from '../../Components/POS/NewClientModal.vue'
import { formatPrice } from '../../utils/currency.js'

// État réactif
const clientSearch = ref('')
const clientSearchInput = ref('')
const clientSearchResults = ref([])
const productSearch = ref('')
const clients = ref([])
const products = ref([])
const selectedClient = ref(null)
const cartItems = ref([])
const paidAmount = ref(0)
const remainingAmount = ref(0)
const isSaving = ref(false)
const currentFilter = ref('')
const paymentMode = ref('') // 'complete' ou 'partial'
const paymentMethod = ref('') // 'cash', 'check', 'card'
const advanceAmount = ref(0)

// Modals
const showAddFrame = ref(false)
const showAddLenses = ref(false)
const showAddContactLenses = ref(false)
const showAddAccessory = ref(false)
const showClientSearch = ref(false)
const showNewClientForm = ref(false)

// Impression
const showPrintConfirmationModal = ref(false)
const showPrintModal = ref(false)
const printType = ref('ticket') // 'ticket' ou 'devis'
const printData = ref({})
const lastSaleId = ref(null)
const confirmationData = ref({
  operationType: 'sale', // 'sale' ou 'quote'
  operationId: 'TEMP-' + Date.now(), // Valeur par défaut non-null
  clientName: '',
  itemCount: 0,
  totalAmount: 0,
  paidAmount: 0,
  remainingAmount: 0,
  saleData: null
})

// Computed
const totalAmount = computed(() => {
  return cartItems.value.reduce((total, item) => {
    return total + (item.price * item.quantity)
  }, 0)
})

const computedRemainingAmount = computed(() => {
  return Math.max(0, totalAmount.value - paidAmount.value)
})

const filteredClients = computed(() => {
  if (!clientSearch.value) return clients.value

  const search = clientSearch.value.toLowerCase()
  return clients.value.filter(client =>
    client.name.toLowerCase().includes(search) ||
    (client.phone && client.phone.includes(search)) ||
    (client.email && client.email.toLowerCase().includes(search))
  )
})

const filteredProducts = computed(() => {
  let filtered = products.value

  // Filtrer par type si un filtre est actif
  if (currentFilter.value) {
    filtered = filtered.filter(product => product.type === currentFilter.value)
  }

  // Filtrer par recherche textuelle
  if (productSearch.value) {
    const search = productSearch.value.toLowerCase()
    filtered = filtered.filter(product =>
      product.name.toLowerCase().includes(search) ||
      (product.brand && product.brand.toLowerCase().includes(search)) ||
      (product.reference && product.reference.toLowerCase().includes(search))
    )
  }

  return filtered.filter(product => product.quantity > 0) // Seulement les produits en stock
})

// Watcher pour mettre à jour le montant payé par défaut
watch(totalAmount, (newTotal, oldTotal) => {
  // Mettre à jour le montant payé seulement si :
  // 1. C'est le premier chargement (oldTotal undefined)
  // 2. Le panier était vide et on ajoute le premier article
  // 3. Le montant payé était égal à l'ancien total (paiement complet)
  if (oldTotal === undefined ||
      (oldTotal === 0 && newTotal > 0) ||
      (paidAmount.value === oldTotal && oldTotal > 0)) {
    paidAmount.value = newTotal
  }
}, { immediate: true })

// Méthodes
const loadClients = async () => {
  try {
    const response = await axios.get('/api/clients')
    if (response.data.success) {
      clients.value = response.data.data
    }
  } catch (error) {
    console.error('Erreur lors du chargement des clients:', error)
  }
}

const loadProducts = async () => {
  try {
    const response = await axios.get('/api/products')
    if (response.data.success) {
      products.value = response.data.data
    }
  } catch (error) {
    console.error('Erreur lors du chargement des produits:', error)
  }
}

const searchClients = () => {
  // La recherche est gérée par le computed filteredClients
}

// Recherche en temps réel des clients
const searchClientsLive = () => {
  if (!clientSearchInput.value || clientSearchInput.value.length < 2) {
    clientSearchResults.value = []
    return
  }

  const search = clientSearchInput.value.toLowerCase()
  clientSearchResults.value = clients.value.filter(client =>
    client.name.toLowerCase().includes(search) ||
    (client.phone && client.phone.includes(search)) ||
    (client.email && client.email.toLowerCase().includes(search))
  ).slice(0, 5) // Limiter à 5 résultats
}

// Sélectionner un client
const selectClient = (client) => {
  selectedClient.value = client
  clientSearchInput.value = ''
  clientSearchResults.value = []
}

// Masquer les résultats de recherche avec un délai pour permettre le clic
const hideSearchResults = () => {
  setTimeout(() => {
    clientSearchResults.value = []
  }, 200)
}

// Sélectionner le mode de paiement
const selectPaymentMode = (mode) => {
  paymentMode.value = mode
  paymentMethod.value = ''
  if (mode === 'complete') {
    advanceAmount.value = totalAmount.value
  } else if (mode === 'partial') {
    // Initialiser avec le montant total par défaut, l'utilisateur peut le modifier
    advanceAmount.value = totalAmount.value
  } else {
    advanceAmount.value = 0
  }
}

// Sélectionner la méthode de paiement
const selectPaymentMethod = (method) => {
  paymentMethod.value = method
}

// Finaliser la vente avec paiement partiel
const finalizeSalePartial = async () => {
  if (cartItems.value.length === 0) return

  isSaving.value = true

  try {
    const saleData = {
      client_id: selectedClient.value ? selectedClient.value.id : null,
      items: cartItems.value,
      paid_amount: advanceAmount.value || 0,
      payment_method: 'partial',
      total_amount: totalAmount.value
    }

    const response = await axios.post('/api/sales', saleData)

    if (response.data.success) {
      // Enregistrer l'ID de la vente pour l'impression
      lastSaleId.value = response.data.sale?.id || response.data.data?.id || Date.now()

      // Sauvegarder les données de la vente AVANT réinitialisation
      const saleData = {
        id: lastSaleId.value,
        date: new Date(),
        vendeur: 'Système', // À remplacer par l'utilisateur connecté
        client: selectedClient.value,
        items: cartItems.value.map(item => ({
          description: item.description,
          quantity: item.quantity,
          price: item.price,
          details: item.details
        })),
        paymentMethod: 'partial',
        paidAmount: advanceAmount.value || 0,
        remainingAmount: computedRemainingAmount.value,
        totalAmount: totalAmount.value
      }

      // Préparer les données pour le modal de confirmation
      confirmationData.value = {
        operationType: 'sale',
        operationId: lastSaleId.value,
        clientName: selectedClient.value?.name || '',
        itemCount: cartItems.value.length,
        totalAmount: totalAmount.value,
        paidAmount: advanceAmount.value || 0,
        remainingAmount: computedRemainingAmount.value,
        saleData: saleData // Ajouter les données complètes
      }

      // Réinitialiser le formulaire
      resetSale()

      // Afficher le modal de confirmation d'impression
      showPrintConfirmationModal.value = true
    } else {
      alert('Erreur lors de la création de la vente')
    }
  } catch (error) {
    console.error('Erreur lors de la création de la vente:', error)
    alert('Erreur lors de la création de la vente: ' + (error.message || 'Erreur inconnue'))
  } finally {
    isSaving.value = false
  }
}

const searchProducts = () => {
  // La recherche est gérée par le computed filteredProducts
}

const filterProducts = (type) => {
  currentFilter.value = currentFilter.value === type ? '' : type
  productSearch.value = ''
}

const quickAddProduct = () => {
  if (filteredProducts.value.length === 1) {
    quickAddProductToCart(filteredProducts.value[0])
  }
}

const quickAddProductToCart = (product) => {
  const cartItem = {
    type: 'product',
    product_id: product.id,
    itemType: product.type || 'accessory',
    description: product.name,
    quantity: 1,
    price: product.selling_price,
    details: {
      brand: product.brand,
      reference: product.reference
    }
  }

  cartItems.value.push(cartItem)
  productSearch.value = ''
  currentFilter.value = ''

  // Basculer vers le panier sur mobile après ajout
  if (isMobile.value) {
    mobileActiveTab.value = 'cart'
  }
}

const selectClientAndContinue = (client) => {
  selectedClient.value = client
  showClientSearch.value = false
}

const handleClientCreated = (client) => {
  selectedClient.value = client
  showNewClientForm.value = false
}

const resetSale = () => {
  selectedClient.value = null
  cartItems.value = []
  paidAmount.value = 0
  remainingAmount.value = 0
  clientSearch.value = ''
  productSearch.value = ''
  currentFilter.value = ''
  paymentMode.value = ''
  paymentMethod.value = ''
  advanceAmount.value = 0
  clientSearchInput.value = ''
  clientSearchResults.value = []
}



const addFrameToCart = (frameData) => {
  cartItems.value.push({
    type: frameData.product_id ? 'product' : 'custom',
    product_id: frameData.product_id,
    itemType: 'frame',
    description: frameData.description,
    quantity: frameData.quantity,
    price: frameData.price,
    details: frameData.details
  })

  // Basculer vers le panier sur mobile après ajout
  if (isMobile.value) {
    mobileActiveTab.value = 'cart'
  }
}

const addLensesToCart = (lensesData) => {
  cartItems.value.push({
    type: 'custom',
    product_id: null,
    itemType: 'lenses',
    description: lensesData.description,
    quantity: lensesData.quantity,
    price: lensesData.price,
    details: lensesData.details
  })

  // Basculer vers le panier sur mobile après ajout
  if (isMobile.value) {
    mobileActiveTab.value = 'cart'
  }
}

const addContactLensesToCart = (contactLensesData) => {
  cartItems.value.push({
    type: contactLensesData.product_id ? 'product' : 'custom',
    product_id: contactLensesData.product_id,
    itemType: 'contact_lenses',
    description: contactLensesData.description,
    quantity: contactLensesData.quantity,
    price: contactLensesData.price,
    details: contactLensesData.details
  })

  // Basculer vers le panier sur mobile après ajout
  if (isMobile.value) {
    mobileActiveTab.value = 'cart'
  }
}

const addAccessoryToCart = (accessoryData) => {
  cartItems.value.push({
    type: accessoryData.product_id ? 'product' : 'custom',
    product_id: accessoryData.product_id,
    itemType: 'accessory',
    description: accessoryData.description,
    quantity: accessoryData.quantity,
    price: accessoryData.price,
    details: accessoryData.details
  })

  // Basculer vers le panier sur mobile après ajout
  if (isMobile.value) {
    mobileActiveTab.value = 'cart'
  }
}

const removeItem = (index) => {
  cartItems.value.splice(index, 1)
}

const updateQuantity = (index, newQuantity) => {
  if (newQuantity > 0) {
    cartItems.value[index].quantity = newQuantity
  }
}

const getItemTypeClasses = (itemType) => {
  const classes = {
    frame: 'bg-blue-100 text-blue-600',
    lenses: 'bg-green-100 text-green-600',
    contact_lenses: 'bg-purple-100 text-purple-600',
    accessory: 'bg-yellow-100 text-yellow-600'
  }
  return classes[itemType] || 'bg-gray-100 text-gray-600'
}

// La fonction formatPrice est maintenant importée depuis utils/currency.js

// Méthodes pour les pastilles colorées des produits
const getProductColorClass = (product) => {
  const type = product.type || product.category || 'other'
  const colorMap = {
    'lens': 'bg-purple-500',
    'lenses': 'bg-purple-500',
    'verre': 'bg-purple-500',
    'verres': 'bg-purple-500',
    'frame': 'bg-green-500',
    'monture': 'bg-green-500',
    'montures': 'bg-green-500',
    'accessory': 'bg-red-500',
    'accessoire': 'bg-red-500',
    'chiffon': 'bg-red-500',
    'contact': 'bg-blue-500',
    'lentille': 'bg-blue-500'
  }
  return colorMap[type.toLowerCase()] || 'bg-gray-500'
}

const getProductInitial = (product) => {
  const type = product.type || product.category || 'other'
  const initialMap = {
    'lens': 'V',
    'lenses': 'V',
    'verre': 'V',
    'verres': 'V',
    'frame': 'M',
    'monture': 'M',
    'montures': 'M',
    'accessory': 'C',
    'accessoire': 'C',
    'chiffon': 'C',
    'contact': 'L',
    'lentille': 'L'
  }
  return initialMap[type.toLowerCase()] || '?'
}

// Méthodes pour les pastilles colorées du panier
const getCartItemColorClass = (item) => {
  if (item.itemType) {
    const colorMap = {
      'lenses': 'bg-purple-500',
      'frame': 'bg-green-500',
      'accessory': 'bg-red-500',
      'contact': 'bg-blue-500'
    }
    return colorMap[item.itemType] || 'bg-gray-500'
  }

  // Fallback basé sur la description
  const description = item.description.toLowerCase()
  if (description.includes('verre') || description.includes('lens')) return 'bg-purple-500'
  if (description.includes('monture') || description.includes('frame')) return 'bg-green-500'
  if (description.includes('chiffon') || description.includes('accessoire')) return 'bg-red-500'
  if (description.includes('lentille') || description.includes('contact')) return 'bg-blue-500'

  return 'bg-gray-500'
}

const getCartItemInitial = (item) => {
  if (item.itemType) {
    const initialMap = {
      'lenses': 'V',
      'frame': 'M',
      'accessory': 'C',
      'contact': 'L'
    }
    return initialMap[item.itemType] || '?'
  }

  // Fallback basé sur la description
  const description = item.description.toLowerCase()
  if (description.includes('verre') || description.includes('lens')) return 'V'
  if (description.includes('monture') || description.includes('frame')) return 'M'
  if (description.includes('chiffon') || description.includes('accessoire')) return 'C'
  if (description.includes('lentille') || description.includes('contact')) return 'L'

  return '?'
}

const formatLensDetails = (details) => {
  const parts = []
  if (details.sph !== undefined) parts.push(`SPH: ${details.sph >= 0 ? '+' : ''}${details.sph}`)
  if (details.cyl !== undefined) parts.push(`CYL: ${details.cyl >= 0 ? '+' : ''}${details.cyl}`)
  if (details.axe !== undefined) parts.push(`AXE: ${details.axe}°`)
  if (details.add !== undefined) parts.push(`ADD: +${details.add}`)
  return parts.join(', ')
}

const saveDraft = async () => {
  await createSale(false)
}

// Nouvelle méthode pour gérer les paiements selon le mode sélectionné
const handlePayment = async () => {
  if (cartItems.value.length === 0 || !paymentMode.value) return

  // Validation pour le paiement partiel
  if (paymentMode.value === 'partial') {
    if (!advanceAmount.value || advanceAmount.value <= 0) {
      alert('Veuillez saisir un montant d\'avance valide pour le paiement partiel.')
      return
    }
    if (advanceAmount.value > totalAmount.value) {
      alert('Le montant d\'avance ne peut pas être supérieur au total à payer.')
      return
    }
  }

  if (paymentMode.value === 'complete') {
    await finalizeSale()
  } else if (paymentMode.value === 'partial') {
    await finalizeSalePartial()
  }
}

const finalizeSale = async () => {
  if (cartItems.value.length === 0) return

  isSaving.value = true

  try {
    const saleData = {
      client_id: selectedClient.value ? selectedClient.value.id : null,
      items: cartItems.value,
      paid_amount: totalAmount.value, // Paiement complet
      payment_method: 'complete',
      total_amount: totalAmount.value
    }

    const response = await axios.post('/api/sales', saleData)

    if (response.data.success) {
      // Enregistrer l'ID de la vente pour l'impression
      lastSaleId.value = response.data.sale?.id || response.data.data?.id || Date.now()

      // Sauvegarder les données de la vente AVANT réinitialisation
      const saleData = {
        id: lastSaleId.value,
        date: new Date(),
        vendeur: 'Système', // À remplacer par l'utilisateur connecté
        client: selectedClient.value,
        items: cartItems.value.map(item => ({
          description: item.description,
          quantity: item.quantity,
          price: item.price,
          details: item.details
        })),
        paymentMethod: 'complete',
        paidAmount: totalAmount.value,
        remainingAmount: 0,
        totalAmount: totalAmount.value
      }

      // Préparer les données pour le modal de confirmation
      confirmationData.value = {
        operationType: 'sale',
        operationId: lastSaleId.value,
        clientName: selectedClient.value?.name || '',
        itemCount: cartItems.value.length,
        totalAmount: totalAmount.value,
        paidAmount: totalAmount.value, // Paiement complet
        remainingAmount: 0,
        saleData: saleData // Ajouter les données complètes
      }

      // Réinitialiser le formulaire
      resetSale()

      // Afficher le modal de confirmation d'impression
      showPrintConfirmationModal.value = true
    } else {
      alert('Erreur lors de la création de la vente')
    }
  } catch (error) {
    console.error('Erreur lors de la création de la vente:', error)
    alert('Erreur lors de la création de la vente: ' + (error.message || 'Erreur inconnue'))
  } finally {
    isSaving.value = false
  }
}



const createSale = async (finalize = false) => {
  if (isSaving.value || cartItems.value.length === 0) return

  isSaving.value = true

  try {
    const saleData = {
      client_id: selectedClient.value ? selectedClient.value.id : null,
      items: cartItems.value,
      paid_amount: finalize ? paidAmount.value : 0
    }

    const response = await axios.post('/api/sales', saleData)

    if (response.data.success) {
      if (!finalize) {
        // Pour les devis, préparer les données pour le modal de confirmation
        const devisId = response.data.sale?.id || response.data.data?.id || Date.now()

        // Sauvegarder les données du devis AVANT réinitialisation
        const devisData = {
          id: devisId,
          date: new Date(),
          vendeur: 'Système', // À remplacer par l'utilisateur connecté
          client: selectedClient.value,
          items: cartItems.value.map(item => ({
            description: item.description,
            quantity: item.quantity,
            price: item.price,
            details: item.details
          })),
          paymentMethod: null,
          paidAmount: 0,
          remainingAmount: totalAmount.value,
          totalAmount: totalAmount.value
        }

        confirmationData.value = {
          operationType: 'quote',
          operationId: devisId,
          clientName: selectedClient.value?.name || '',
          itemCount: cartItems.value.length,
          totalAmount: totalAmount.value,
          paidAmount: 0,
          remainingAmount: totalAmount.value,
          saleData: devisData // Ajouter les données complètes
        }

        // Réinitialiser le formulaire
        resetSale()

        // Afficher le modal de confirmation d'impression
        showPrintConfirmationModal.value = true
      } else {
        // Pour les ventes finalisées (ne devrait pas arriver avec cette méthode)
        resetSale()
        alert('Vente finalisée avec succès !')
      }
    } else {
      alert('Erreur lors de la création de la vente')
    }
  } catch (error) {
    console.error('Erreur lors de la création de la vente:', error)
    if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      alert('Erreurs de validation:\n' + errors.join('\n'))
    } else {
      alert('Erreur lors de la création de la vente')
    }
  } finally {
    isSaving.value = false
  }
}

// Mobile responsive
const mobileActiveTab = ref('products')
const isMobile = ref(false)

// Détecter si on est sur mobile
const checkMobile = () => {
  isMobile.value = window.innerWidth < 768
}

// Méthodes d'impression
const closePrintConfirmationModal = () => {
  showPrintConfirmationModal.value = false
  // Ne pas réinitialiser confirmationData pour éviter les erreurs
  // Les données seront écrasées lors de la prochaine vente
}

const handlePrintConfirmation = (confirmation) => {
  // Si l'utilisateur a choisi d'imprimer
  if (confirmation.printType !== 'none') {
    try {
      // Déterminer le type de document à imprimer
      let documentType = confirmation.printType
      if (confirmation.printType === 'receipt') {
        documentType = 'ticket' // Les reçus utilisent le template ticket
      }

      console.log('Index - handlePrintConfirmation - Données reçues:', confirmation)
      console.log('Index - handlePrintConfirmation - saleData du confirmation:', confirmation.saleData)
      console.log('Index - handlePrintConfirmation - saleData stocké:', confirmationData.value.saleData)

      // Utiliser les données complètes (priorité à celles du confirmation)
      const saleData = confirmation.saleData || confirmationData.value.saleData

      if (saleData) {
        // Vérifier que les articles sont présents
        console.log('Index - handlePrintConfirmation - Articles:', saleData.items)

        // Ajouter les options d'impression aux données sauvegardées
        const printDataComplete = {
          ...saleData,
          printOptions: confirmation.printOptions
        }

        // Préparer les données d'impression
        printType.value = documentType
        printData.value = printDataComplete

        // Ouvrir le modal d'impression
        showPrintModal.value = true
      } else {
        console.error('Données de vente non disponibles pour impression')
        alert('Erreur: Données de vente non disponibles pour impression')
      }
    } catch (error) {
      console.error('Erreur lors de la préparation de l\'impression:', error)
      alert('Erreur lors de la préparation de l\'impression: ' + error.message)
    }
  }

  // Fermer le modal de confirmation
  closePrintConfirmationModal()
}

const openPrintModal = (type) => {
  try {
    printType.value = type
    printData.value = preparePrintData(type)
    showPrintModal.value = true
  } catch (error) {
    console.error('Erreur lors de l\'ouverture du modal d\'impression:', error)
    alert('Erreur lors de la préparation de l\'impression. Veuillez réessayer.')
  }
}

const closePrintModal = () => {
  showPrintModal.value = false
  printData.value = {}
}

const preparePrintDataFromConfirmation = (confirmationData, printOptions) => {
  return {
    id: confirmationData.operationId,
    date: new Date(),
    vendeur: 'Système', // À remplacer par l'utilisateur connecté
    client: confirmationData.clientName ? { name: confirmationData.clientName } : null,
    items: [], // Les items ont été réinitialisés, on utilise les données sauvegardées
    paymentMethod: confirmationData.operationType === 'sale' ? 'complete' : null,
    paidAmount: confirmationData.paidAmount,
    remainingAmount: confirmationData.remainingAmount,
    printOptions: printOptions
  }
}

const preparePrintData = (type) => {
  const baseData = {
    id: lastSaleId.value || 'NOUVEAU',
    date: new Date(),
    vendeur: 'Système', // À remplacer par l'utilisateur connecté
    client: selectedClient.value,
    items: cartItems.value.map(item => ({
      description: item.description,
      quantity: item.quantity,
      price: item.price,
      details: item.details
    })),
    paymentMethod: paymentMode.value,
    paidAmount: paidAmount.value,
    remainingAmount: computedRemainingAmount.value
  }

  if (type === 'devis') {
    return {
      ...baseData,
      validityDate: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000), // 30 jours
      conditions: [
        'Devis valable 30 jours',
        'Prix susceptibles de modification selon disponibilité',
        'Acompte de 50% à la commande'
      ]
    }
  }

  return baseData
}

const handlePrintSuccess = (printInfo) => {
  console.log('Document imprimé:', printInfo)
  // Optionnel: enregistrer l'impression en base
}

// Lifecycle
onMounted(() => {
  loadClients()
  loadProducts()

  // Détecter la taille d'écran
  checkMobile()
  window.addEventListener('resize', checkMobile)

  // Écouter l'événement de réinitialisation depuis le header
  window.addEventListener('reset-pos-sale', resetSale)
})

// Nettoyage lors de la destruction du composant
onUnmounted(() => {
  window.removeEventListener('reset-pos-sale', resetSale)
  window.removeEventListener('resize', checkMobile)
})
</script>

<style scoped>
/* Améliorations mobile pour le POS */
@media (max-width: 768px) {
  /* Améliorer la zone tactile des boutons */
  button {
    min-height: 44px;
  }

  /* Optimiser les inputs pour mobile */
  input[type="text"], input[type="number"] {
    font-size: 16px; /* Évite le zoom sur iOS */
  }

  /* Améliorer l'affichage des listes de produits */
  .product-list-item {
    padding: 12px;
    border-radius: 8px;
  }

  /* Optimiser les modals sur mobile */
  .modal-content {
    margin: 10px;
    max-height: calc(100vh - 20px);
  }
}

/* Animation pour les onglets mobiles */
.tab-transition {
  transition: all 0.3s ease-in-out;
}

/* Améliorer la visibilité des badges */
.cart-badge {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
}

/* Amélioration du défilement */
.pos-container {
  height: 100vh;
  overflow: hidden;
}

.pos-scrollable {
  overflow-y: auto;
  scrollbar-width: thin;
  scrollbar-color: #cbd5e0 #f7fafc;
}

.pos-scrollable::-webkit-scrollbar {
  width: 6px;
}

.pos-scrollable::-webkit-scrollbar-track {
  background: #f7fafc;
  border-radius: 3px;
}

.pos-scrollable::-webkit-scrollbar-thumb {
  background: #cbd5e0;
  border-radius: 3px;
}

.pos-scrollable::-webkit-scrollbar-thumb:hover {
  background: #a0aec0;
}

/* Défilement fluide sur mobile */
@media (max-width: 768px) {
  .pos-mobile-scroll {
    -webkit-overflow-scrolling: touch;
    overflow-y: auto;
    max-height: calc(100vh - 120px);
  }
}

/* Amélioration de la navigation mobile */
.mobile-nav-sticky {
  position: sticky;
  top: 0;
  z-index: 10;
  backdrop-filter: blur(8px);
  background-color: rgba(255, 255, 255, 0.95);
}

/* Indicateur de défilement */
.scroll-indicator {
  position: relative;
}

.scroll-indicator::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 30px;
  height: 3px;
  background: linear-gradient(90deg, transparent, #3b82f6, transparent);
  border-radius: 2px;
  opacity: 0.6;
  animation: scroll-hint 2s ease-in-out infinite;
}

@keyframes scroll-hint {
  0%, 100% { opacity: 0.3; }
  50% { opacity: 0.8; }
}

/* Masquer l'indicateur sur desktop */
@media (min-width: 768px) {
  .scroll-indicator::after {
    display: none;
  }
}
</style>
