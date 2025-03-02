<script setup>
defineProps(['modelValue']);
defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <label class="block mb-2 text-gray-700">Destinazione</label>
        <input v-model="modelValue.title" type="text" placeholder="Es. Tokyo, Giappone"
            :class="`w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-${$colorScheme}-300`">

        <label class="block mb-2 text-gray-700">Data di Inizio</label>
        <input v-model="modelValue.startDate" type="date"
            :class="`w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-${$colorScheme}-300`">

        <label class="block mb-2 text-gray-700">Data di Fine</label>
        <input v-model="modelValue.endDate" type="date"
            :class="`w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-${$colorScheme}-300`">
    </div>
</template>
