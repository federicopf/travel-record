<script setup>
import { ref, watch } from 'vue';

const props = defineProps(['modelValue']);
const emit = defineEmits(['update:modelValue']);

const isSingleDay = ref(false);

// Aggiorna la data di fine quando cambia la data di inizio
watch(() => props.modelValue.startDate, (newStartDate) => {
    if (newStartDate) {
        emit('update:modelValue', { 
            ...props.modelValue, 
            endDate: newStartDate 
        });
    }
});

// Gestisce il cambio di modalità giornata unica
watch(isSingleDay, (newValue) => {
    if (newValue) {
        // Se è giornata unica, la data di fine diventa la data di inizio
        emit('update:modelValue', { 
            ...props.modelValue, 
            endDate: props.modelValue.startDate 
        });
    } else {
        // Se NON è giornata unica, la data di fine resta visibile
        emit('update:modelValue', { 
            ...props.modelValue, 
            endDate: props.modelValue.startDate 
        });
    }
});
</script>

<template>
    <div class="space-y-4">
        <!-- Destinazione -->
        <div>
            <label class="block mb-2 text-gray-700 font-medium">
                Destinazione
            </label>
            <input 
                v-model="modelValue.title" 
                type="text" 
                placeholder="Es. Tokyo, Giappone"
                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-transparent"
            >
        </div>

        <!-- Data di Inizio -->
        <div>
            <label class="block mb-2 text-gray-700 font-medium">
                Data di Inizio
            </label>
            <input 
                v-model="modelValue.startDate" 
                type="date"
                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-transparent"
            >
        </div>

        <!-- Checkbox per giornata unica -->
        <div class="flex items-center">
            <input 
                type="checkbox" 
                id="single-day" 
                v-model="isSingleDay" 
                class="mr-3 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
            >
            <label for="single-day" class="text-gray-700">
                Giornata unica
            </label>
        </div>

        <!-- Data di Fine (visibile solo se non è giornata unica) -->
        <div v-if="!isSingleDay">
            <label class="block mb-2 text-gray-700 font-medium">
                Data di Fine
            </label>
            <input 
                v-model="modelValue.endDate" 
                type="date"
                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-transparent"
            >
        </div>
    </div>
</template>
