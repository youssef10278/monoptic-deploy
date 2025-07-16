import '../css/app.css';
import './bootstrap';

import { createApp } from 'vue';
import router from './router';
import axios from 'axios';
import App from './App.vue';

// Configuration d'Axios
axios.defaults.baseURL = window.location.origin;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['Content-Type'] = 'application/json';

// Intercepteur pour ajouter le token d'authentification
axios.interceptors.request.use(config => {
    const token = localStorage.getItem('auth_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Intercepteur pour gérer les erreurs d'authentification
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_data');
            router.push('/login');
        }
        return Promise.reject(error);
    }
);

// Créer l'application Vue
const app = createApp(App);

// Utiliser le routeur
app.use(router);

// Rendre Axios disponible globalement
app.config.globalProperties.$axios = axios;

// Monter l'application
app.mount('#app');
