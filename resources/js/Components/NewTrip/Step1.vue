<script setup>
import { ref, watch } from 'vue';

const props = defineProps(['modelValue']);
const emit = defineEmits(['update:modelValue']);

const isSingleDay = ref(false);

// Sincronizza endDate con startDate se "Giornata unica" è attivata
watch(() => props.modelValue.startDate, (newStartDate) => {
  if (isSingleDay.value) {
    emit('update:modelValue', { ...props.modelValue, endDate: newStartDate });
  }
});

// Se isSingleDay viene attivato, forza la data di fine uguale alla data di inizio
watch(isSingleDay, (newValue) => {
  if (newValue) {
    emit('update:modelValue', { ...props.modelValue, endDate: props.modelValue.startDate });
  }
});
</script>

<template>
  <div>
    <label class="block mb-2 text-gray-700">Destinazione</label>
    <input v-model="modelValue.title" type="text" placeholder="Es. Tokyo, Giappone"
      :class="`w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-${$colorScheme}-300`">

    <label class="block mb-2 text-gray-700">Data di Inizio</label>
    <input v-model="modelValue.startDate" type="date"
      :class="`w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-${$colorScheme}-300`">

    <!-- Checkbox per giornata unica -->
    <div class="flex items-center mb-4">
      <input type="checkbox" id="single-day" v-model="isSingleDay" class="mr-2">
      <label for="single-day" class="text-gray-700">Giornata unica</label>
    </div>

    <!-- Campo "Data di Fine" visibile solo se giornata unica è disattivata -->
    <div v-if="!isSingleDay">
      <label class="block mb-2 text-gray-700">Data di Fine</label>
      <input v-model="modelValue.endDate" type="date"
        :class="`w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-${$colorScheme}-300`">
    </div>
  </div>
</template>
