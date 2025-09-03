<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';

import Step1 from '@/Components/NewTrip/Step1.vue';
import Step2 from '@/Components/NewTrip/Step2.vue';
import StepIndicator from '@/Components/UI/StepIndicator.vue';
import ActionButton from '@/Components/UI/ActionButton.vue';

const tripData = ref({
    title: '',
    startDate: '',
    endDate: '',
    favorite_photo: '',
    places: []
});

const step = ref(1);
const isLoading = ref(false);

const canProceed = computed(() => {
    if (step.value === 1) {
        return tripData.value.title.trim() && 
               tripData.value.startDate && 
               tripData.value.endDate;
    }
    return true;
});

const nextStep = () => {
    if (canProceed.value && step.value < 2) {
        step.value++;
    }
};

const prevStep = () => {
    if (step.value > 1) {
        step.value--;
    }
};

const saveTrip = async () => {
    if (isLoading.value) return;
    
    isLoading.value = true;
    
    try {
        const formData = new FormData();
        formData.append('title', tripData.value.title.trim());
        formData.append('start_date', tripData.value.startDate);
        formData.append('end_date', tripData.value.endDate);
        formData.append('favorite_photo', tripData.value.favorite_photo);

        tripData.value.places.forEach((place, index) => {
            formData.append(`places[${index}][name]`, place.name);
            formData.append(`places[${index}][lat]`, place.lat);
            formData.append(`places[${index}][lng]`, place.lng);
        });

        await router.post(route('new-trip.store'), formData);
    } catch (error) {
        console.error("Errore nel salvataggio:", error);
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <AppLayout>
        <div class="max-w-lg mx-auto px-4 py-6">
            <h1 :class="`text-3xl font-bold text-center text-${$colorScheme}-600 mb-6`">
                Aggiungi un nuovo viaggio
            </h1>

            <div class="bg-white p-6 rounded-lg shadow-lg">
                <StepIndicator 
                    :current-step="step"
                    :total-steps="2"
                    class="mb-6"
                />

                <Step1 v-if="step === 1" v-model="tripData" />
                <Step2 v-if="step === 2" v-model="tripData" />
                
                <div class="flex justify-between mt-6">
                    <ActionButton 
                        v-if="step > 1" 
                        variant="secondary"
                        :disabled="isLoading"
                        @click="prevStep"
                    >
                        Indietro
                    </ActionButton>
                    
                    <ActionButton 
                        v-if="step === 1" 
                        :disabled="!canProceed || isLoading"
                        :loading="isLoading"
                        @click="nextStep"
                    >
                        Avanti
                    </ActionButton>
                    
                    <ActionButton 
                        v-if="step === 2" 
                        :loading="isLoading"
                        loading-text="Salvando..."
                        @click="saveTrip"
                    >
                        Salva Viaggio
                    </ActionButton>
                </div>
            </div>
        </div>
    </AppLayout>
</template>