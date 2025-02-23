<script setup>
import { ref } from 'vue';

const props = defineProps(['modelValue']);
const emit = defineEmits(['update:modelValue']);

const selectedPlace = ref('');
const fileInput = ref(null);

// Aggiungi immagini al posto selezionato
const handleFileUpload = (event) => {
    if (!selectedPlace.value) return;

    const place = props.modelValue.places.find(p => p.name === selectedPlace.value);
    if (!place) return;

    const files = Array.from(event.target.files);

    // Limita a massimo 10 immagini per posto
    if (place.photos.length + files.length > 10) {
        alert("Puoi caricare massimo 10 immagini per ogni posto.");
        return;
    }

    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
            place.photos.push({ file, preview: e.target.result });
            emit('update:modelValue', props.modelValue);
        };
        reader.readAsDataURL(file);
    });

    // Resetta il file input per consentire il caricamento multiplo
    fileInput.value.value = "";
};

// Rimuove un'immagine caricata
const removePhoto = (placeIndex, photoIndex) => {
    props.modelValue.places[placeIndex].photos.splice(photoIndex, 1);
    emit('update:modelValue', props.modelValue);
};
</script>

<template>
    <div>
        <label class="block mb-2 text-gray-700">Seleziona un posto</label>
        <select v-model="selectedPlace" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-pink-300">
            <option v-for="place in modelValue.places" :key="place.name" :value="place.name">
                {{ place.name }}
            </option>
        </select>

        <label class="block mt-4 mb-2 text-gray-700">Carica immagini (max 10 per posto)</label>
        <input ref="fileInput" type="file" multiple accept="image/*" @change="handleFileUpload"
            class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-pink-300">

        <div class="mt-4">
            <h3 class="text-lg font-semibold text-gray-800">Anteprima immagini</h3>
            <div v-for="(place, placeIndex) in modelValue.places" :key="placeIndex">
                <h4 class="text-pink-600 font-bold mt-2">{{ place.name }}</h4>
                <div class="grid grid-cols-3 gap-2 mt-2">
                    <div v-for="(photo, photoIndex) in place.photos" :key="photoIndex"
                        class="relative w-full h-24 bg-gray-200 rounded-lg overflow-hidden shadow">
                        <img :src="photo.preview" alt="Anteprima" class="w-full h-full object-cover">
                        <button @click="removePhoto(placeIndex, photoIndex)"
                            class="absolute top-0 right-0 bg-red-500 text-white px-2 py-1 text-xs rounded-bl">
                            X
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
