<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    trip: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <Link 
        :href="route('trip.show', trip.id)"
        class="bg-white shadow-lg rounded-lg overflow-hidden transform hover:scale-105 transition block"
    >
        <img 
            v-if="trip.image" 
            :src="trip.image" 
            alt="Immagine del viaggio" 
            class="w-full h-48 object-cover"
        >
        <div v-else class="p-3 flex h-48 italic text-center items-center justify-center bg-gray-200">
            Nessuna immagine
        </div>

        <div :class="`p-4 pb-2 bg-${$colorScheme}-50`">
            <h2 :class="`text-xl font-semibold text-${$colorScheme}-700`">
                {{ trip.title }}
            </h2>
            <p :class="`text-sm text-${$colorScheme}-500 italic`">
                {{ trip.start_date }} - {{ trip.end_date }}
            </p>
        </div>
        
        <div class="p-4 pt-0">
            <div class="flex flex-wrap gap-1 mt-2">
                <span
                    v-for="hashtag in trip.hashtags"
                    :key="hashtag.id"
                    :style="{ backgroundColor: hashtag.color }"
                    class="text-xs text-white px-2 py-1 rounded-full"
                >
                    #{{ hashtag.name }}
                </span>
            </div>
        </div>
    </Link>
</template>
