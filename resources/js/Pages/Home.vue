<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import { Link, usePage } from '@inertiajs/vue3';

    const successMessage = usePage().props.flash?.success ?? null;
    const trips = usePage().props.trips ?? [];
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
                <h1 class="text-3xl font-bold text-pink-600 mb-4 md:mb-0">I nostri viaggi insieme</h1>
                <Link :href="route('new-trip')" class="bg-pink-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-pink-700 transition">
                    + Aggiungi viaggio
                </Link>
            </div>

            <div v-if="trips.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <div v-for="trip in trips" :key="trip.id"
                    class="bg-white shadow-lg rounded-lg overflow-hidden transform hover:scale-105 transition">
                    
                    <img v-if="trip.image" :src="trip.image" :alt="trip.title" class="w-full h-48 object-cover">
                    <p v-else class="p-3 flex h-48 italic text-center items-center object-cover">Nessuna immagine per questo viaggio! Male...</p>

                    <div class="p-4 bg-pink-50">
                        <h2 class="text-xl font-semibold text-pink-700">{{ trip.title }}</h2>
                        <p class="text-sm text-pink-500 italic">{{ trip.start_date }} - {{ trip.end_date }}</p>
                    </div>
                </div>
            </div>

            <div v-else class="text-center text-gray-500 mt-10">
                Nessun viaggio disponibile. Aggiungine uno!
            </div>
        </div>
    </AppLayout>
</template>
