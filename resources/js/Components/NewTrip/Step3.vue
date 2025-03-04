<script setup>
import { ref, computed } from 'vue';
import { XMarkIcon, ChevronLeftIcon, ChevronRightIcon, StarIcon } from '@heroicons/vue/24/solid';

const props = defineProps(['modelValue']);
const emit = defineEmits(['update:modelValue']);

const selectedPlace = ref('');
const fileInput = ref(null);
const currentPhotoIndex = ref(0);
const favoritePhoto = ref(null);
const errorMessage = ref(null);

const selectedPlaceData = computed(() => {
    return props.modelValue.places.find(p => p.name === selectedPlace.value) || null;
});

const handleFileUpload = (event) => {
    if (!selectedPlaceData.value) {
        errorMessage.value = "Seleziona prima un posto!";
        return;
    }

    errorMessage.value = null;

    const files = Array.from(event.target.files);

    if (selectedPlaceData.value.photos.length + files.length > 30) {
        alert("Puoi caricare massimo 30 file per ogni posto.");
        return;
    }

    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
            selectedPlaceData.value.photos.push({ 
                file, 
                preview: e.target.result, 
                name: file.name,
                type: file.type.startsWith("image") ? "image" : "video", // ✅ Distinzione tra immagini e video
                is_favorite: false
            });
            emit('update:modelValue', props.modelValue);
        };
        reader.readAsDataURL(file);
    });

    fileInput.value.value = "";
};

const removePhoto = () => {
    if (selectedPlaceData.value && selectedPlaceData.value.photos.length > 0) {
        selectedPlaceData.value.photos.splice(currentPhotoIndex.value, 1);
        emit('update:modelValue', props.modelValue);

        if (currentPhotoIndex.value >= selectedPlaceData.value.photos.length) {
            currentPhotoIndex.value = Math.max(0, selectedPlaceData.value.photos.length - 1);
        }
    }
};

const nextPhoto = () => {
    if (selectedPlaceData.value && currentPhotoIndex.value < selectedPlaceData.value.photos.length - 1) {
        currentPhotoIndex.value++;
    }
};

const prevPhoto = () => {
    if (selectedPlaceData.value && currentPhotoIndex.value > 0) {
        currentPhotoIndex.value--;
    }
};

const setFavorite = () => {
    if (!selectedPlaceData.value) return;

    const currentMedia = selectedPlaceData.value.photos[currentPhotoIndex.value];

    if (currentMedia.type === "video") {
        alert("Non puoi selezionare un video come preferito.");
        return;
    }

    props.modelValue.places.forEach(place => {
        place.photos.forEach(photo => {
            photo.is_favorite = false;
        });
    });

    currentMedia.is_favorite = true;
    favoritePhoto.value = currentMedia.name;

    emit('update:modelValue', {
        ...props.modelValue,
        favorite_photo: favoritePhoto.value
    });
};
</script>

<template>
    <div>
        <label class="block mb-2 text-gray-700">Seleziona un posto</label>
        <select v-model="selectedPlace" :class="`w-full p-2 border rounded focus:ring-2 focus:ring-${$colorScheme}-300`">
            <option v-for="place in modelValue.places" :key="place.name" :value="place.name">
                {{ place.name }}
            </option>
        </select>

        <p v-if="errorMessage" class="text-red-500 text-sm mt-2">{{ errorMessage }}</p>

        <label class="block mt-4 mb-2 text-gray-700">Carica immagini o video (max 30 per posto)</label>
        <input ref="fileInput" type="file" multiple accept="image/*,video/*" @change="handleFileUpload"
            :class="`w-full p-2 border rounded focus:ring-2 focus:ring-${$colorScheme}-300`">

        <div v-if="selectedPlaceData && selectedPlaceData.photos.length > 0" class="mt-6 text-center">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Anteprima file</h3>

            <div class="relative w-full max-w-md mx-auto bg-gray-200 rounded-lg overflow-hidden shadow-lg">
                <img v-if="selectedPlaceData.photos[currentPhotoIndex].type === 'image'"
                    :src="selectedPlaceData.photos[currentPhotoIndex].preview" alt="Anteprima"
                    class="w-full h-64 object-cover">

                <video v-else controls class="w-full h-64">
                    <source :src="selectedPlaceData.photos[currentPhotoIndex].preview" type="video/mp4">
                    Il tuo browser non supporta il tag video.
                </video>

                <button @click="prevPhoto" class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-gray-700 text-white p-2 rounded-full shadow">
                    <ChevronLeftIcon class="w-6 h-6" />
                </button>

                <button @click="nextPhoto" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-gray-700 text-white p-2 rounded-full shadow">
                    <ChevronRightIcon class="w-6 h-6" />
                </button>

                <button v-if="selectedPlaceData.photos[currentPhotoIndex].type === 'image'" @click="setFavorite"
                    class="absolute top-2 left-2 p-2 rounded-full transition"
                    :class="selectedPlaceData.photos[currentPhotoIndex].is_favorite ? 'text-black' : 'text-gray-400'">
                    <StarIcon class="w-6 h-6" />
                </button>

                <button @click="removePhoto"
                    class="absolute top-2 right-2 bg-red-500 text-white p-2 rounded-full shadow">
                    <XMarkIcon class="w-5 h-5" />
                </button>
            </div>
        </div>
    </div>
</template>
