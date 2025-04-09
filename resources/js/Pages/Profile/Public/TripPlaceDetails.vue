<script setup>
import { onMounted, ref, computed, nextTick } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Loader } from '@googlemaps/js-api-loader'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  place: Object,
  user: Object,
  trip: Object
})

const map = ref(null)
const mapInstance = ref(null)

const userPointerUrl = computed(() => {
  return props.place.map_pointer_url ?? 'https://icons.iconarchive.com/icons/icons-land/vista-map-markers/256/Map-Marker-Ball-Pink-icon.png'
})

const initializeMap = async () => {
  await nextTick()

  if (!map.value || !props.place.lat || !props.place.lng) return

  const loader = new Loader({
    apiKey: import.meta.env.VITE_GOOGLE_MAPS_API_KEY,
    version: 'weekly',
    libraries: ['places']
  })

  try {
    const google = await loader.load()

    const position = {
      lat: parseFloat(props.place.lat),
      lng: parseFloat(props.place.lng)
    }

    mapInstance.value = new google.maps.Map(map.value, {
      center: position,
      zoom: 10
    })

    new google.maps.Marker({
      position,
      map: mapInstance.value,
      icon: {
        url: userPointerUrl.value,
        scaledSize: new google.maps.Size(40, 40),
        anchor: new google.maps.Point(20, 40)
      },
      title: props.place.name
    })
  } catch (error) {
    console.error('Errore Google Maps:', error)
  }
}

onMounted(() => {
  initializeMap()
})
</script>

<template>
    <AppLayout>
      <div class="max-w-4xl mx-auto px-6 py-8 space-y-8">
        <div class="flex items-center justify-between">
          <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-6 h-6 text-pink-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.1 2 5 5.1 5 9c0 5.2 7 13 7 13s7-7.8 7-13c0-3.9-3.1-7-7-7zm0 9.5c-1.4 0-2.5-1.1-2.5-2.5S10.6 6.5 12 6.5s2.5 1.1 2.5 2.5S13.4 11.5 12 11.5z"/></svg>
            {{ place.name }}
          </h1>
          <Link
            :href="route('profile.public.trip', { username: user.username, trip: trip.id })"
            class="text-sm bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition"
          >
            ← Torna al viaggio
          </Link>
        </div>
  
        <p class="text-gray-600 text-sm">{{ place.address }}</p>
  
        <div ref="map" class="w-full h-[350px] rounded-lg shadow border" />
  
        <div v-if="place.hashtags.length" class="flex flex-wrap gap-2 mt-2">
          <span
            v-for="tag in place.hashtags"
            :key="tag.id"
            class="text-xs font-semibold px-3 py-1 rounded-full"
            :style="{
              backgroundColor: tag.color + '22',
              color: tag.color
            }"
          >
            #{{ tag.name }}
          </span>
        </div>
  
        <div v-if="place.photos.length" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <img
            v-for="(photo, i) in place.photos"
            :key="i"
            :src="photo"
            alt="Foto del luogo"
            class="rounded-xl w-full h-40 object-cover shadow-sm hover:shadow-lg transition"
          />
        </div>
      </div>
    </AppLayout>
  </template>
  
