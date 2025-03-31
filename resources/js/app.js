import './bootstrap'; // Untuk mengimpor bootstrap atau dependensi lainnya
import { createApp } from 'vue';
import App from './app.vue'; // Mengimpor App.vue yang sudah dibuat
import { createRouter, createWebHistory } from 'vue-router';
import ChatApp from "./components/ChatApp.vue";
import Login from "./components/Login.vue";
import Register from "./components/Register.vue";

const isAuthenticated = localStorage.getItem('token') !== null; // Ganti dengan logika autentikasi yang sesuai
// Membuat router
const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: "/",
            component: ChatApp,
            beforeEnter: (to, from, next) => {
                if (!isAuthenticated) {
                    next('/login'); // Redirect ke halaman login jika belum login
                } else {
                    next(); // Lanjutkan ke rute yang diminta
                }
            }
        },
        {
            path: "/login",
            component: Login,
            beforeEnter: (to, from, next) => {
                if (isAuthenticated) {
                    next("/"); // Redirect ke halaman utama jika sudah login
                } else {
                    next(); // Lanjutkan ke halaman login jika belum login
                }
            },
        },
        { path: "/register", component: Register },
    ],
});

// Membuat aplikasi Vue dan memasangnya
createApp(App)
    .use(router) // Menambahkan router ke aplikasi Vue
    .mount('#app'); // Memasang aplikasi ke elemen dengan id "app"