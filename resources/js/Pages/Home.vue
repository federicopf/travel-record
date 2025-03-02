<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// Otteniamo i dati dell'utente e dei viaggi
const page = usePage();
const user = page.props.auth?.user ?? null;
const successMessage = page.props.flash?.success ?? null;
const trips = page.props.trips ?? [];

// Computed property per il titolo dinamico
const titleText = computed(() => {
    return user?.type === 'couple' ? 'I nostri viaggi insieme' : 'I miei viaggi';
});

// Computed property per il messaggio di nessun viaggio disponibile
const noTripsMessage = computed(() => {
    return user?.type === 'couple' 
        ? 'Nessun viaggio disponibile. Aggiungine uno per voi!' 
        : 'Nessun viaggio disponibile. Aggiungine uno!';
});
</script>

<template>
    <AppLayout>
        <transition name="fade">
            <div v-if="successMessage" class="bg-green-500 text-white p-3 rounded-md text-center mb-6">
                {{ successMessage }}
            </div>
        </transition>

        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6">
                <h1 :class="`text-3xl font-bold text-${$colorScheme}-600 mb-4 md:mb-0`">
                    {{ titleText }}
                </h1>
                <Link :href="route('new-trip')" :class="`bg-${$colorScheme}-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-${$colorScheme}-700 transition`">
                    + Aggiungi viaggio
                </Link>
            </div>

            <div v-if="trips.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <Link v-for="trip in trips" :key="trip.id" :href="route('trip.show', trip.id)"
                    class="bg-white shadow-lg rounded-lg overflow-hidden transform hover:scale-105 transition block">
                    
                    <img v-if="trip.image" :src="trip.image" :alt="trip.title" class="w-full h-48 object-cover">
                    <p v-else class="p-3 flex h-48 italic text-center items-center justify-center bg-gray-200">
                        Nessuna immagine per questo viaggio! Male...
                    </p>

                    <div :class="`p-4 bg-${$colorScheme}-50`">
                        <h2 :class="`text-xl font-semibold text-${$colorScheme}-700`">{{ trip.title }}</h2>
                        <p :class="`text-sm text-${$colorScheme}-500 italic`">{{ trip.start_date }} - {{ trip.end_date }}</p>
                    </div>
                </Link>
            </div>

            <div v-else class="text-center text-gray-500 mt-10">
                {{ noTripsMessage }}
            </div>
        </div>
    </AppLayout>
</template>
