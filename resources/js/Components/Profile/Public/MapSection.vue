<script setup>
import { ref, onMounted, nextTick, computed } from 'vue'
import { Loader } from '@googlemaps/js-api-loader'
import { usePage } from '@inertiajs/vue3';

const page = usePage()
const username = page.props.user?.username

const props = defineProps({
  places: Array
})

const map = ref(null)
const mapInstance = ref(null)
const infoWindow = ref(null)
const markers = ref([])

const userPointerUrl = computed(() => {
    return page.props.auth?.user?.map_pointer_url ?? 'https://icons.iconarchive.com/icons/icons-land/vista-map-markers/256/Map-Marker-Ball-Pink-icon.png';
});

const initializeMap = async () => {
  await nextTick()

  if (!map.value) return

  const loader = new Loader({
    apiKey: import.meta.env.VITE_GOOGLE_MAPS_API_KEY,
    version: 'weekly',
    libraries: ['places']
  })

  try {
    const google = await loader.load()

    const center = { lat: 41.9028, lng: 12.4964 }

    mapInstance.value = new google.maps.Map(map.value, {
      center,
      zoom: 5
    })

    infoWindow.value = new google.maps.InfoWindow()

    addMarkers(google)
  } catch (error) {
    console.error('Errore durante il caricamento di Google Maps:', error)
  }
}

const addMarkers = (google) => {
  if (!mapInstance.value) return

  markers.value.forEach(marker => marker.setMap(null))
  markers.value = []

  props.places.forEach(place => {
    const marker = new google.maps.Marker({
      position: { lat: parseFloat(place.lat), lng: parseFloat(place.lng) },
      map: mapInstance.value,
      icon: {
        url: userPointerUrl.value, // Usa il Map Pointer dell'utente
        scaledSize: new google.maps.Size(40, 40),
        anchor: new google.maps.Point(20, 40)
      },
      title: place.name
    })

    marker.addListener('click', () => {
      let images = ''
      if (place.images && place.images.length > 0) {
        images = `
          <div style="display:flex;gap:4px;justify-content:center;margin-top:8px;">
            ${place.images.map(img => `<img src="${img}" style="width:70px;height:70px;object-fit:cover;border-radius:6px;">`).join('')}
          </div>`
      }

      const content = `
        <div style="max-width:220px;padding:10px;font-family:sans-serif;">
          <h3 style="margin:0;font-size:16px;color:#d63384;text-align:center;">${place.trip}</h3>
          <p style="text-align:center;margin:4px 0;font-size:14px;"><strong>${place.name}</strong></p>
          <p style="text-align:center;font-size:12px;color:#555;">Dal ${place.start_date} al ${place.end_date}</p>
          ${images}
          <div style="text-align:center;margin-top:8px;">
            <a href="`+route('profile.public.trip', { username, trip: place.trip_id })+`" style="background-color:#d63384;color:white;padding:6px 12px;border-radius:4px;text-decoration:none;font-size:13px;">Vai al viaggio</a>
          </div>
        </div>
      `
      infoWindow.value.setContent(content)
      infoWindow.value.open(mapInstance.value, marker)
    })

    markers.value.push(marker)
  })
}

onMounted(() => {
  initializeMap()
})
</script>

<template>
  <div>
    <h2 class="text-xl font-semibold text-gray-700 mb-2">Mappa dei viaggi</h2>
    <div ref="map" class="w-full h-[500px] rounded shadow border" />
  </div>
</template>
