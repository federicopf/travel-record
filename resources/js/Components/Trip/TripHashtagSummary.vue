<script setup>
import { computed } from 'vue'
import { Pie } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, ArcElement)

const props = defineProps({
  places: {
    type: Array,
    required: true
  }
})

const hashtagCounts = computed(() => {
  const countMap = new Map()

  props.places.forEach(place => {
    (place.hashtags ?? []).forEach(hashtag => {
      if (!countMap.has(hashtag.id)) {
        countMap.set(hashtag.id, { ...hashtag, count: 1 })
      } else {
        countMap.get(hashtag.id).count += 1
      }
    })
  })

  return Array.from(countMap.values()).sort((a, b) => b.count - a.count)
})

const chartData = computed(() => ({
  labels: hashtagCounts.value.map(h => `#${h.name}`),
  datasets: [
    {
      data: hashtagCounts.value.map(h => h.count),
      backgroundColor: hashtagCounts.value.map(h => h.color),
      borderWidth: 1
    }
  ]
}))
</script>

<template>
  <div class="mt-10">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Mood del viaggio</h2>

    <div v-if="hashtagCounts.length" class="relative">
      <div class="flex overflow-x-scroll no-scrollbar snap-x snap-mandatory scroll-smooth transition-all duration-300">
        
        <!-- 1. Top Hashtag -->
        <div class="snap-start shrink-0 w-full sm:w-[80%] md:w-[50%] lg:w-[25%] p-2">
          <div class="bg-white shadow rounded-lg p-4 h-full">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Top Hashtag</h3>
            <ul class="space-y-2">
              <li v-for="(h, index) in hashtagCounts.slice(0, 3)" :key="h.id" class="flex items-center gap-2">
                <span :style="{ backgroundColor: h.color }" class="w-4 h-4 rounded-full inline-block"></span>
                <span class="text-sm text-gray-800 font-medium">#{{ h.name }}</span>
                <span class="ml-auto text-gray-500 text-xs">({{ h.count }})</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- 2. Pie Chart -->
        <div class="snap-start shrink-0 w-full sm:w-[80%] md:w-[50%] lg:w-[25%] p-2">
          <div class="bg-white shadow rounded-lg p-4 h-full">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Distribuzione</h3>
            <Pie
              :data="chartData"
              :options="{ responsive: true, plugins: { legend: { display: false } } }"
            />
          </div>
        </div>

        <!-- 3. Mood per luogo -->
        <div
          v-if="props.places.some(place => place.hashtags?.length)"
          class="snap-start shrink-0 w-full sm:w-[80%] md:w-[50%] lg:w-[25%] p-2"
        >
          <div class="bg-white shadow rounded-lg p-4 h-full">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Mood per luogo</h3>
            <ul class="text-sm text-gray-700 space-y-1">
              <li v-for="place in props.places.slice(0, 3)" :key="place.id" class="flex flex-col">
                <span class="font-semibold text-gray-800">{{ place.name }}</span>
                <span v-if="place.hashtags?.length" class="text-xs text-gray-500 truncate">
                  {{ place.hashtags.map(h => `#${h.name}`).join(', ') }}
                </span>
                <span v-else class="text-xs italic text-gray-400">Nessun hashtag</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- 4. Podio -->
        <div v-if="hashtagCounts.length >= 1" class="snap-start shrink-0 w-full sm:w-[80%] md:w-[50%] lg:w-[25%] p-2">
          <div class="bg-white shadow rounded-lg p-4 h-full">
            <h3 class="text-lg font-semibold text-gray-700 mb-2 text-center">🏆 Podio Mood</h3>
            <div class="flex flex-col items-center justify-center gap-2">
              <div
                v-for="(h, index) in hashtagCounts.slice(0, 3)"
                :key="h.id"
                class="flex items-center gap-2"
              >
                <span class="text-xl">
                  {{ index === 0 ? '🥇' : index === 1 ? '🥈' : '🥉' }}
                </span>
                <span class="text-sm font-medium text-gray-800">#{{ h.name }}</span>
                <span class="text-xs text-gray-500">({{ h.count }})</span>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <p v-else class="text-gray-500 text-sm mt-6 text-center">
      Nessun hashtag ancora associato ai luoghi.
    </p>
  </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
