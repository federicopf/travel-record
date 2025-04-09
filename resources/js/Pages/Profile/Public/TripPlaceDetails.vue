<script setup>
import { onMounted, ref, computed, nextTick } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Loader } from '@googlemaps/js-api-loader'

const props = defineProps({
  place: Object
})

const map = ref(null)
const mapInstance = ref(null)

const userPointerUrl = computed(() => {
  return props.place.map_pointer_url ??
    'https://icons.iconarchive.com/icons/icons-land/vista-map-markers/256/Map-Marker-Ball-Pink-icon.png'
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
    <div class="max-w-4xl mx-auto px-6 py-8">
      <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ place.name }}</h1>
      <p class="text-sm text-gray-600 mb-4">{{ place.address }}</p>

      <div ref="map" class="w-full h-[350px] rounded shadow mb-6" />

      <div v-if="place.photos.length" class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
        <img
          v-for="(photo, i) in place.photos"
          :key="i"
          :src="photo"
          alt="Foto del luogo"
          class="rounded-lg w-full h-40 object-cover shadow"
        />
      </div>

      <div v-if="place.hashtags.length" class="flex flex-wrap gap-2">
        <span
          v-for="tag in place.hashtags"
          :key="tag.id"
          class="text-xs font-medium text-white px-2 py-1 rounded-full"
          :style="{ backgroundColor: tag.color }"
        >
          #{{ tag.name }}
        </span>
      </div>
    </div>
  </AppLayout>
</template>
