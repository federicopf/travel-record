<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps(['modelValue']);
const emit = defineEmits(['update:modelValue']);

const newPlace = ref('');
const placeInput = ref(null);

onMounted(() => {
    if (window.google) {
        const autocomplete = new window.google.maps.places.Autocomplete(placeInput.value, {
            types: ['geocode'], 
        });

        autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace();
            if (place.geometry) {
                props.modelValue.places.push({
                    name: place.name,
                    address: place.formatted_address,
                    lat: place.geometry.location.lat(),
                    lng: place.geometry.location.lng(),
                    photos: []
                });

                emit('update:modelValue', props.modelValue);
                newPlace.value = ''; // Resetta l'input
            }
        });
    }
});
</script>

<template>
    <div>
        <label class="block mb-2 text-gray-700">Aggiungi un posto da visitare</label>

        <div class="flex gap-2">
            <input ref="placeInput" v-model="newPlace" placeholder="Es. Colosseo, Roma"
                class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-pink-300">

            <button class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                +
            </button>
        </div>

        <ul class="mt-4">
            <li v-for="(place, index) in modelValue.places" :key="index" class="p-2 border-b text-gray-700">
                <strong>{{ place.name }}</strong> ({{ place.lat }}, {{ place.lng }})
            </li>
        </ul>
    </div>
</template>
