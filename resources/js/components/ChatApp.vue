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
        <div v-for="(message, index) in messages" :key="index" class="mb-2">
          <div :class="message.role === 'user' ? 'text-right' : 'text-left'">
            <span class="font-semibold"
              >{{ message.role === "user" ? "Anda" : "AI" }}:</span
            >
            <span>{{ message.content }}</span>
            <!-- Show loading indicator if AI is thinking -->
            <span v-if="message.status === 'thinking'" class="text-gray-500"
              >...loading</span
            >
          </div>
        </div>
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
const messages = ref([]);

const sendMessage = async () => {
  if (!input.value.trim()) return;

  // Kirim pesan pengguna
  const userMessage = {
    role: "user",
    content: input.value,
    status: "complete",
  };
  messages.value.push(userMessage);

  // Menambahkan placeholder AI
  const aiMessage = { role: "assistant", content: "", status: "thinking" };
  messages.value.push(aiMessage);

  input.value = "";

  try {
    // Mengirim request ke API Laravel
    const response = await fetch("/api/chat", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ message: userMessage.content }),
    });

    aiMessage.status = "generating";
    const reader = response.body.getReader();
    const decoder = new TextDecoder();

    // Streaming data dari OpenAI
    while (true) {
      const { done, value } = await reader.read();
      if (done) break;
      aiMessage.content += decoder.decode(value);

      // Update the last message with the AI response in real-time
      messages.value[messages.value.length - 1] = { ...aiMessage }; // Create a new object reference
    }

    // Log the raw AI message content
    console.log("Raw AI message content:", aiMessage.content);

    // New code to display the message in the desired format
    const parsedMessage = JSON.parse(aiMessage.content);
    aiMessage.content = parsedMessage.data;

    // Update the last message with the AI response
    messages.value[messages.value.length - 1] = { ...aiMessage }; // Create a new object reference

    // Update the status to complete after setting content
    aiMessage.status = "complete";

    // Log the updated AI message content
    console.log("Updated AI message content:", aiMessage.content);
  } catch (error) {
    aiMessage.content = "⚠️ Layanan AI tidak tersedia";
    aiMessage.status = "error";
    messages.value[messages.value.length - 1] = { ...aiMessage }; // Create a new object reference
  }
};
</script>