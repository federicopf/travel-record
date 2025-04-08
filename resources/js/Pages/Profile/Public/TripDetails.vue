<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onMounted, computed } from 'vue'
import { Loader } from '@googlemaps/js-api-loader'
import { usePage, Link } from '@inertiajs/vue3'
import TripHashtagSummary from '@/Components/Trip/TripHashtagSummary.vue'

const props = defineProps({
  trip: Object,
})

const map = ref(null)
const mapInstance = ref(null)
const user = usePage().props.auth?.user ?? null

const userPointerUrl = computed(() => {
  return user?.map_pointer_url ?? 'https://icons.iconarchive.com/icons/icons-land/vista-map-markers/256/Map-Marker-Ball-Pink-icon.png';
})

const initializeMap = async () => {
  const loader = new Loader({
    apiKey: import.meta.env.VITE_GOOGLE_MAPS_API_KEY,
    version: 'weekly',
    libraries: ['places']
  })

  try {
    const google = await loader.load()

    const center = props.trip.places.length > 0
      ? { lat: parseFloat(props.trip.places[0].lat), lng: parseFloat(props.trip.places[0].lng) }
      : { lat: 41.9028, lng: 12.4964 }

    mapInstance.value = new google.maps.Map(map.value, {
      center,
      zoom: 5
    })

    props.trip.places.forEach(place => {
      new google.maps.Marker({
        position: { lat: parseFloat(place.lat), lng: parseFloat(place.lng) },
        map: mapInstance.value,
        icon: {
          url: userPointerUrl.value,
          scaledSize: new google.maps.Size(40, 40),
          anchor: new google.maps.Point(20, 40)
        },
        title: place.name
      })
    })

  } catch (error) {
    console.error('Errore durante il caricamento della mappa:', error)
  }
}

onMounted(() => {
  initializeMap()
})
</script>

<template>
  <AppLayout>
    <div class="container mx-auto px-4 py-6">
      <div v-if="trip.image" class="mb-6">
        <img :src="trip.image" alt="Copertina" class="w-full h-64 object-cover rounded-lg shadow-lg">
      </div>

      <div class="flex my-4">
        <Link :href="route('profile.public', trip.username)" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
          Indietro
        </Link>
      </div>

      <h1 class="text-3xl font-bold text-gray-700 mb-4">{{ trip.title }}</h1>
      <p class="text-gray-600">Dal {{ trip.start_date }} al {{ trip.end_date }}</p>

      <div ref="map" class="w-full h-[400px] rounded-lg shadow my-6" />

      <TripHashtagSummary :places="trip.places" />

      <h2 class="text-2xl font-bold text-gray-700 mt-6 mb-4">Luoghi visitati</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div
          v-for="place in trip.places"
          :key="place.id"
          class="bg-white shadow-lg rounded-lg overflow-hidden"
        >
          <img v-if="place.photos.length" :src="place.firstPhoto" class="w-full h-40 object-cover">
          <div v-else class="w-full h-40 bg-gray-300 flex items-center justify-center text-gray-500">
            Nessuna immagine
          </div>

          <div class="p-4">
            <h3 class="text-lg font-semibold text-gray-800">{{ place.name }}</h3>
            <p class="text-gray-600 text-sm">{{ place.address }}</p>
            <div class="flex flex-wrap gap-1 mt-2">
              <span
                v-for="hashtag in place.hashtags"
                :key="hashtag.id"
                :style="{ backgroundColor: hashtag.color }"
                class="text-xs text-white px-2 py-1 rounded-full"
              >
                #{{ hashtag.name }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
