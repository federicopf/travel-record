<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import axios from 'axios'

const props = defineProps({
  query: String,
  results: Array
})

const search = ref(props.query ?? '')
let debounceTimer = null
const sendingRequest = ref(null)

watch(search, (value) => {
  clearTimeout(debounceTimer)

  debounceTimer = setTimeout(() => {
    if (value.trim().length >= 3) {
      router.get(route('friends.search'), { q: value }, {
        preserveState: true,
        replace: true
      })
    }
  }, 500)
})

const sendFollowRequest = async (userId) => {
  sendingRequest.value = userId

  try {
    const response = await axios.post(route('friends.follow', userId))
    const status = response.data.status

    const index = props.results.findIndex(user => user.id === userId)
    if (index !== -1) {
      props.results[index].status = status
    }
  } catch (error) {
    console.error(error.response?.data?.error ?? 'Errore durante l’invio della richiesta.')
  } finally {
    sendingRequest.value = null
  }
}
</script>

<template>
  <AppLayout>
    <div class="max-w-4xl mx-auto px-6 py-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-6">Cerca utenti</h1>

      <div class="mb-6">
        <input
          type="text"
          v-model="search"
          placeholder="Cerca per username (min 3 caratteri)..."
          class="w-full border px-4 py-2 rounded-lg"
        />
      </div>

      <div v-if="results.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <div
          v-for="user in results"
          :key="user.id"
          class="bg-white shadow rounded-lg p-4 text-center"
        >
          <h2 class="text-lg font-semibold text-gray-800">{{ user.name }}</h2>
          <p class="text-sm text-gray-500">@{{ user.username }}</p>

          <div class="mt-2">
            <span
              v-if="user.status === 'accepted'"
              class="inline-block text-green-600 text-sm font-medium"
            >
              Già seguito
            </span>
            <span
              v-else-if="user.status === 'pending'"
              class="inline-block text-yellow-600 text-sm font-medium"
            >
              Richiesta in attesa
            </span>
            <button
              v-else
              @click="sendFollowRequest(user.id)"
              :disabled="sendingRequest === user.id"
              class="text-sm text-blue-600 underline hover:text-blue-800"
            >
              {{ sendingRequest === user.id ? '...' : 'Segui' }}
            </button>
          </div>
        </div>
      </div>

      <p
        v-else-if="search.trim().length >= 3"
        class="text-gray-500 text-center mt-6"
      >
        Nessun utente trovato con "{{ search }}"
      </p>
    </div>
  </AppLayout>
</template>
