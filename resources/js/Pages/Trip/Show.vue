<script setup>
import { ref, onMounted, computed } from 'vue';
import { Loader } from '@googlemaps/js-api-loader';
import { Link, useForm, router, usePage } from '@inertiajs/vue3';
import { TrashIcon } from '@heroicons/vue/24/solid';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps(['trip']);
const page = usePage();

const map = ref(null);
const mapInstance = ref(null);
const placeInput = ref(null);
const user = page.props.auth?.user ?? null; 

let autocomplete = null;

const userPointerUrl = computed(() => {
    return user?.map_pointer_url ?? 'https://icons.iconarchive.com/icons/icons-land/vista-map-markers/256/Map-Marker-Ball-Pink-icon.png';
});

const form = useForm({
    name: '',
    address: '',
    lat: '',
    lng: ''
});

// Funzione per eliminare il viaggio
const deleteTrip = () => {
    if (confirm("Sei sicuro di voler eliminare questo viaggio? L'operazione non può essere annullata.")) {
        form.delete(route('trip.destroy', props.trip.id));
    }
};

// **Funzione per rimuovere un posto dalla lista frontend**
const removePlace = (placeId) => {
    if (confirm("Sei sicuro di voler eliminare questo posto?")) {
        form.delete(route('trip.place.destroy', { trip: props.trip.id, place: placeId }));
    }
};

// **Gestisce la navigazione al dettaglio del luogo**
const navigateToPlace = (placeId) => {
    router.visit(route('trip.place.show', { trip: props.trip.id, place: placeId }));
};

// **Inizializza la mappa**
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
                    url: userPointerUrl.value,
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

// **Inizializza Google Places Autocomplete e invia il luogo automaticamente**
const initializeAutocomplete = async () => {
    const loader = new Loader({
        apiKey: import.meta.env.VITE_GOOGLE_MAPS_API_KEY,
        version: 'weekly',
        libraries: ['places']
    });

    try {
        const google = await loader.load();

        if (placeInput.value) {
            autocomplete = new google.maps.places.Autocomplete(placeInput.value, {
                types: []
            });

            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();
                if (place.geometry) {
                    form.name = place.name;
                    form.address = place.formatted_address || '';
                    form.lat = place.geometry.location.lat();
                    form.lng = place.geometry.location.lng();

                    form.post(route('trip.place.addPlace', props.trip.id), {
                        onSuccess: () => {
                            form.reset();
                            placeInput.value.value = ''; 
                        }
                    });
                }
            });
        }
    } catch (error) {
        console.error("Errore nel caricamento di Google Maps:", error);
    }
};

onMounted(() => {
    initializeMap();
    initializeAutocomplete();
});
</script>

<template>
    <AppLayout>
        <div class="container mx-auto px-4 py-6">
            <div v-if="trip.image" class="mb-6">
                <img :src="trip.image" alt="Immagine non trovata!" class="w-full h-64 object-cover rounded-lg shadow-lg">
            </div>

            <h1 class="text-3xl font-bold text-gray-700 mb-6">{{ trip.title }}</h1>

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
                    @click.stop="navigateToPlace(place.id)"> 

                    <!-- Pulsante di eliminazione -->
                    <button @click.stop="removePlace(place.id)"
                        class="absolute top-2 right-2 bg-red-500 text-white p-2 rounded-full shadow-lg hover:bg-red-600 transition flex items-center justify-center z-10">
                        <TrashIcon class="w-6 h-6" />
                    </button>

                    <div class="relative">
                        <img v-if="place.photos.length > 0" :src="place.photos[0].path" class="w-full h-40 object-cover">
                        <div v-else class="w-full h-40 bg-gray-300 flex items-center justify-center">
                            <span class="text-gray-500">Nessuna immagine</span>
                        </div>
                    </div>

                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800">{{ place.name }}</h3>
                        <p class="text-gray-600 text-sm mt-1">{{ place.address }}</p>
                    </div>
                </div>
            </div>

            <!-- Aggiungi Luogo -->
            <div class="mt-6">
                <h2 class="text-2xl font-bold text-gray-700 mb-2">Aggiungi un nuovo posto</h2>

                <div class="flex gap-2">
                    <input ref="placeInput" type="text" placeholder="Cerca un luogo..." 
                        class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-300">
                    
                    <button :class="`bg-${$colorScheme}-500 text-white px-4 py-2 rounded shadow-lg hover:bg-${$colorScheme}-600 transition`">
                        +
                    </button>
                </div>
            </div>

            <hr class="mt-10 border-black"> 
            
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
