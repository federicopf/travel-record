<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
  requests: Array
})

const accepting = ref(null)
const rejecting = ref(null)
const feedbacks = ref({}) // userId: message

const acceptRequest = async (userId) => {
  accepting.value = userId

  try {
    await axios.put(route('friends.accept', userId))

    const index = props.requests.findIndex(user => user.id === userId)
    if (index !== -1) {
      feedbacks.value[userId] = 'Richiesta accettata!'
      setTimeout(() => {
        props.requests.splice(index, 1)
        delete feedbacks.value[userId]
      }, 3000)
    }
  } catch (e) {
    feedbacks.value[userId] = 'Errore durante l’accettazione.'
    setTimeout(() => delete feedbacks.value[userId], 3000)
  } finally {
    accepting.value = null
  }
}

const rejectRequest = async (userId) => {
  rejecting.value = userId

  try {
    await axios.delete(route('friends.reject', userId))

    const index = props.requests.findIndex(user => user.id === userId)
    if (index !== -1) {
      feedbacks.value[userId] = 'Richiesta rifiutata.'
      setTimeout(() => {
        props.requests.splice(index, 1)
        delete feedbacks.value[userId]
      }, 3000)
    }
  } catch (e) {
    feedbacks.value[userId] = 'Errore durante il rifiuto.'
    setTimeout(() => delete feedbacks.value[userId], 3000)
  } finally {
    rejecting.value = null
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

      <div v-if="requests.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <div
          v-for="user in requests"
          :key="user.id"
          class="bg-white shadow rounded-lg p-4 text-center"
        >
          <h2 class="text-lg font-semibold text-gray-800">{{ user.name }}</h2>
          <p class="text-sm text-gray-500">@{{ user.username }}</p>

          <div class="mt-4 flex flex-col gap-2">
            <button
              @click="acceptRequest(user.id)"
              :disabled="accepting === user.id"
              class="bg-green-600 text-white py-1 rounded hover:bg-green-700 transition"
            >
              {{ accepting === user.id ? '...' : 'Accetta' }}
            </button>
            <button
              @click="rejectRequest(user.id)"
              :disabled="rejecting === user.id"
              class="bg-red-500 text-white py-1 rounded hover:bg-red-600 transition"
            >
              {{ rejecting === user.id ? '...' : 'Rifiuta' }}
            </button>

            <p
              v-if="feedbacks[user.id]"
              class="text-xs text-center text-gray-600 mt-1"
            >
              {{ feedbacks[user.id] }}
            </p>
          </div>
        </div>
      </div>

      <p v-else class="text-gray-500 text-center mt-6">
        Nessuna richiesta in attesa.
      </p>
    </div>
  </AppLayout>
</template>
