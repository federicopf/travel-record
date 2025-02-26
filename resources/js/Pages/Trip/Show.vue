<script setup>

import { ref, computed } from 'vue';
import { onMounted } from 'vue';
import { Loader } from '@googlemaps/js-api-loader';
import { Link } from '@inertiajs/vue3';

import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps(['trip']);
const map = ref(null);
const mapInstance = ref(null);

// Carica la mappa
const initializeMap = async () => {
    const loader = new Loader({
        apiKey: import.meta.env.VITE_GOOGLE_MAPS_API_KEY,
        version: 'weekly',
        libraries: ['places']
    });

    try {
        const google = await loader.load();

        // Centro della mappa sul primo luogo (se presente)
        const center = props.trip.places.length > 0
            ? { lat: parseFloat(props.trip.places[0].lat), lng: parseFloat(props.trip.places[0].lng) }
            : { lat: 41.9028, lng: 12.4964 }; // Roma di default

        mapInstance.value = new google.maps.Map(map.value, {
            center,
            zoom: 5,
        });

        // Aggiunge marker per ogni luogo visitato
        props.trip.places.forEach(place => {
            new google.maps.Marker({
                position: { lat: parseFloat(place.lat), lng: parseFloat(place.lng) },
                map: mapInstance.value,
                icon: {
                    //FIXME IMPOSTARE MESSO IN PROD FS NOSTRO E NON QUESTO 
                    url: 'https://icons.iconarchive.com/icons/icons-land/vista-map-markers/256/Map-Marker-Ball-Pink-icon.png',
                    scaledSize: new google.maps.Size(40, 40), 
                    anchor: new google.maps.Point(20, 40) 
                }, 
                title: place.name
            });
        });

    } catch (error) {
        console.error("Errore durante il caricamento della mappa:", error);
    }
};

onMounted(() => {
    initializeMap();
});
</script>

<template>
    <AppLayout>
        <div class="container mx-auto px-4 py-6">
            <div v-if="trip.image" class="mb-6">
                <img :src="trip.image" :alt="trip.title" class="w-full h-64 object-cover rounded-lg shadow-lg">
            </div>

            <h1 class="text-3xl font-bold text-pink-600 mb-6">{{ trip.title }}</h1>
            
            <div class="flex my-4">
                <Link :href="route('trip.edit', trip.id)"
                    class="bg-pink-500 text-white font-semibold px-6 py-2 rounded-lg shadow-lg hover:bg-pink-600 transition duration-300">
                    Modifica Viaggio
                </Link>
            </div>

            
            <p class="text-gray-600">Dal {{ trip.start_date }} al {{ trip.end_date }}</p>

            <!-- Mappa -->
            <div ref="map" class="w-full h-[400px] rounded-lg shadow my-6"></div>

            <!-- Luoghi visitati -->
            <h2 class="text-2xl font-bold text-gray-700 mt-6">Luoghi visitati</h2>
            <ul class="mt-4">
                <li v-for="place in trip.places" :key="place.id" class="bg-gray-100 p-4 rounded-lg mb-2">
                    <h3 class="text-lg font-semibold text-gray-800">{{ place.name }}</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-2">
                        <img v-for="photo in place.photos" :key="photo.id" :src="photo.path" class="w-full h-40 object-cover rounded-lg">
                    </div>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>

