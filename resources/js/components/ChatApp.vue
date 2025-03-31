<template>
  <div class="min-h-screen bg-gray-100 flex flex-col">
    <!-- Header -->
    <header class="bg-white shadow-md py-4">
      <div class="max-w-4xl mx-auto px-6">
        <h1 class="text-3xl font-semibold text-gray-800">AI Chatbot</h1>
      </div>
    </header>

    <!-- Chat Container -->
    <div class="flex-1 max-w-4xl w-full mx-auto p-4 space-y-6">
      <!-- Histori Chat -->
      <div class="space-y-4">
        <button
          @click="loadHistory"
          class="bg-green-500 text-white px-4 py-2 rounded-lg"
        >
          Lihat Histori Chat
        </button>
        <div v-if="history.length > 0" class="space-y-2 mt-4">
          <div
            v-for="(item, index) in history"
            :key="index"
            class="p-4 bg-gray-200 rounded-lg shadow-sm"
          >
            <p><strong>Anda:</strong> {{ item.user_message }}</p>
            <p><strong>AI:</strong> {{ item.ai_response }}</p>
          </div>
        </div>
      </div>

      <!-- Chat Messages -->
      <div
        class="bg-white rounded-lg shadow-lg overflow-auto p-4 space-y-4 h-[500px]"
      >
        <div
          v-for="(message, index) in messages"
          :key="index"
          class="flex flex-col space-y-2"
        >
          <!-- User Message -->
          <div v-if="message.role === 'user'" class="flex justify-end">
            <div class="bg-blue-500 text-white p-3 rounded-lg max-w-xs">
              <p>{{ message.content }}</p>
            </div>
          </div>

          <!-- AI Message -->
          <div v-if="message.role === 'assistant'" class="flex justify-start">
            <div class="bg-gray-200 text-gray-800 p-3 rounded-lg max-w-xs">
              <p>{{ message.content }}</p>
              <span
                v-if="message.status === 'thinking'"
                class="text-sm text-gray-500"
                >AI is thinking...</span
              >
            </div>
          </div>
        </div>
      </div>

      <!-- Input Area -->
      <div class="bg-white p-4 rounded-lg shadow-md">
        <div class="flex items-center gap-4">
          <textarea
            v-model="input"
            rows="3"
            class="flex-1 resize-none border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
            placeholder="Tulis pesan Anda..."
          ></textarea>
          <button
            @click="sendMessage"
            class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
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
const messages = ref([]);
const history = ref([]);

const sendMessage = async () => {
  if (!input.value.trim()) return;

  const userMessage = {
    role: "user",
    content: input.value,
    status: "complete",
  };
  messages.value.push(userMessage);

  const aiMessage = { role: "assistant", content: "", status: "thinking" };
  messages.value.push(aiMessage);

  input.value = "";

  try {
    const response = await fetch("/api/chat", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
      body: JSON.stringify({ message: userMessage.content }),
    });

    aiMessage.status = "generating";
    const reader = response.body.getReader();
    const decoder = new TextDecoder();

    while (true) {
      const { done, value } = await reader.read();
      if (done) break;
      aiMessage.content += decoder.decode(value);
      messages.value[messages.value.length - 1] = { ...aiMessage };
    }

    const parsedMessage = JSON.parse(aiMessage.content);
    aiMessage.content = parsedMessage.data;
    aiMessage.status = "complete";
    messages.value[messages.value.length - 1] = { ...aiMessage };
  } catch (error) {
    aiMessage.content = "⚠️ Layanan AI tidak tersedia";
    aiMessage.status = "error";
    messages.value[messages.value.length - 1] = { ...aiMessage };
  }
};

// Load history
const loadHistory = async () => {
  try {
    const response = await fetch("/api/history", {
      method: "GET",
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });
    history.value = await response.json();
  } catch (error) {
    console.error("Failed to load history:", error);
  }
};
</script>