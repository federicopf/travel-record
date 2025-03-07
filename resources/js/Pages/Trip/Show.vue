<script setup>
import { ref, onMounted } from 'vue';
import { Loader } from '@googlemaps/js-api-loader';
import { Link, useForm } from '@inertiajs/vue3';

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
            <h2 class="text-2xl font-bold text-gray-700 mt-6">Luoghi visitati</h2>
            <ul class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <li v-for="place in trip.places" :key="place.id" class="bg-gray-100 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-800">{{ place.name }}</h3>

                    <!-- Controllo se ci sono immagini/video -->
                    <div v-if="place.photos.length > 0">
                        <swiper
                            :modules="[Navigation, Pagination]"
                            :navigation="true"
                            :pagination="{ clickable: true }"
                            class="my-4 rounded-lg shadow-lg">
                            
                            <swiper-slide v-for="(photo, index) in place.photos" :key="photo.id">
                                <!-- Mostra immagine se è un'immagine -->
                                <img 
                                    v-if="photo.path.match(/\.(jpeg|jpg|png|webp)$/i)"
                                    :src="photo.path" 
                                    class="w-full h-60 object-cover rounded-lg cursor-pointer"
                                    @click="openFullscreenCarousel(place.photos, index)"
                                >

                                <!-- Mostra video se è un video -->
                                <video 
                                    v-else controls 
                                    class="w-full h-60 rounded-lg shadow cursor-pointer"
                                    @click="openFullscreenCarousel(place.photos, index)">
                                    <source :src="photo.path" type="video/mp4">
                                    Il tuo browser non supporta il tag video.
                                </video>
                            </swiper-slide>
                        </swiper>
                    </div>

                    <!-- Messaggio alternativo se non ci sono media -->
                    <div v-else class="my-4 p-4 bg-red-100 text-red-700 text-center rounded-lg">
                        Nessun media per questo posto! Male...
                    </div>
                </li>
            </ul>

            <!-- Bottone per eliminare il viaggio -->
            <div class="mt-10 flex justify-center">
                <button 
                    @click="deleteTrip"
                    class="bg-gray-500 text-white font-semibold px-6 py-2 rounded-lg shadow-lg hover:bg-gray-600 transition duration-300">
                    Elimina Viaggio
                </button>
            </div>
        </div>

        <!-- Modal per Carosello Fullscreen -->
        <div v-if="showFullscreen" class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-90 flex justify-center items-center z-50">
            <div class="absolute top-4 right-4 text-white text-3xl cursor-pointer z-50" @click="closeFullscreenCarousel">×</div>

            <swiper
                :modules="[Navigation, Pagination]"
                :navigation="true"
                :pagination="{ clickable: true }"
                :initial-slide="currentIndex"
                class="w-full h-full">
                
                <swiper-slide v-for="media in fullscreenMedia" :key="media.id" class="flex justify-center items-center">
                    <!-- Mostra immagine -->
                    <img v-if="media.path.match(/\.(jpeg|jpg|png|webp)$/i)" :src="media.path" class="max-w-full max-h-full object-contain">
                    
                    <!-- Mostra video -->
                    <video v-else controls class="max-w-full max-h-full object-contain">
                        <source :src="media.path" type="video/mp4">
                        Il tuo browser non supporta il tag video.
                    </video>
                </swiper-slide>
            </swiper>
        </div>
    </AppLayout>
</template>
