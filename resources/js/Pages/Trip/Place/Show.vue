<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Loader } from '@googlemaps/js-api-loader';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps(['trip', 'place']);
const map = ref(null);
const mapInstance = ref(null);
const form = useForm({ file: null }); 

// 📌 Funzione per caricare un file (immagine o video)
const uploadFile = () => {
    form.post(route('trip.place.photo.upload', { trip: props.trip.id, place: props.place.id }), {
        onSuccess: () => form.reset('file'),
    });
};

// 📌 Funzione per eliminare un file (immagine o video)
const deleteFile = (photoId) => {
    if (confirm("Sei sicuro di voler eliminare questo file?")) {
        form.delete(route('trip.place.photo.delete', { trip: props.trip.id, place: props.place.id, photo: photoId }));
    }
};

// 📌 Funzione per impostare un'immagine come preferita
const setFavoritePhoto = (photoId) => {
    form.post(route('trip.photo.setFavorite', { trip: props.trip.id, photo: photoId }));
};

// 📌 Inizializza la mappa
const initializeMap = async () => {
    const loader = new Loader({
        apiKey: import.meta.env.VITE_GOOGLE_MAPS_API_KEY,
        version: 'weekly',
        libraries: ['places']
    });

    try {
        const google = await loader.load();
        mapInstance.value = new google.maps.Map(map.value, {
            center: { lat: parseFloat(props.place.lat), lng: parseFloat(props.place.lng) },
            zoom: 12,
        });

        new google.maps.Marker({
            position: { lat: parseFloat(props.place.lat), lng: parseFloat(props.place.lng) },
            map: mapInstance.value,
            title: props.place.name
        });

    } catch (error) {
        console.error("Errore durante il caricamento della mappa:", error);
    }
};

onMounted(() => {
    initializeMap();
});
</script>

<template>
    <AppLayout>
        <div class="container mx-auto px-4 py-6">
            <h1 class="text-3xl font-bold text-gray-700">{{ place.name }}</h1>
            <p class="text-gray-500 text-lg">{{ place.address }}</p>

            <!-- Mappa -->
            <div ref="map" class="w-full h-[400px] rounded-lg shadow my-6"></div>

            <!-- Form per caricare immagini o video -->
            <div class="my-6">
                <h2 class="text-2xl font-bold text-gray-700">Carica un file (immagine o video)</h2>
                <input type="file" @input="form.file = $event.target.files[0]" accept="image/*,video/*" class="mt-2">
                <button @click="uploadFile" class="bg-blue-500 text-white px-4 py-2 rounded mt-2">Carica</button>
            </div>

            <!-- Media -->
            <h2 class="text-2xl font-bold text-gray-700 mt-6">Media</h2>
            <div v-if="place.photos?.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div v-for="photo in place.photos" :key="photo.id" class="bg-gray-100 p-4 rounded-lg relative">
                    <!-- Se è un'immagine -->
                    <img v-if="photo.path.match(/\.(jpeg|jpg|png|webp)$/i)" :src="photo.path" class="w-full h-60 object-cover rounded-lg">
                    
                    <!-- Se è un video -->
                    <video v-else controls class="w-full h-60 rounded-lg shadow">
                        <source :src="photo.path" type="video/mp4">
                        <source :src="photo.path" type="video/webm">
                        <source :src="photo.path" type="video/ogg">
                        Il tuo browser non supporta il video.
                    </video>

                    <!-- Pulsanti -->
                    <div class="mt-2 flex justify-between">
                        <button v-if="photo.path.match(/\.(jpeg|jpg|png|webp)$/i)" @click="setFavoritePhoto(photo.id)" class="text-yellow-500">⭐ Preferita</button>
                        <button @click="deleteFile(photo.id)" class="text-red-500">❌ Elimina</button>
                    </div>
                </div>
            </div>

            <div v-else class="my-4 p-4 bg-red-100 text-red-700 text-center rounded-lg">
                Nessun media disponibile per questo luogo.
            </div>
        </div>
    </AppLayout>
</template>
