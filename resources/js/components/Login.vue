<template>
  <div class="max-w-md mx-auto p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold mb-6">Login</h2>

    <form @submit.prevent="login">
      <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700"
          >Email</label
        >
        <input
          type="email"
          v-model="email"
          id="email"
          class="w-full px-3 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          required
        />
      </div>

      <div class="mb-6">
        <label for="password" class="block text-sm font-medium text-gray-700"
          >Password</label
        >
        <input
          type="password"
          v-model="password"
          id="password"
          class="w-full px-3 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          required
        />
      </div>

      <button
        type="submit"
        class="w-full py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
      >
        Login
      </button>
    </form>

    <p class="mt-4 text-center text-sm text-gray-500">
      Don't have an account?
      <router-link to="/register" class="text-blue-600">Register</router-link>
    </p>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";

const email = ref("");
const password = ref("");
const router = useRouter();

const login = async () => {
  try {
    const response = await fetch("/api/login", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        email: email.value,
        password: password.value,
      }),
    });
    const data = await response.json();
    if (response.ok) {
      localStorage.setItem("token", data.token); // Menyimpan token di localStorage
      router.push("/chat");
    } else {
      alert(data.error || "Login failed");
    }
  } catch (error) {
    console.error("Login error", error);
  }
};
</script>