import './bootstrap'; // Untuk mengimpor bootstrap atau dependensi lainnya
import { createApp } from 'vue';
import App from './app.vue'; // Mengimpor App.vue yang sudah dibuat
import { createRouter, createWebHistory } from 'vue-router';
import ChatApp from "./components/ChatApp.vue";
import Login from "./components/Login.vue";
import Register from "./components/Register.vue";

// Membuat router
const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', component: ChatApp },
        { path: '/login', component: Login },
        { path: '/register', component: Register },
    ],
});

// Membuat aplikasi Vue dan memasangnya
createApp(App)
    .use(router) // Menambahkan router ke aplikasi Vue
    .mount('#app'); // Memasang aplikasi ke elemen dengan id "app"