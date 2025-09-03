<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import TripCard from '@/Components/Trip/TripCard.vue';
import FeedbackMessage from '@/Components/UI/FeedbackMessage.vue';

const page = usePage();
const user = page.props.auth?.user ?? null;
const successMessage = page.props.flash?.success ?? null;
const trips = page.props.trips ?? [];

const titleText = computed(() => 
    user?.type === 'couple' ? 'I nostri viaggi insieme' : 'I miei viaggi'
);

const noTripsMessage = computed(() => 
    user?.type === 'couple' 
        ? 'Nessun viaggio disponibile. Aggiungine uno per voi!' 
        : 'Nessun viaggio disponibile. Aggiungine uno!'
);
</script>

<template>
    <AppLayout>
        <FeedbackMessage 
            v-if="successMessage"
            type="success"
            :message="successMessage"
            :auto-hide="true"
        />

        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6">
                <h1 :class="`text-3xl font-bold text-${$colorScheme}-600 mb-4 md:mb-0`">
                    {{ titleText }}
                </h1>
                <Link 
                    :href="route('new-trip')" 
                    :class="`bg-${$colorScheme}-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-${$colorScheme}-700 transition`"
                >
                    + Aggiungi viaggio
                </Link>
            </div>

            <div v-if="trips.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <TripCard 
                    v-for="trip in trips" 
                    :key="trip.id" 
                    :trip="trip"
                />
            </div>

            <div v-else class="text-center text-gray-500 mt-10">
                {{ noTripsMessage }}
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
