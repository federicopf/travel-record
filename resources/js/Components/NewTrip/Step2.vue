<script setup>
import { ref, onMounted } from 'vue';
import { Loader } from '@googlemaps/js-api-loader';

const props = defineProps(['modelValue']);
const emit = defineEmits(['update:modelValue']);

const newPlace = ref('');
const placeInput = ref(null);
const isLoading = ref(false);
let autocomplete = null;

// Carica Google Maps API
const loadGoogleMaps = async () => {
    if (isLoading.value) return;
    
    isLoading.value = true;
    
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
    } finally {
        isLoading.value = false;
    }
};

// Inizializza l'autocomplete
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

                emit('update:modelValue', { 
                    ...props.modelValue, 
                    places: updatedPlaces 
                });
                newPlace.value = ''; // Resetta l'input
            }
        });
    }
};

// Rimuove un posto dall'elenco
const removePlace = (index) => {
    const updatedPlaces = props.modelValue.places.filter((_, i) => i !== index);
    emit('update:modelValue', { 
        ...props.modelValue, 
        places: updatedPlaces 
    });
};

onMounted(() => {
    loadGoogleMaps();
});
</script>

<template>
    <div class="space-y-4">
        <!-- Input per aggiungere luoghi -->
        <div>
            <label class="block mb-2 text-gray-700 font-medium">
                Aggiungi un posto da visitare
            </label>
            
            <div class="flex gap-2">
                <input 
                    ref="placeInput" 
                    v-model="newPlace" 
                    placeholder="Es. Colosseo, Roma"
                    :disabled="isLoading"
                    :class="`w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-${$colorScheme}-300 focus:border-transparent disabled:opacity-50`"
                >
            </div>
            
            <p v-if="isLoading" class="text-sm text-gray-500 mt-1">
                Caricamento mappe...
            </p>
        </div>

        <!-- Lista dei luoghi aggiunti -->
        <div v-if="modelValue.places.length > 0">
            <h3 class="text-lg font-medium text-gray-700 mb-3">
                Luoghi aggiunti ({{ modelValue.places.length }})
            </h3>
            
            <ul class="space-y-2">
                <li 
                    v-for="(place, index) in modelValue.places" 
                    :key="index" 
                    class="p-3 border border-gray-200 rounded-lg flex justify-between items-center bg-gray-50"
                >
                    <div class="flex-1">
                        <div class="font-medium text-gray-800">
                            {{ place.name }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ place.address }}
                        </div>
                        <div class="text-xs text-gray-400">
                            {{ place.lat.toFixed(4) }}, {{ place.lng.toFixed(4) }}
                        </div>
                    </div>
                    
                    <button 
                        @click="removePlace(index)" 
                        class="ml-3 text-red-500 hover:text-red-700 text-sm font-medium"
                    >
                        Rimuovi
                    </button>
                </li>
            </ul>
        </div>

        <!-- Messaggio quando non ci sono luoghi -->
        <div v-else class="text-center text-gray-500 py-4">
            <p>Nessun luogo aggiunto ancora.</p>
            <p class="text-sm">Inizia a digitare per cercare e aggiungere luoghi.</p>
        </div>
    </div>
</template>
