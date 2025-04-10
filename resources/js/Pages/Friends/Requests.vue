<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import axios from 'axios'

import ProfilePicture from '@/Components/Profile/Sections/ProfilePicture.vue'

const props = defineProps({
  requests: Array
})

const processingUserId = ref(null)
const feedbacks = ref({}) // userId: message

const handleRequest = async (userId, action) => {
  processingUserId.value = userId

  try {
    if (action === 'accept') {
      await axios.put(route('friends.accept', userId))
      feedbacks.value[userId] = 'Richiesta accettata!'
    } else {
      await axios.delete(route('friends.reject', userId))
      feedbacks.value[userId] = 'Richiesta rifiutata.'
    }

    // Rimuove l'utente dalla lista dopo 3 secondi
    setTimeout(() => {
      const index = props.requests.findIndex(u => u.id === userId)
      if (index !== -1) {
        props.requests.splice(index, 1)
      }
      delete feedbacks.value[userId]
    }, 3000)
  } catch (e) {
    feedbacks.value[userId] = 'Errore. Riprova.'
    setTimeout(() => delete feedbacks.value[userId], 3000)
  } finally {
    processingUserId.value = null
  }
}
</script>

<template>
  <AppLayout>
    <div class="max-w-4xl mx-auto px-6 py-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-6">Richieste di amicizia</h1>

      <div class="mb-4">
        <Link
          :href="route('friends.index')"
          class="inline-block text-sm text-gray-700 underline hover:text-gray-900"
        >
          ← Torna alla lista amici
        </Link>
      </div>

      <div v-if="requests.length" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <div
          v-for="user in requests"
          :key="user.id"
          class="bg-white shadow rounded-lg p-4 text-center flex flex-col items-center"
        >
          <ProfilePicture
            :username="user.username"
            :name="user.name"
            :size="'w-16 h-16'"
            :font-size="'text-xl'"
            class="mb-2"
          />

          <h2 class="text-lg font-semibold text-gray-800">{{ user.name }}</h2>
          <p class="text-sm text-gray-500 mb-3">@{{ user.username }}</p>

          <div class="w-full">
            <!-- Mostra messaggio di feedback se esiste -->
            <p v-if="feedbacks[user.id]" class="text-sm text-center text-gray-600">
              {{ feedbacks[user.id] }}
            </p>

            <!-- Mostra bottoni solo se nessun feedback è già presente -->
            <div v-else class="flex flex-col gap-2">
              <button
                @click="handleRequest(user.id, 'accept')"
                :disabled="processingUserId === user.id"
                class="bg-green-600 text-white py-1 rounded hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Accetta
              </button>
              <button
                @click="handleRequest(user.id, 'reject')"
                :disabled="processingUserId === user.id"
                class="bg-red-500 text-white py-1 rounded hover:bg-red-600 transition disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Rifiuta
              </button>
            </div>
          </div>
        </div>
      </div>

      <p v-else class="text-gray-500 text-center mt-6">
        Nessuna richiesta in attesa.
      </p>
    </div>
  </AppLayout>
</template>
