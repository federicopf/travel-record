<script setup>
import { Link, usePage } from '@inertiajs/vue3'

const props = defineProps({
  trips: Array
})

const page = usePage()
const username = page.props.user?.username
</script>

<template>
  <div class="mt-10">
    <hr class="mb-6">

    <div v-if="trips.length" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <Link
        v-for="trip in trips"
        :key="trip.id"
        :href="route('profile.trip', { username, trip: trip.id })"
        class="bg-white shadow rounded-lg overflow-hidden hover:scale-[1.02] transition"
      >
        <img
          v-if="trip.image"
          :src="trip.image"
          alt="Copertina viaggio"
          class="w-full h-48 object-cover"
        />
        <div
          v-else
          class="h-48 bg-gray-200 flex items-center justify-center italic text-gray-500"
        >
          Nessuna immagine
        </div>

        <div class="p-4">
          <h3 class="text-lg font-bold text-gray-800">{{ trip.title }}</h3>
          <p class="text-sm text-gray-500 mb-2">
            {{ trip.start_date }} – {{ trip.end_date }}
          </p>

          <div
            v-if="trip.hashtags.length"
            class="flex flex-wrap gap-2 mt-2"
          >
            <span
              v-for="tag in trip.hashtags"
              :key="tag.id"
              class="text-xs font-medium text-white px-2 py-1 rounded-full"
              :style="{ backgroundColor: tag.color }"
            >
              #{{ tag.name }}
            </span>
          </div>
        </div>
      </Link>
    </div>

    <p v-else class="text-gray-500 text-sm italic text-center mt-4">
      Nessun viaggio pubblico disponibile.
    </p>
  </div>
</template>
