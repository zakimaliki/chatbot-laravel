<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
      <div class="max-w-4xl mx-auto px-4 py-4">
        <h1 class="text-2xl font-semibold text-gray-800">ChatGPT</h1>
      </div>
    </header>

    <!-- Chat Container -->
    <div class="flex-1 max-w-4xl w-full mx-auto p-4">
      <!-- Chat messages -->
      <div
        class="bg-white rounded-lg shadow-sm min-h-[400px] mb-4 p-4 overflow-y-auto"
      >
        <div
          v-for="(message, index) in messages"
          :key="index"
          class="mb-2 flex"
        >
          <div
            :class="
              message.role === 'user'
                ? 'bg-blue-100 p-2 rounded-lg inline-block max-w-[70%] ml-auto'
                : 'bg-gray-100 p-2 rounded-lg inline-block max-w-[70%] mr-auto'
            "
          >
            <span>{{ message.content }}</span>
            <span v-if="message.status === 'thinking'" class="text-gray-500"
              >...loading</span
            >
            <div class="text-gray-500 text-xs">{{ message.created_at }}</div>
          </div>
        </div>
      </div>

      <!-- Input Area -->
      <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex gap-2">
          <textarea
            v-model="input"
            rows="2"
            class="flex-1 resize-none border border-gray-200 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
            placeholder="Tulis pesan Anda di sini..."
          />
          <button
            @click="sendMessage"
            class="px-4 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 flex items-center"
          >
            <i class="fa-solid fa-arrow-up"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

const input = ref("");
const messages = ref([]);

// Fungsi untuk mengambil riwayat chat
const fetchChatHistory = async () => {
  try {
    const response = await fetch("/api/history", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Authorization: "Bearer " + localStorage.getItem("token"),
      },
    });

    if (response.ok) {
      const history = await response.json();
      const formattedMessages = history.flatMap((item) => [
        {
          role: "user",
          content: item.user_message,
          status: "complete",
          created_at: new Date(item.created_at).toLocaleDateString(),
        },
        {
          role: "assistant",
          content: item.ai_response,
          status: "complete",
          created_at: new Date(item.created_at).toLocaleDateString(),
        },
      ]);
      messages.value = formattedMessages;
    }
  } catch (error) {
    console.error("Error fetching chat history:", error);
  }
};

// Panggil fetchChatHistory saat komponen dimuat
onMounted(() => {
  fetchChatHistory();
});

// Fungsi untuk mengirim pesan ke AI
const sendMessage = async () => {
  if (!input.value.trim()) return;

  // Tambahkan pesan pengguna ke tampilan
  const userMessage = {
    role: "user",
    content: input.value,
    status: "complete",
    created_at: new Date().toLocaleDateString(),
  };
  messages.value.push(userMessage);

  // Tambahkan placeholder untuk respons AI
  const aiMessage = {
    role: "assistant",
    content: "",
    status: "thinking",
    created_at: "",
  };
  messages.value.push(aiMessage);

  input.value = "";

  try {
    const response = await fetch("/api/chat", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: "Bearer " + localStorage.getItem("token"),
      },
      body: JSON.stringify({ message: userMessage.content }),
    });

    // Mendukung streaming data
    const reader = response.body.getReader();
    const decoder = new TextDecoder();
    let partialMessage = "";

    while (true) {
      const { done, value } = await reader.read();
      if (done) break;
      const textChunk = decoder.decode(value, { stream: true });

      // Pastikan hanya membaca konten yang diperlukan
      const lines = textChunk
        .split("\n")
        .map((line) => line.trim())
        .filter((line) => line.startsWith("data:"));

      for (const line of lines) {
        const jsonData = line.substring(6); // Hapus "data: " di awal

        if (jsonData === "[DONE]") break;

        try {
          const parsedData = JSON.parse(jsonData);
          const newText = parsedData.data || "";
          partialMessage += newText;

          // Update tampilan secara real-time
          aiMessage.content = partialMessage;
          aiMessage.created_at = new Date().toLocaleDateString();
          messages.value[messages.value.length - 1] = { ...aiMessage };
        } catch (error) {
          console.error("Error parsing JSON:", error);
        }
      }
    }

    // Tandai respons AI sebagai selesai
    aiMessage.status = "complete";
    aiMessage.created_at = new Date().toLocaleDateString();
    messages.value[messages.value.length - 1] = { ...aiMessage };
  } catch (error) {
    aiMessage.content = "⚠️ Layanan AI tidak tersedia";
    aiMessage.status = "error";
    messages.value[messages.value.length - 1] = { ...aiMessage };
  }
};
</script>