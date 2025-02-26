<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { XMarkIcon, StarIcon } from '@heroicons/vue/24/solid';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps(['trip']);

const form = useForm({
    title: props.trip.title || '',
    start_date: props.trip.start_date || '',
    end_date: props.trip.end_date || '',
    places: props.trip.places.length > 0 ? props.trip.places.map(place => ({
        id: place.id,
        name: place.name,
        lat: place.lat,
        lng: place.lng,
        photos: place.photos.map(photo => ({
            id: photo.id, path: photo.path, is_favorite: photo.path === props.trip.image
        }))
    })) : [], 
    newPhotos: {}, 
    favorite_photo: props.trip.image || null,
});

const fileInputs = ref({});

const handleFileUpload = (event, placeIndex) => {
    const files = Array.from(event.target.files);

    if (!form.newPhotos[placeIndex]) {
        form.newPhotos[placeIndex] = [];
    }

    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
            form.newPhotos[placeIndex].push({ 
                file, 
                preview: e.target.result, 
                is_favorite: false // ✅ Anche le nuove immagini possono essere preferite
            });
        };
        reader.readAsDataURL(file);
    });

    if (fileInputs.value[placeIndex]) {
        fileInputs.value[placeIndex].value = "";
    }
};


const removePhoto = (placeIndex, photoIndex, isNew = false) => {
    if (isNew) {
        form.newPhotos[placeIndex].splice(photoIndex, 1);
    } else {
        form.places[placeIndex].photos.splice(photoIndex, 1);
    }
};

const setFavorite = (photo, isNew) => {
    form.favorite_photo = isNew ? photo.preview : photo.path;

    form.places.forEach(place => {
        place.photos.forEach(p => p.is_favorite = false);
    });

    Object.values(form.newPhotos).forEach(photoList => {
        photoList.forEach(p => p.is_favorite = false);
    });

    photo.is_favorite = true;
};

const triggerFileInput = (placeIndex) => {
    if (fileInputs.value[placeIndex]) {
        fileInputs.value[placeIndex].click();
    }
};

const submit = () => {
    router.put(route('trip.update', props.trip.id), form);
};

</script>

<template>
    <AppLayout>
        <div class="container mx-auto px-4 py-6">
            <h1 class="text-3xl font-bold text-pink-600 mb-6">Modifica Viaggio</h1>

            <div class="bg-white p-6 rounded-lg shadow-lg">
                <!-- Modifica dati principali -->
                <label class="block mb-2 text-gray-700">Destinazione</label>
                <input v-model="form.title" type="text"
                    class="w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-pink-300">

                <label class="block mb-2 text-gray-700">Data di Inizio</label>
                <input v-model="form.start_date" type="date"
                    class="w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-pink-300">

                <label class="block mb-2 text-gray-700">Data di Fine</label>
                <input v-model="form.end_date" type="date"
                    class="w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-pink-300">

                <!-- Luoghi visitati -->
                <h2 class="text-xl font-bold text-gray-700 mt-6">Luoghi visitati</h2>
                <ul class="mt-4">
                    <li v-for="(place, placeIndex) in form.places" :key="place.id" class="bg-gray-100 p-4 rounded-lg mb-2">
                        <h3 class="text-lg font-semibold text-gray-800">{{ place.name }}</h3>

                        <input ref="fileInputs" type="file" multiple accept="image/*" 
                            @change="handleFileUpload($event, placeIndex)"
                            class="hidden">

                        <!-- Bottone personalizzato per il caricamento -->
                        <button @click="triggerFileInput(placeIndex)"
                            class="mt-2 px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition flex items-center gap-2">
                            Aggiungi Immagini
                        </button>

                        <!-- Anteprima immagini -->
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-2">
                            <!-- Immagini già caricate -->
                            <div v-for="(photo, photoIndex) in place.photos" :key="photo.id" class="relative">
                                <img :src="photo.path" class="w-full h-40 object-cover rounded-lg">

                                <!-- Icona per impostare come preferita -->
                                <button @click="setFavorite(photo, false)"
                                    class="absolute top-2 left-2 bg-white p-1 rounded-full shadow">
                                    <StarIcon :class="photo.is_favorite ? 'text-yellow-500' : 'text-gray-400'" class="w-6 h-6" />
                                </button>

                                <!-- Icona per eliminare l'immagine -->
                                <button @click="removePhoto(placeIndex, photoIndex, false)"
                                    class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full shadow">
                                    <XMarkIcon class="w-5 h-5" />
                                </button>
                            </div>

                            <!-- Nuove immagini caricate -->
                            <div v-for="(newPhoto, newIndex) in form.newPhotos[placeIndex]" :key="newIndex" class="relative">
                                <img :src="newPhoto.preview" class="w-full h-40 object-cover rounded-lg">

                                <!-- Icona per impostare come preferita -->
                                <button @click="setFavorite(newPhoto, true)"
                                    class="absolute top-2 left-2 bg-white p-1 rounded-full shadow">
                                    <StarIcon :class="newPhoto.is_favorite ? 'text-yellow-500' : 'text-gray-400'" class="w-6 h-6" />
                                </button>

                                <!-- Icona per eliminare le nuove immagini -->
                                <button @click="removePhoto(placeIndex, newIndex, true)"
                                    class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full shadow">
                                    <XMarkIcon class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    </li>
                </ul>

                <!-- Pulsante Salva -->
                <div class="flex justify-between mt-6">
                    <button @click="submit"
                        class="px-4 py-2 bg-pink-600 text-white rounded hover:bg-pink-700 transition">
                        Salva Modifiche
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
