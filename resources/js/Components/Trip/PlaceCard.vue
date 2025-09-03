<script setup>
import { TrashIcon } from '@heroicons/vue/24/solid';

defineProps({
    place: {
        type: Object,
        required: true
    },
    tripId: {
        type: [String, Number],
        required: true
    },
    showDeleteButton: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['delete', 'click']);

const handleDelete = (event) => {
    event.stopPropagation();
    emit('delete', place.id);
};

const handleClick = () => {
    emit('click', place.id);
};
</script>

<template>
    <div 
        class="relative bg-white shadow-lg rounded-lg overflow-hidden cursor-pointer transition transform hover:scale-105"
        @click="handleClick"
    >
        <button 
            v-if="showDeleteButton"
            @click="handleDelete"
            class="absolute top-2 right-2 bg-red-500 text-white p-2 rounded-full shadow-lg hover:bg-red-600 transition z-10"
        >
            <TrashIcon class="w-4 h-4" />
        </button>

        <img 
            v-if="place.photos.length > 0" 
            :src="place.firstPhoto" 
            class="w-full h-40 object-cover"
        >
        <div v-else class="w-full h-40 bg-gray-300 flex items-center justify-center">
            <span class="text-gray-500">Nessuna immagine</span>
        </div>

        <div class="p-4">
            <h3 class="text-lg font-semibold text-gray-800">{{ place.name }}</h3>
            <p class="text-gray-600 text-sm mt-1">{{ place.address }}</p>
            <div class="flex flex-wrap gap-1 mt-2">
                <span
                    v-for="hashtag in place.hashtags"
                    :key="hashtag.id"
                    :style="{ backgroundColor: hashtag.color }"
                    class="text-xs text-white px-2 py-1 rounded-full"
                >
                    #{{ hashtag.name }}
                </span>
            </div>
        </div>
    </div>
</template>
