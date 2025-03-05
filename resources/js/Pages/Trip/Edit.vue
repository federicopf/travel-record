<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { XMarkIcon, StarIcon } from '@heroicons/vue/24/solid';
import { Link } from '@inertiajs/vue3';

import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps(['trip']);

const form = useForm({
    title: props.trip.title || '',
    start_date: props.trip.start_date || '',
    end_date: props.trip.end_date || '',
    places: props.trip.places.map(place => ({
        id: place.id,
        name: place.name,
        lat: place.lat,
        lng: place.lng,
        photos: place.photos.map(photo => ({
            id: photo.id,
            path: photo.path,
            is_favorite: photo.path === props.trip.image
        }))
    })),
    newPhotos: {},
    deletedPhotos: [],
    favorite_photo: props.trip.image || null,
});

const fileInputs = ref({});

// Gestisce il caricamento delle immagini
const handleFileUpload = (event, placeIndex) => {
    console.log(placeIndex);
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
                isVideo: file.type.startsWith("video"), 
                is_favorite: false 
            });
        };
        reader.readAsDataURL(file);
    });

    if (fileInputs.value[placeIndex]) {
        fileInputs.value[placeIndex].value = "";
    }
};



// Rimuove un’immagine esistente o nuova
const removePhoto = (placeIndex, photoIndex, isNew = false) => {
    if (isNew) {
        form.newPhotos[placeIndex].splice(photoIndex, 1);
    } else {
        const removedPhoto = form.places[placeIndex].photos.splice(photoIndex, 1)[0];
        form.deletedPhotos.push(removedPhoto.id);
    }
};

const setFavorite = (photo, isNew) => {
    const photoPath = isNew ? photo.file.name : photo.path;

    // Controlla che photoPath sia definito prima di usare match()
    if (!photoPath || photoPath.match(/\.(mp4|mov|avi)$/i)) {
        alert("Non puoi selezionare un video come immagine preferita.");
        return;
    }

    form.favorite_photo = photoPath;

    // Resetta lo stato di is_favorite per tutte le immagini
    form.places.forEach(place => {
        place.photos.forEach(p => p.is_favorite = false);
    });

    Object.values(form.newPhotos).forEach(photoList => {
        photoList.forEach(p => p.is_favorite = false);
    });

    // Imposta l'immagine selezionata come preferita
    photo.is_favorite = true;
};


const triggerFileInput = (placeIndex) => {
    if (fileInputs.value[placeIndex]) {
        fileInputs.value[placeIndex].click();
    }
};

const submit = () => {

    const formData = new FormData();

    formData.append('title', form.title);
    formData.append('start_date', form.start_date);
    formData.append('end_date', form.end_date);
    formData.append('favorite_photo', form.favorite_photo || '');

    // Aggiunge i luoghi
    form.places.forEach((place, index) => {
        formData.append(`places[${index}][id]`, place.id);
        formData.append(`places[${index}][name]`, place.name);
        formData.append(`places[${index}][lat]`, place.lat);
        formData.append(`places[${index}][lng]`, place.lng);

        // Aggiunge le foto già esistenti
        place.photos.forEach((photo, photoIndex) => {
            formData.append(`places[${index}][photos][${photoIndex}][id]`, photo.id);
            formData.append(`places[${index}][photos][${photoIndex}][path]`, photo.path);
            formData.append(`places[${index}][photos][${photoIndex}][is_favorite]`, photo.is_favorite ? 1 : 0);
        });
    });

    // Aggiunge le nuove foto caricate
    Object.keys(form.newPhotos).forEach(placeIndex => {
        const placeId = form.places[placeIndex]?.id; // Recupera l'ID reale del posto
        if (placeId) {
            form.newPhotos[placeIndex].forEach((photo, newIndex) => {
                formData.append(`newPhotos[${placeId}][${newIndex}]`, photo.file);
            });
        }
    });

    // Aggiunge le immagini eliminate
    form.deletedPhotos.forEach((photoId, index) => {
        formData.append(`deletedPhotos[${index}]`, photoId);
    });

    router.post(route('trip.update', props.trip.id), formData, {
        forceFormData: true, 
    });
};

</script>

