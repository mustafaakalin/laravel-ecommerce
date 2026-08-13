import './bootstrap';
import '@fortawesome/fontawesome-free/css/all.min.css';

import { createApp } from 'vue/dist/vue.esm-bundler';
import ProductSearch from './vue/components/ProductSearch.vue';

const app = createApp({});

app.component('product-search', ProductSearch);

// Mount the Vue app
app.mount('#app');