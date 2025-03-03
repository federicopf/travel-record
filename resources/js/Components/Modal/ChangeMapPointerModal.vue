<script setup>
import { ref, defineExpose } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const isOpen = ref(false);
const selectedPointer = ref(null);
const page = usePage();
const mapPointers = page.props.mapPointers ?? []; 
const user = page.props.auth?.user ?? null;

// Aprire la modale
const openModal = () => {
    selectedPointer.value = user?.map_pointer_id ?? null;
    isOpen.value = true;
};

// Chiudere la modale
const closeModal = () => {
    isOpen.value = false;
};

// Cambiare il Map Pointer
const changePointer = () => {
    router.post(route('map-pointer.change'), { map_pointer_id: selectedPointer.value }, {
        preserveState: true,
        onSuccess: () => {
            closeModal();
            window.location.reload(); // Ricarica la pagina per applicare il nuovo pointer
        }
    });
};

defineExpose({ openModal });
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div :class="`bg-gray-50 p-6 rounded-lg shadow-lg w-96`">
            <h2 :class="`text-lg font-semibold mb-4 text-${$colorScheme}-700`">Seleziona un nuovo segnaposto</h2>

            <!-- Griglia dei Map Pointers -->
            <div class="grid grid-cols-3 gap-4">
                <div 
                    v-for="pointer in mapPointers" 
                    :key="pointer.id" 
                    @click="selectedPointer = pointer.id"
                    class="cursor-pointer p-2 bg-white rounded-lg shadow-md flex flex-col items-center justify-center 
                           border transition duration-300"
                    :class="selectedPointer === pointer.id ? `border-${$colorScheme}-500 ring-2 ring-${$colorScheme}-300` : 'border-gray-300'">
                    <img :src="pointer.url" :alt="pointer.name" class="w-12 h-12 object-cover rounded-md">
                    <p class="text-sm mt-2 text-gray-700">{{ pointer.name }}</p>
                </div>
            </div>

            <!-- Pulsanti -->
            <div class="flex justify-between mt-6">
                <button @click="closeModal" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">Annulla</button>
                <button @click="changePointer" :class="`px-4 py-2 bg-${$colorScheme}-500 text-white rounded-lg hover:bg-${$colorScheme}-600 transition`">
                    Salva
                </button>
            </div>
        </div>
    </div>
</template>
