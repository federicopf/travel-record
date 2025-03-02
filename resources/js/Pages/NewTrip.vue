<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

import { ref, computed } from 'vue';
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';

import Step1 from '@/Components/NewTrip/Step1.vue';
import Step2 from '@/Components/NewTrip/Step2.vue';
import Step3 from '@/Components/NewTrip/Step3.vue';

// Stato globale del viaggio
const tripData = ref({
    title: '',
    startDate: '',
    endDate: '',
    favorite_photo: '',
    places: []
});

// Stato attuale dello step
const step = ref(1);

// Controllo per abilitare/disabilitare il pulsante "Avanti"
const canProceed = computed(() => {
    if (step.value === 1) {
        return tripData.value.title && tripData.value.startDate && tripData.value.endDate;
    }
    return true;
});

const nextStep = () => {
    if (canProceed.value && step.value < 3) {
        step.value++;
    }
};

const prevStep = () => {
    if (step.value > 1) {
        step.value--;
    }
};

const saveTrip = () => {
    const formData = new FormData();
    formData.append('title', tripData.value.title);
    formData.append('start_date', tripData.value.startDate);
    formData.append('end_date', tripData.value.endDate);
    formData.append('favorite_photo', tripData.value.favorite_photo);

    tripData.value.places.forEach((place, index) => {
        formData.append(`places[${index}][name]`, place.name);
        formData.append(`places[${index}][lat]`, place.lat);
        formData.append(`places[${index}][lng]`, place.lng);

        place.photos.forEach((photo, pIndex) => {
            formData.append(`places[${index}][photos][${pIndex}]`, photo.file);
        });
    });

    router.post(route('new-trip.store'), formData, {
        onSuccess: () => {
            console.log("Viaggio salvato con successo!");
        },
        onError: (errors) => {
            console.log("Errore nel salvataggio:", errors);
        }
    });
};
</script>

<template>
    <AppLayout>
        <div class="max-w-lg mx-auto px-4 py-6">
            <h1 :class="`text-3xl font-bold text-center text-${$colorScheme}-600 mb-6`">Aggiungi un nuovo viaggio</h1>

            <div class="bg-white p-6 rounded-lg shadow-lg">
                <Step1 v-if="step === 1" v-model="tripData" />
                <Step2 v-if="step === 2" v-model="tripData" />
                <Step3 v-if="step === 3" v-model="tripData" />

                <!-- Navigazione tra gli step -->
                <div class="flex justify-between mt-6">
                    <button v-if="step > 1" @click="prevStep"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition">
                        Indietro
                    </button>
                    <button v-if="step < 3" @click="nextStep" :disabled="!canProceed"
                        :class="[
                            'px-4 py-2 rounded transition',
                            canProceed 
                                ? `bg-${$colorScheme}-600 text-white hover:bg-${$colorScheme}-700`
                                : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                        ]">
                        Avanti
                    </button>

                    <button v-if="step === 3" @click="saveTrip"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                        Salva Viaggio
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
