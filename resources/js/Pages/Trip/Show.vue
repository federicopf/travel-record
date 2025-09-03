<script setup>
import TripHashtagSummary from '@/Components/Trip/TripHashtagSummary.vue';
import PlaceCard from '@/Components/Trip/PlaceCard.vue';
import ActionButton from '@/Components/UI/ActionButton.vue';
import { ref, onMounted, computed } from 'vue';
import { Loader } from '@googlemaps/js-api-loader';
import { Link, useForm, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps(['trip']);
const page = usePage();
const user = page.props.auth?.user ?? null;

const map = ref(null);
const mapInstance = ref(null);
const placeInput = ref(null);
let autocomplete = null;

const userPointerUrl = computed(() => 
    user?.map_pointer_url ?? 'https://icons.iconarchive.com/icons/icons-land/vista-map-markers/256/Map-Marker-Ball-Pink-icon.png'
);

const form = useForm({
    name: '',
    address: '',
    lat: '',
    lng: ''
});

const deleteTrip = () => {
    if (confirm("Sei sicuro di voler eliminare questo viaggio? L'operazione non può essere annullata.")) {
        form.delete(route('trip.destroy', props.trip.id));
    }
};

const removePlace = (placeId) => {
    if (confirm("Sei sicuro di voler eliminare questo posto?")) {
        form.delete(route('trip.place.destroy', { trip: props.trip.id, place: placeId }));
    }
};

const navigateToPlace = (placeId) => {
    router.visit(route('trip.place.show', { trip: props.trip.id, place: placeId }));
};

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
                <img :src="trip.image" alt="Immagine del viaggio" class="w-full h-64 object-cover rounded-lg shadow-lg">
            </div>

            <div class="flex gap-4 mb-6">
                <Link 
                    :href="route('home')"
                    class="bg-gray-500 text-white font-semibold px-6 py-2 rounded-lg hover:bg-gray-600 transition"
                >
                    Indietro
                </Link>
                <Link 
                    :href="route('trip.edit', trip.id)"
                    :class="`bg-${$colorScheme}-500 text-white font-semibold px-6 py-2 rounded-lg hover:bg-${$colorScheme}-600 transition`"
                >
                    Modifica viaggio
                </Link>
            </div>

            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-700 mb-2">{{ trip.title }}</h1>
                <p class="text-gray-600">Dal {{ trip.start_date }} al {{ trip.end_date }}</p>
            </div>

            <div ref="map" class="w-full h-[400px] rounded-lg shadow mb-6"></div>

            <TripHashtagSummary :places="trip.places" />

            <div class="mt-8">
                <h2 class="text-2xl font-bold text-gray-700 mb-4">Luoghi visitati</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <PlaceCard 
                        v-for="place in trip.places" 
                        :key="place.id"
                        :place="place"
                        :trip-id="trip.id"
                        :show-delete-button="true"
                        @delete="removePlace"
                        @click="navigateToPlace"
                    />
                </div>
            </div>

            <div class="mt-8">
                <h2 class="text-2xl font-bold text-gray-700 mb-4">Aggiungi un nuovo posto</h2>
                <div class="flex gap-2">
                    <input 
                        ref="placeInput" 
                        type="text" 
                        placeholder="Cerca un luogo..." 
                        class="flex-1 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300"
                    >
                    <ActionButton size="sm">
                        +
                    </ActionButton>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-gray-300 text-center">
                <ActionButton 
                    variant="danger"
                    @click="deleteTrip"
                >
                    Elimina Viaggio
                </ActionButton>
            </div>
        </div>
    </AppLayout>
</template>