<template>
    <AppLayout>
        <div class="container mx-auto px-4 py-6">
            <h1 :class="`text-3xl font-bold text-${$colorScheme}-600 mb-6`">Modifica Viaggio</h1>

            <div class="flex my-4">
                <Link :href="route('trip.show', trip.id)"
                    class="bg-gray-500 text-white font-semibold px-6 py-2 rounded-lg shadow-lg hover:bg-gray-600 transition duration-300">
                    Indietro
                </Link>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-lg">
                <label class="block mb-2 text-gray-700">Destinazione</label>
                <input v-model="form.title" type="text"
                    :class="`w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-${$colorScheme}-300`">

                <label class="block mb-2 text-gray-700">Data di Inizio</label>
                <input v-model="form.start_date" type="date"
                    :class="`w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-${$colorScheme}-300`">

                <label class="block mb-2 text-gray-700">Data di Fine</label>
                <input v-model="form.end_date" type="date"
                    :class="`w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-${$colorScheme}-300`">

                <h2 class="text-xl font-bold text-gray-700 mt-6">Luoghi visitati</h2>
                <ul class="mt-4">
                    <li v-for="(place, placeIndex) in form.places" :key="place.id" class="bg-gray-100 p-4 rounded-lg mb-2">
                        <h3 class="text-lg font-semibold text-gray-800">{{ place.name }}</h3>

                        <input ref="fileInputs" type="file" multiple accept="image/*, video/*" 
                            @change="handleFileUpload($event, placeIndex)"
                            class="hidden">

                        <button @click="triggerFileInput(placeIndex)"
                            :class="`mt-2 px-4 py-2 bg-${$colorScheme}-600 text-white rounded-lg hover:bg-${$colorScheme}-700 transition flex items-center gap-2`">
                            Aggiungi Immagini
                        </button>

                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-2">
                            <div v-for="(photo, photoIndex) in place.photos" :key="photo.id" class="relative">
                                <!-- Mostra immagine -->
                                <img v-if="!photo.path.match(/\.(mp4|mov|avi)$/i)" :src="photo.path" class="w-full h-40 object-cover rounded-lg">
                                
                                <!-- Mostra video -->
                                <video v-else controls class="w-full h-40 rounded-lg shadow">
                                    <source :src="photo.path" type="video/mp4">
                                    Il tuo browser non supporta il tag video.
                                </video>

                                <!-- Bottone per impostare immagine preferita (solo immagini) -->
                                <button v-if="!photo.path.match(/\.(mp4|mov|avi)$/i)" @click="setFavorite(photo, false)"
                                    class="absolute top-2 left-2 bg-white p-1 rounded-full shadow">
                                    <StarIcon :class="photo.is_favorite ? 'text-yellow-500' : 'text-gray-400'" class="w-6 h-6" />
                                </button>

                                <!-- Bottone per rimuovere immagine o video -->
                                <button @click="removePhoto(placeIndex, photoIndex, false)"
                                    class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full shadow">
                                    <XMarkIcon class="w-5 h-5" />
                                </button>
                            </div>

                            <div v-for="(newPhoto, newIndex) in form.newPhotos[placeIndex]" :key="newIndex" class="relative">
                                <!-- Mostra immagine caricata -->
                                <img v-if="!newPhoto.file.type.startsWith('video')" :src="newPhoto.preview" class="w-full h-40 object-cover rounded-lg">

                                <!-- Mostra video caricato -->
                                <video v-else controls class="w-full h-40 rounded-lg shadow">
                                    <source :src="newPhoto.preview" type="video/mp4">
                                    Il tuo browser non supporta il tag video.
                                </video>

                                <!-- Bottone per impostare immagine preferita (solo immagini) -->
                                <button v-if="!newPhoto.file.type.startsWith('video')" 
                                        @click="setFavorite(newPhoto, true)"
                                        class="absolute top-2 left-2 bg-white p-1 rounded-full shadow">
                                    <StarIcon :class="newPhoto.is_favorite ? 'text-yellow-500' : 'text-gray-400'" class="w-6 h-6" />
                                </button>

                                <!-- Bottone per rimuovere immagine o video -->
                                <button @click="removePhoto(placeIndex, newIndex, true)"
                                        class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full shadow">
                                    <XMarkIcon class="w-5 h-5" />
                                </button>
                            </div>
                        </div>

                    </li>
                </ul>

                <div class="flex justify-between mt-6">
                    <button @click="submit"
                        :class="`px-4 py-2 bg-${$colorScheme}-600 text-white rounded hover:bg-${$colorScheme}-700 transition`">
                        Salva Modifiche
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
