<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  friends: Array
})
</script>

<template>
  <AppLayout>
    <div class="max-w-4xl mx-auto px-6 py-8">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">I tuoi amici</h1>
        <Link
          :href="route('friends.search')"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
        >
          Cerca amici
        </Link>
      </div>

      <div v-if="friends.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <div
          v-for="friend in friends"
          :key="friend.id"
          class="bg-white shadow rounded-lg p-4 text-center"
        >
          <img
            :src="friend.map_pointer_url ?? 'https://via.placeholder.com/80'"
            alt="Avatar"
            class="w-20 h-20 rounded-full mx-auto mb-3 object-cover"
          />
          <h2 class="text-lg font-semibold text-gray-800">{{ friend.name }}</h2>
          <p class="text-sm text-gray-500">@{{ friend.username }}</p>
        </div>
      </div>

      <p v-else class="text-gray-500 mt-6 text-center">
        Non stai ancora seguendo nessuno.
      </p>
    </div>
  </AppLayout>
</template>
