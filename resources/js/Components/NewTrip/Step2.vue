<script setup>
import { ref, onMounted } from 'vue';
import { Loader } from '@googlemaps/js-api-loader';

const props = defineProps(['modelValue']);
const emit = defineEmits(['update:modelValue']);

const newPlace = ref('');
const placeInput = ref(null);
let autocomplete = null;

const loadGoogleMaps = async () => {
    try {
        const loader = new Loader({
            apiKey: import.meta.env.VITE_GOOGLE_MAPS_API_KEY,
            version: 'weekly',
            libraries: ['places']
        });

        const google = await loader.load();
        initAutocomplete(google);
    } catch (error) {
        console.error("Errore nel caricamento di Google Maps:", error);
    }
};

const initAutocomplete = (google) => {
    if (placeInput.value) {
        autocomplete = new google.maps.places.Autocomplete(placeInput.value, {
            types: []
        });

        autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace();
            if (place.geometry) {
                const updatedPlaces = [...props.modelValue.places, {
                    name: place.name,
                    address: place.formatted_address,
                    lat: place.geometry.location.lat(),
                    lng: place.geometry.location.lng(),
                    photos: []
                }];

                emit('update:modelValue', { ...props.modelValue, places: updatedPlaces });
                newPlace.value = ''; // Resetta l'input
            }
        });
    }
};

// Funzione per rimuovere un posto dall'elenco
const removePlace = (index) => {
    const updatedPlaces = props.modelValue.places.filter((_, i) => i !== index);
    emit('update:modelValue', { ...props.modelValue, places: updatedPlaces });
};

onMounted(() => {
    loadGoogleMaps();
});
</script>

<template>
    <div>
        <label class="block mb-2 text-gray-700">Aggiungi un posto da visitare</label>

        <div class="flex gap-2">
            <input ref="placeInput" v-model="newPlace" placeholder="Es. Colosseo, Roma"
                :class="`w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-${$colorScheme}-300`">
        </div>

        <ul class="mt-4">
            <li v-for="(place, index) in modelValue.places" :key="index" class="p-2 border-b flex justify-between items-center">
                <span>
                    <strong>{{ place.name }}</strong> ({{ place.lat }}, {{ place.lng }})
                </span>
                <button @click="removePlace(index)" class="text-red-500 hover:text-red-700 text-sm">Rimuovi</button>
            </li>
        </ul>
    </div>
</template>
