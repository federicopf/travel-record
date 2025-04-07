<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import axios from 'axios'

const props = defineProps({
  friends: Array,
  requests_count: Number
})

const sendingRequest = ref(null)
const feedbacks = ref({}) // userId: string

const cancelFollow = async (userId) => {
  sendingRequest.value = userId
  try {
    await axios.delete(route('friends.unfollow', userId))

    const index = props.friends.findIndex(user => user.id === userId)
    if (index !== -1) {
      props.friends.splice(index, 1)
      feedbacks.value[userId] = 'Follow rimosso'
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
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">I tuoi amici</h1>
        <div class="flex gap-3">
          <Link
            :href="route('friends.search')"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition text-sm"
          >
            Cerca amici
          </Link>
          <Link
            :href="route('friends.requests')"
            class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600 transition text-sm"
          >
            Vedi richieste
            <span v-if="requests_count > 0" class="ml-1 bg-white text-orange-600 rounded-full px-2 text-xs font-bold">
              {{ requests_count }}
            </span>
          </Link>
        </div>
      </div>

      <div v-if="friends.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <div
          v-for="friend in friends"
          :key="friend.id"
          class="bg-white shadow rounded-lg p-4 text-center relative"
        >
          <h2 class="text-lg font-semibold text-gray-800">{{ friend.name }}</h2>
          <p class="text-sm text-gray-500">@{{ friend.username }}</p>

          <button
            @click="cancelFollow(friend.id)"
            :disabled="sendingRequest === friend.id"
            class="mt-2 text-sm text-red-600 underline hover:text-red-800"
          >
            {{ sendingRequest === friend.id ? '...' : 'Togli follow' }}
          </button>

          <p
            v-if="feedbacks[friend.id]"
            class="text-xs text-green-600 mt-1"
          >
            {{ feedbacks[friend.id] }}
          </p>
        </div>
      </div>

      <p v-else class="text-gray-500 mt-6 text-center">
        Non stai ancora seguendo nessuno.
      </p>
    </div>
  </AppLayout>
</template>
