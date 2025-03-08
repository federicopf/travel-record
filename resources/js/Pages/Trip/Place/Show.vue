<script setup>
import { ref, onMounted, computed } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { Loader } from '@googlemaps/js-api-loader';
import { EyeSlashIcon, StarIcon, TrashIcon, ArrowUpTrayIcon, XMarkIcon  } from '@heroicons/vue/24/solid';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps(['trip', 'place']);
const page = usePage();

const map = ref(null);
const mapInstance = ref(null);
const form = useForm({ files: [] });

const showFullscreen = ref(false);
const fullscreenMedia = ref(null);
const favoritePhotoId = ref(props.trip.image);

const user = page.props.auth?.user ?? null; // Otteniamo l'utente

let autocomplete = null;

const userPointerUrl = computed(() => {
    return user?.map_pointer_url ?? 'https://icons.iconarchive.com/icons/icons-land/vista-map-markers/256/Map-Marker-Ball-Pink-icon.png';
});

const handleFileUpload = (event) => {
    const selectedFiles = Array.from(event.target.files);

    if (selectedFiles.length > 5) {
        alert("Puoi caricare un massimo di 5 file alla volta!");
        return;
    }

    form.files = selectedFiles;
    if (form.files.length > 0) {
        uploadFiles();
    }
};

const uploadFiles = () => {
    form.post(route('trip.place.photo.upload', { trip: props.trip.id, place: props.place.id }), {
        onSuccess: () => form.reset('files'),
    });
};

const deleteFile = (photoId) => {
    if (confirm("Sei sicuro di voler eliminare questo file?")) {
        form.delete(route('trip.place.photo.delete', { trip: props.trip.id, place: props.place.id, photo: photoId }));
    }
};

const setFavoritePhoto = (photoId, photoPath) => {
    form.post(route('trip.photo.setFavorite', { trip: props.trip.id, photo: photoId }), {
        onSuccess: () => {
            favoritePhotoId.value = photoId;
        },
    });
};

const openFullscreen = (media) => {
    fullscreenMedia.value = media;
    showFullscreen.value = true;
};

const closeFullscreen = () => {
    showFullscreen.value = false;
};

const normalizePath = (photoPath) => {
    return photoPath.includes('/storage/') ? photoPath.split('/storage/')[1] : photoPath;
};

const isFavorite = (photoId, photoPath) => {
    return favoritePhotoId.value === photoId || favoritePhotoId.value === normalizePath(photoPath);
};

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
            icon: {
                url: userPointerUrl.value, // Usa il Map Pointer dell'utente
                scaledSize: new google.maps.Size(40, 40),
                anchor: new google.maps.Point(20, 40)
            },
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

        <div class="flex my-4">
            <Link :href="route('trip.show', props.trip.id)"
                class="bg-gray-500 text-white font-semibold px-6 py-2 rounded-lg shadow-lg hover:bg-gray-600 transition duration-300">
                Torna al Viaggio
            </Link>
        </div>

        <div class="container mx-auto px-4 py-6">
            <h1 :class="`text-3xl font-bold text-${$colorScheme}-600 mb-6`">{{ place.name }}</h1>
            <p class="text-gray-500 text-lg">{{ place.address }}</p>

            <div ref="map" class="w-full h-[400px] rounded-lg shadow my-6"></div>

            <div class="my-6">
                <h2 class="text-2xl font-bold text-gray-700">Carica fino a 5 file alla volta!</h2>
                <label :class="`bg-${$colorScheme}-500 text-white px-4 py-2 rounded flex items-center cursor-pointer hover:bg-${$colorScheme}-600 mt-3`">
                    <ArrowUpTrayIcon class="w-5 h-5 mr-2" /> Seleziona File
                    <input type="file" multiple class="hidden" @input="handleFileUpload" accept="image/*,video/*">
                </label>
            </div>

            <h2 class="text-2xl font-bold text-gray-700 mt-6">Media</h2>
            <div v-if="place.photos?.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <div v-for="photo in place.photos" :key="photo.id" class="relative bg-gray-100 p-4 rounded-lg overflow-hidden">
                    
                    <div class="absolute top-2 left-2 flex gap-2">
                        <button 
                            @click="setFavoritePhoto(photo.id, photo.path)" 
                            :class="`p-2 rounded-full shadow ${
                                isFavorite(photo.id, photo.path) ? 'bg-yellow-500 text-white hover:bg-yellow-600' : 'bg-gray-300 text-gray-700 hover:bg-gray-400'
                            }`">
                            <StarIcon class="w-5 h-5" />
                        </button>
                        <button @click="deleteFile(photo.id)" class="bg-red-500 text-white p-2 rounded-full shadow hover:bg-red-600">
                            <TrashIcon class="w-5 h-5" />
                        </button>
                    </div>

                    <img v-if="photo.path.match(/\.(jpeg|jpg|png|webp|svg)$/i)" :src="photo.path"
                        class="w-full h-60 object-cover rounded-lg cursor-pointer"
                        @click="openFullscreen(photo)"> 
                    
                    <video v-else controls class="w-full h-60 rounded-lg shadow cursor-pointer" @click="openFullscreen(photo)">
                        <source :src="photo.path" type="video/mp4">
                        <source :src="photo.path" type="video/webm">
                        <source :src="photo.path" type="video/ogg">
                        Il tuo browser non supporta il video.
                    </video>
                </div>
            </div>

            <div v-else class="my-4 p-4 bg-red-100 text-red-700 text-center rounded-lg">
                Nessun media disponibile per questo luogo.
            </div>
        </div>

        <div v-if="showFullscreen" class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-90 flex justify-center items-center z-50">
            <div class="absolute top-4 right-4 text-white text-3xl cursor-pointer z-50" @click="closeFullscreen">
                <XMarkIcon class="w-8 h-8" />
            </div>
            
            <div class="flex justify-center items-center w-full h-full">
                <img v-if="fullscreenMedia.path.match(/\.(jpeg|jpg|png|webp)$/i)" :src="fullscreenMedia.path" class="max-w-full max-h-full object-contain">
                
                <video v-else controls class="max-w-full max-h-full object-contain">
                    <source :src="fullscreenMedia.path" type="video/mp4">
                    <source :src="fullscreenMedia.path" type="video/webm">
                    <source :src="fullscreenMedia.path" type="video/ogg">
                    Il tuo browser non supporta il video.
                </video>
            </div>
        </div>
    </AppLayout>
</template>
