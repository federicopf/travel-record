<script setup>
const props = defineProps({
  trips: Array
})
</script>

<template>
  <div class="mt-10">
    <h2 class="text-xl font-semibold text-gray-800 mb-4">Viaggi pubblici</h2>

    <div v-if="trips.length" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <div
        v-for="trip in trips"
        :key="trip.id"
        class="bg-white shadow rounded-lg overflow-hidden"
      >
        <img
          v-if="trip.image"
          :src="trip.image"
          alt="Copertina viaggio"
          class="w-full h-48 object-cover"
        />
        <div v-else class="h-48 bg-gray-200 flex items-center justify-center italic text-gray-500">
          Nessuna immagine
        </div>

        <div class="p-4">
          <h3 class="text-lg font-bold text-gray-800">{{ trip.title }}</h3>
          <p class="text-sm text-gray-500 mb-2">
            {{ trip.start_date }} – {{ trip.end_date }}
          </p>

          <div v-if="trip.hashtags.length" class="flex flex-wrap gap-2 mt-2">
            <span
              v-for="tag in trip.hashtags"
              :key="tag.id"
              class="text-xs px-2 py-1 rounded-full font-medium text-white"
              :style="{ backgroundColor: tag.color }"
            >
              #{{ tag.name }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <p v-else class="text-gray-500 text-sm italic">Nessun viaggio pubblico disponibile.</p>
  </div>
</template>
