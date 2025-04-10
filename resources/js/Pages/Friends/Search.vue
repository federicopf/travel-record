<script setup>
import { ref, watch } from 'vue'
import { router, Link  } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import axios from 'axios'

import ProfilePicture from '@/Components/Profile/Sections/ProfilePicture.vue'

const props = defineProps({
  query: String,
  results: Array
})

const search = ref(props.query ?? '')
const sendingRequest = ref(null)
const feedbacks = ref({}) // userId: string

let debounceTimer = null

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
    console.error(error)
  } finally {
    sendingRequest.value = null
  }
}

const cancelFollow = async (userId) => {
  sendingRequest.value = userId
  try {
    await axios.delete(route('friends.unfollow', userId))

    const index = props.results.findIndex(user => user.id === userId)
    if (index !== -1) {
      props.results[index].status = null
      feedbacks.value[userId] = 'Follow annullato'
      setTimeout(() => delete feedbacks.value[userId], 3000)
    }
  } catch (error) {
    console.error(error)
  } finally {
    sendingRequest.value = null
  }
}
</script>

<template>
  <AppLayout>
    <div class="max-w-4xl mx-auto px-6 py-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-6">Cerca utenti</h1>

      <div class="mb-4">
        <Link
          :href="route('friends.index')"
          class="inline-block text-sm text-gray-700 underline hover:text-gray-900"
        >
          ← Torna alla lista amici
        </Link>
      </div>

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
          <p class="text-sm text-gray-500">@{{ user.username }}</p>

          <div class="mt-3 space-y-1">
            <button
              v-if="user.status === 'accepted'"
              @click="cancelFollow(user.id)"
              :disabled="sendingRequest === user.id"
              class="text-sm text-red-600 underline hover:text-red-800"
            >
              {{ sendingRequest === user.id ? '...' : 'Togli follow' }}
            </button>

            <button
              v-else-if="user.status === 'pending'"
              @click="cancelFollow(user.id)"
              :disabled="sendingRequest === user.id"
              class="text-sm text-orange-600 underline hover:text-orange-800"
            >
              {{ sendingRequest === user.id ? '...' : 'Annulla richiesta' }}
            </button>

            <button
              v-else
              @click="sendFollowRequest(user.id)"
              :disabled="sendingRequest === user.id"
              class="text-sm text-blue-600 underline hover:text-blue-800"
            >
              {{ sendingRequest === user.id ? '...' : 'Segui' }}
            </button>

            <p
              v-if="feedbacks[user.id]"
              class="text-xs text-green-600 mt-1"
            >
              {{ feedbacks[user.id] }}
            </p>
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
