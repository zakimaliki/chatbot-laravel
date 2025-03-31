<template>
  <div id="app">
    <!-- Menampilkan komponen berdasarkan route -->
    <router-view></router-view>
  </div>
</template>

<script>
// Mengimpor Vue Router
import { defineComponent } from "vue";
import { createRouter, createWebHistory } from "vue-router";
import ChatApp from "./components/ChatApp.vue";
import Login from "./components/Login.vue";
import Register from "./components/Register.vue";

// Membuat router dengan rute
const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: "/", component: ChatApp },
    { path: "/login", component: Login },
    { path: "/register", component: Register },
  ],
});

export default defineComponent({
  name: "App",
  components: {
    ChatApp,
    Login,
    Register,
  },
  setup() {
    return { router };
  },
});
</script>

<style>
/* Styling aplikasi Vue (opsional) */
</style>