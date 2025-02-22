<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';

// Stato per il nuovo viaggio
const newTrip = ref({
    title: '',
    date: '',
    image: ''
});

// Simula il salvataggio del viaggio (per ora solo frontend)
const addTrip = () => {
    if (newTrip.value.title && newTrip.value.date && newTrip.value.image) {
        console.log("Viaggio aggiunto:", newTrip.value);
        Inertia.visit('/'); // Torna alla dashboard dopo l'aggiunta
    }
};
</script>

<template>
    <AppLayout>
        <div class="max-w-lg mx-auto px-4 py-6">
            <h1 class="text-3xl font-bold text-center text-pink-600 mb-6">Aggiungi un nuovo viaggio</h1>

            <div class="bg-white p-6 rounded-lg shadow-lg">
                <label class="block mb-2 text-gray-700">Destinazione</label>
                <input v-model="newTrip.title" type="text" placeholder="Es. Tokyo, Giappone"
                    class="w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-pink-300">

                <label class="block mb-2 text-gray-700">Mese e Anno</label>
                <input v-model="newTrip.date" type="text" placeholder="Es. Aprile 2023"
                    class="w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-pink-300">

                <label class="block mb-2 text-gray-700">URL immagine</label>
                <input v-model="newTrip.image" type="text" placeholder="Es. https://via.placeholder.com/400x300"
                    class="w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-pink-300">

                <div class="flex justify-between mt-6">
                    <button @click="$inertia.visit('/')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition">
                        Annulla
                    </button>
                    <button @click="addTrip" class="px-4 py-2 bg-pink-600 text-white rounded hover:bg-pink-700 transition">
                        Aggiungi
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
