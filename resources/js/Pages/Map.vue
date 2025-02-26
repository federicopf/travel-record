<script setup>
import { onMounted, ref, watch } from 'vue';
import { Loader } from '@googlemaps/js-api-loader';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps(['places']);
const map = ref(null);
const mapInstance = ref(null);
const markers = ref([]);


const initializeMap = async () => {
    const loader = new Loader({
        apiKey: import.meta.env.VITE_GOOGLE_MAPS_API_KEY,
        version: 'weekly',
        libraries: ['places']
    });

    try {
        const google = await loader.load();
        const center = { lat: 41.9028, lng: 12.4964 }; 

        mapInstance.value = new google.maps.Map(map.value, {
            center,
            zoom: 5,
        });

        addMarkers();
    } catch (error) {
        console.error("Errore durante il caricamento di Google Maps:", error);
    }
};

const addMarkers = () => {
    if (!mapInstance.value) return;

    markers.value.forEach(marker => marker.setMap(null));
    markers.value = [];

    props.places.forEach(place => {
        const marker = new google.maps.Marker({
            position: { lat: parseFloat(place.lat), lng: parseFloat(place.lng) },
            map: mapInstance.value,
            icon: {
                //FIXME IMPOSTARE MESSO IN PROD FS NOSTRO E NON QUESTO 
                url: 'https://icons.iconarchive.com/icons/icons-land/vista-map-markers/256/Map-Marker-Ball-Pink-icon.png',
                scaledSize: new google.maps.Size(40, 40), 
                anchor: new google.maps.Point(20, 40) 
            }, 
            title: place.name,
        });

        markers.value.push(marker);
    });

    console.log("Markers aggiunti:", markers.value);
};

onMounted(() => {
    initializeMap();
});
</script>

<template>
    <AppLayout>
        <div class="container mx-auto px-4 py-6">
            <h1 class="text-3xl font-bold text-pink-600 mb-6 text-center">Mappa dei nostri viaggi</h1>

            <div ref="map" class="w-full h-[500px] rounded-lg shadow"></div>
        </div>
    </AppLayout>
</template>
