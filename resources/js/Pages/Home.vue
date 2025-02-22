<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

const trips = [
    {
        id: 1,
        title: 'Tokyo, Giappone',
        date: 'Aprile 2023',
        image: 'https://via.placeholder.com/400x300/ffb6c1/ffffff?text=Tokyo'
    },
    {
        id: 2,
        title: 'Parigi, Francia',
        date: 'Settembre 2022',
        image: 'https://via.placeholder.com/400x300/ffb6c1/ffffff?text=Parigi'
    },
    {
        id: 3,
        title: 'Santorini, Grecia',
        date: 'Luglio 2021',
        image: 'https://via.placeholder.com/400x300/ffb6c1/ffffff?text=Santorini'
    },
    {
        id: 4,
        title: 'Kyoto, Giappone',
        date: 'Dicembre 2020',
        image: 'https://via.placeholder.com/400x300/ffb6c1/ffffff?text=Kyoto'
    }
];
</script>

<template>
    <AppLayout>
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-pink-600">I nostri viaggi insieme</h1>
                <a href="/new-trip" class="bg-pink-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-pink-700 transition">
                    + Aggiungi viaggio
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <div v-for="trip in trips" :key="trip.id"
                    class="bg-white shadow-lg rounded-lg overflow-hidden transform hover:scale-105 transition">
                    
                    <img :src="trip.image" :alt="trip.title" class="w-full h-48 object-cover">

                    <div class="p-4 bg-pink-50">
                        <h2 class="text-xl font-semibold text-pink-700">{{ trip.title }}</h2>
                        <p class="text-sm text-pink-500 italic">{{ trip.date }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
