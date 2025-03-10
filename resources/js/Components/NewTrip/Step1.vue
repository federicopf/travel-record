<script setup>
import { ref, watch } from 'vue';

const props = defineProps(['modelValue']);
const emit = defineEmits(['update:modelValue']);

const isSingleDay = ref(false);

// Ogni volta che cambia la data di inizio, aggiorna sempre la data di fine
watch(() => props.modelValue.startDate, (newStartDate) => {
  if (newStartDate) {
    emit('update:modelValue', { ...props.modelValue, endDate: newStartDate });
  }
});

// Se cambia lo stato di "Giornata unica"
watch(isSingleDay, (newValue) => {
  if (newValue) {
    // Se è giornata unica, la data di fine diventa la data di inizio e si nasconde il campo
    emit('update:modelValue', { ...props.modelValue, endDate: props.modelValue.startDate });
  } else {
    // Se NON è giornata unica, la data di fine resta visibile e viene impostata alla data di inizio
    emit('update:modelValue', { ...props.modelValue, endDate: props.modelValue.startDate });
  }
});
</script>

<template>
  <div>
    <!-- Destinazione -->
    <label class="block mb-2 text-gray-700">Destinazione</label>
    <input v-model="modelValue.title" type="text" placeholder="Es. Tokyo, Giappone"
      class="w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-blue-300">

    <!-- Data di Inizio -->
    <label class="block mb-2 text-gray-700">Data di Inizio</label>
    <input v-model="modelValue.startDate" type="date"
      class="w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-blue-300">

    <!-- Checkbox per giornata unica -->
    <div class="flex items-center mb-4">
      <input type="checkbox" id="single-day" v-model="isSingleDay" class="mr-2">
      <label for="single-day" class="text-gray-700">Giornata unica</label>
    </div>

    <!-- Campo "Data di Fine" (si nasconde solo se è giornata unica) -->
    <div v-if="!isSingleDay">
      <label class="block mb-2 text-gray-700">Data di Fine</label>
      <input v-model="modelValue.endDate" type="date"
        class="w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-blue-300">
    </div>
  </div>
</template>
