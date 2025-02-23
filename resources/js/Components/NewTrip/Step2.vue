<script setup>
import { ref } from 'vue';

const props = defineProps(['modelValue']);
const emit = defineEmits(['update:modelValue']);
const newPlace = ref('');

const addPlace = () => {
    if (newPlace.value) {
        props.modelValue.places.push({ name: newPlace.value, photos: [] });
        newPlace.value = '';
        emit('update:modelValue', props.modelValue);
    }
};
</script>

<template>
    <div>
        <label class="block mb-2 text-gray-700">Aggiungi un posto da visitare</label>
        <div class="flex gap-2">
            <input v-model="newPlace" type="text" placeholder="Es. Shibuya Crossing"
                class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-pink-300">
            <button @click="addPlace"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                +
            </button>
        </div>

        <ul class="mt-4">
            <li v-for="(place, index) in modelValue.places" :key="index" class="p-2 border-b text-gray-700">
                {{ place.name }}
            </li>
        </ul>
    </div>
</template>
