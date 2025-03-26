<script setup>
import { ref, onMounted } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import { Loader } from '@googlemaps/js-api-loader';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps(['trip', 'place']);

const placeInput = ref(null);
let autocomplete = null;

const form = useForm({
    lat: props.place.lat,
    lng: props.place.lng
});

const initializeAutocomplete = async () => {
    const loader = new Loader({
        apiKey: import.meta.env.VITE_GOOGLE_MAPS_API_KEY,
        version: 'weekly',
        libraries: ['places']
    });

    try {
        const google = await loader.load();

        if (placeInput.value) {
            autocomplete = new google.maps.places.Autocomplete(placeInput.value);

            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();

                if (place.geometry) {
                    form.lat = place.geometry.location.lat();
                    form.lng = place.geometry.location.lng();
                }
            });
        }
    } catch (error) {
        console.error("Errore nell'autocomplete:", error);
    }
};

const submit = () => {
    form.put(route('trip.place.update', { trip: props.trip.id, place: props.place.id }));
};

onMounted(() => {
    initializeAutocomplete();
});


</script>

<template>
    <AppLayout>
        <div class="container mx-auto px-4 py-6">
            <!-- Pulsante di ritorno -->
            <div class="mb-4">
                <Link :href="route('trip.place.show', { trip: props.trip.id, place: props.place.id })"
                      class="bg-gray-500 text-white font-semibold px-6 py-2 rounded-lg shadow hover:bg-gray-600 transition duration-300">
                    Torna al posto
                </Link>
            </div>

            <!-- Titolo -->
            <h1 :class="`text-3xl font-bold text-${$colorScheme}-600 mb-6`">
                Modifica posizione
            </h1>

            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label class="block text-gray-700 mb-1">Cerca nuovo luogo</label>
                    <input
                        ref="placeInput"
                        type="text"
                        placeholder="Scrivi il nome del nuovo luogo..."
                        class="w-full border p-2 rounded"
                        required
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 mb-1">Latitudine</label>
                        <input
                            v-model="form.lat"
                            type="number"
                            step="any"
                            class="w-full border p-2 rounded bg-gray-100"
                            disabled
                        />
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Longitudine</label>
                        <input
                            v-model="form.lng"
                            type="number"
                            step="any"
                            class="w-full border p-2 rounded bg-gray-100"
                            disabled
                        />
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button
                        type="submit"
                        class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg shadow hover:bg-blue-700 transition duration-300"
                    >
                        Salva nuova posizione
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
