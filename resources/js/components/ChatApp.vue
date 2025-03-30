<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
      <div class="max-w-4xl mx-auto px-4 py-4">
        <h1 class="text-2xl font-semibold text-gray-800">Chatbot</h1>
      </div>
    </header>

    <!-- Chat Container -->
    <div class="flex-1 max-w-4xl w-full mx-auto p-4">
      <!-- Chat messages will go here -->
      <div class="bg-white rounded-lg shadow-sm min-h-[400px] mb-4 p-4">
        <!-- Messages will be displayed here -->
      </div>

      <!-- Input Area -->
      <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex gap-3">
          <textarea
            v-model="input"
            rows="3"
            class="flex-1 resize-none border border-gray-200 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
            placeholder="Tulis pesan Anda di sini..."
          />
          <button
            @click="sendMessage"
            class="self-end px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
          >
            Kirim
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

const input = ref("");
const sendMessage = () => {
  console.log("Mengirim:", input.value);
};
</script>