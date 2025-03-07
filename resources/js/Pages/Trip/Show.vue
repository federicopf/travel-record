<script setup>
import { ref, onMounted } from 'vue';
import { Loader } from '@googlemaps/js-api-loader';
import { Link, useForm, router } from '@inertiajs/vue3';

// Importa Swiper per il carosello
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { Navigation, Pagination } from 'swiper/modules';

import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps(['trip']);
const map = ref(null);
const mapInstance = ref(null);

// Stato per il carosello fullscreen
const showFullscreen = ref(false);
const fullscreenMedia = ref([]);
const currentIndex = ref(0);

// Form per eliminare il viaggio
const form = useForm({});

// Apri il carosello fullscreen con tutte le immagini e i video di un luogo
const openFullscreenCarousel = (media, index) => {
    fullscreenMedia.value = media;
    currentIndex.value = index;
    showFullscreen.value = true;
};

// Chiudi il carosello fullscreen
const closeFullscreenCarousel = () => {
    showFullscreen.value = false;
};

// Inizializza la mappa
const initializeMap = async () => {
    const loader = new Loader({
        apiKey: import.meta.env.VITE_GOOGLE_MAPS_API_KEY,
        version: 'weekly',
        libraries: ['places']
    });

    try {
        const google = await loader.load();

        const center = props.trip.places.length > 0
            ? { lat: parseFloat(props.trip.places[0].lat), lng: parseFloat(props.trip.places[0].lng) }
            : { lat: 41.9028, lng: 12.4964 };

        mapInstance.value = new google.maps.Map(map.value, {
            center,
            zoom: 5,
        });

        props.trip.places.forEach(place => {
            new google.maps.Marker({
                position: { lat: parseFloat(place.lat), lng: parseFloat(place.lng) },
                map: mapInstance.value,
                icon: {
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

// Funzione per eliminare il viaggio
const deleteTrip = () => {
    if (confirm("Sei sicuro di voler eliminare questo viaggio? L'operazione non può essere annullata.")) {
        form.delete(route('trip.destroy', props.trip.id));
    }
};

// Navigazione al dettaglio del luogo
const navigateToPlace = (placeId) => {
    router.visit(route('trip.place.show', { trip: props.trip.id, place: placeId }));
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

            <h1 :class="`text-3xl font-bold text-${$colorScheme}-600 mb-6`">{{ trip.title }}</h1>

            <div class="flex my-4">
                <Link :href="route('trip.edit', trip.id)"
                :class="`bg-${$colorScheme}-500 text-white font-semibold px-6 py-2 rounded-lg shadow-lg hover:bg-${$colorScheme}-600 transition duration-300`">
                    Modifica viaggio
                </Link>
            </div>

            <p class="text-gray-600">Dal {{ trip.start_date }} al {{ trip.end_date }}</p>

            <!-- Mappa -->
            <div ref="map" class="w-full h-[400px] rounded-lg shadow my-6"></div>

            <!-- Luoghi visitati -->
            <h2 class="text-2xl font-bold text-gray-700 mt-6 mb-4">Luoghi visitati</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div v-for="place in trip.places" :key="place.id"
                    class="relative bg-white shadow-lg rounded-lg overflow-hidden cursor-pointer transition transform hover:scale-105"
                    @click="navigateToPlace(place.id)">
                    
                    <!-- Immagine -->
                    <div class="relative">
                        <img v-if="place.photos.length > 0" :src="place.photos[0].path" class="w-full h-40 object-cover">
                        <div v-else class="w-full h-40 bg-gray-300 flex items-center justify-center">
                            <span class="text-gray-500">Nessuna immagine</span>
                        </div>
                    </div>

                    <!-- Informazioni -->
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800">{{ place.name }}</h3>
                        <p class="text-gray-600 text-sm mt-1">{{ place.address }}</p>
                    </div>
                </div>
            </div>

            <!-- Bottone per eliminare il viaggio -->
            <div class="mt-10 flex justify-center">
                <button 
                    @click="deleteTrip"
                    class="bg-gray-500 text-white font-semibold px-6 py-2 rounded-lg shadow-lg hover:bg-gray-600 transition duration-300">
                    Elimina Viaggio
                </button>
            </div>
        </div>
    </AppLayout>
</template>
