<script setup>
import { onMounted, ref, nextTick, computed } from 'vue';
import { Loader } from '@googlemaps/js-api-loader';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps(['places']);
const page = usePage();
const user = page.props.auth?.user ?? null; // Otteniamo l'utente

const map = ref(null);
const mapInstance = ref(null);
const markers = ref([]);
const infoWindow = ref(null);

// Computed property per l'icona del Map Pointer dell'utente
const userPointerUrl = computed(() => {
    return user?.map_pointer_url ?? 'https://icons.iconarchive.com/icons/icons-land/vista-map-markers/256/Map-Marker-Ball-Pink-icon.png';
});

// Computed property per il titolo dinamico
const mapTitle = computed(() => {
    return user?.type === 'couple' ? 'Mappa dei nostri viaggi' : 'Mappa dei miei viaggi';
});

const initializeMap = async () => {
    await nextTick(); // Assicura che il DOM sia montato prima di iniziare

    if (!map.value) {
        console.error("Errore: l'elemento della mappa non è stato trovato.");
        return;
    }

    const loader = new Loader({
        apiKey: import.meta.env.VITE_GOOGLE_MAPS_API_KEY,
        version: 'weekly',
        libraries: ['places']
    });

    try {
        const google = await loader.load();
        const center = { lat: 41.9028, lng: 12.4964 };

        mapInstance.value = new google.maps.Map(map.value, {
            center,
            zoom: 5,
        });

        infoWindow.value = new google.maps.InfoWindow();

        addMarkers(google);
    } catch (error) {
        console.error("Errore durante il caricamento di Google Maps:", error);
    }
};

const addMarkers = (google) => {
    if (!mapInstance.value) return;

    // Rimuoviamo i marker precedenti
    markers.value.forEach(marker => marker.setMap(null));
    markers.value = [];

    props.places.forEach(place => {
        const marker = new google.maps.Marker({
            position: { lat: parseFloat(place.lat), lng: parseFloat(place.lng) },
            map: mapInstance.value,
            icon: {
                url: userPointerUrl.value, // Usa il Map Pointer dell'utente
                scaledSize: new google.maps.Size(40, 40),
                anchor: new google.maps.Point(20, 40)
            },
            title: place.name,
        });

        marker.addListener("click", () => {
            let imageGallery = "";
            if (place.images && place.images.length > 0) {
                imageGallery = `
                    <div style="display: flex; justify-content: center; gap: 5px; margin-top: 8px;">
                        ${place.images.map(img => `
                            <img src="${img}" style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px; box-shadow: 2px 2px 6px rgba(0,0,0,0.2);">
                        `).join('')}
                    </div>`;
            }

            infoWindow.value.setContent(`
                <div style="
                    font-family: Arial, sans-serif;
                    padding: 12px;
                    max-width: 220px;
                    border-radius: 10px;
                    box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
                    background-color: white;
                ">
                    <h3 style="margin: 0; color: #d63384; font-size: 16px; text-align: center;">${place.trip}</h3>
                    <p style="margin: 5px 0; text-align: center; font-size: 14px;">
                        <strong>${place.name}</strong>
                    </p>
                    <p style="margin: 5px 0; text-align: center; font-size: 12px; color: gray;">
                        Dal <strong>${place.start_date}</strong> al <strong>${place.end_date}</strong>
                    </p>
                    ${imageGallery}
                    <div style="text-align: center; margin-top: 10px;">
                        <a href="`+route('trip.show', { trip: place.trip_id })+`" style="
                            display: inline-block;
                            text-decoration: none;
                            color: white;
                            background-color: #d63384;
                            padding: 6px 12px;
                            border-radius: 6px;
                            font-size: 14px;
                            font-weight: bold;
                            box-shadow: 2px 2px 6px rgba(0,0,0,0.2);
                        ">
                            Vai al viaggio
                        </a>
                    </div>
                </div>
            `);
            infoWindow.value.open(mapInstance.value, marker);
        });

        markers.value.push(marker);
    });

    console.log("Markers aggiunti:", markers.value);
};

onMounted(() => {
    initializeMap();
});
</script>

<template>
    <AppLayout>
        <div class="container mx-auto px-4 py-6">
            <h1 :class="`text-3xl font-bold text-${$colorScheme}-600 mb-6 text-center`">
                {{ mapTitle }}
            </h1>

            <div ref="map" class="w-full h-[500px] rounded-lg shadow"></div>
        </div>
    </AppLayout>
</template>
