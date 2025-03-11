<script setup>
import { ref, onMounted, computed } from 'vue';
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
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

const uploading = ref(false); 
const uploadProgress = ref(0); 
const totalFiles = ref(0); 

const user = page.props.auth?.user ?? null;

let autocomplete = null;

const userPointerUrl = computed(() => {
    return user?.map_pointer_url ?? 'https://icons.iconarchive.com/icons/icons-land/vista-map-markers/256/Map-Marker-Ball-Pink-icon.png';
});

const handleFileUpload = async (event) => {
    const selectedFiles = Array.from(event.target.files);
    const existingFiles = props.place.photos ? props.place.photos.length : 0;
    const totalAfterUpload = existingFiles + selectedFiles.length;
    const maxFileSize = 1024 * 1024 * 100; // 100MB per file
    const validFiles = [];

    if (totalAfterUpload > 30) {
        alert(`Puoi avere al massimo 30 file in totale! Attualmente hai ${existingFiles} file.`);
        return;
    }

    for (const file of selectedFiles) {
        if (file.size > maxFileSize) {
            alert(`Il file "${file.name}" è troppo grande! Massimo 100MB per file.`);
            return; 
        }
        validFiles.push(file);
    }

    if (validFiles.length === 0) {
        alert("Nessun file valido selezionato.");
        return;
    }

    uploading.value = true;
    uploadProgress.value = 0;
    totalFiles.value = validFiles.length;

    for (const file of validFiles) {
        await uploadFile(file);
        uploadProgress.value++;
    }

    uploading.value = false;
};

const uploadFile = (file) => {
    return new Promise((resolve, reject) => {
        const formData = new FormData();
        formData.append('files[]', file); 

        router.post(route('trip.place.photo.upload', { trip: props.trip.id, place: props.place.id }), formData, {
            onSuccess: () => {
                console.log(`File ${file.name} caricato con successo!`);
                resolve(); 
            },
            onError: (errors) => {
                console.error(`Errore nel caricamento del file ${file.name}:`, errors);
                reject(errors); 
            }
        });
    });
};


const deleteFile = (photoId) => {
    if (confirm("Sei sicuro di voler eliminare questo file?")) {
        form.delete(route('trip.place.photo.delete', { trip: props.trip.id, place: props.place.id, photo: photoId }));
    }
};

const setFavoritePhoto = (photoId, photoPath) => {
    form.post(route('trip.place.setFavorite', { trip: props.trip.id, photo: photoId }), {
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

const handleOverlayClick = (event) => {
    if (event.target.tagName === 'DIV') {
        closeFullscreen();
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
                <h2 class="text-2xl font-bold text-gray-700">Carica fino a 30 file!</h2>
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
                                isFavorite(photo.id, photo.path) ? 'bg-yellow-500 text-white hover:bg-yellow-600 z-10' : 'bg-gray-300 text-gray-700 hover:bg-gray-400 z-10'
                            }`">
                            <StarIcon class="w-5 h-5" />
                        </button>
                        <button @click="deleteFile(photo.id)" class="bg-red-500 text-white p-2 rounded-full shadow hover:bg-red-600 z-10">
                            <TrashIcon class="w-5 h-5" />
                        </button>
                    </div>

                    <img v-if="photo.path.match(/\.(jpeg|jpg|png|webp|svg)$/i)" :src="photo.path"
                        class="w-full h-60 object-cover rounded-lg cursor-pointer"
                        @click="openFullscreen(photo)"> 
                    
                    <video v-else class="w-full h-60 rounded-lg shadow cursor-pointer" @click.stop="openFullscreen(photo)">
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

        <div v-if="uploading" class="fixed inset-0 bg-black bg-opacity-75 flex flex-col items-center justify-center z-10">
            <p class="text-white text-lg mb-4">Caricamento file {{ uploadProgress }} di {{ totalFiles }}...</p>
            <div class="w-2/3 md:w-1/3 bg-gray-300 rounded-full h-3">
                <div class="bg-blue-500 h-3 rounded-full transition-all"
                    :style="{ width: (uploadProgress / totalFiles) * 100 + '%' }"></div>
            </div>
        </div>

        <div 
            v-if="showFullscreen" 
            class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-90 flex justify-center items-center z-50 cursor-pointer"
            @click="handleOverlayClick"
        >
            <!-- Icona per chiudere manualmente -->
            <div class="absolute top-4 right-4 text-white text-3xl cursor-pointer z-10" @click.stop="closeFullscreen">
                <XMarkIcon class="w-8 h-8" />
            </div>
            
            <!-- Contenitore media -->
            <div class="flex justify-center items-center w-full h-full">
                <img 
                    v-if="fullscreenMedia.path.match(/\.(jpeg|jpg|png|webp)$/i)" 
                    :src="fullscreenMedia.path" 
                    class="max-w-full max-h-full object-contain"
                >
                
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
