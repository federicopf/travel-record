<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    tripId: Number,
    placeId: Number,
    availableHashtags: Array,
    selectedHashtags: Array
})

const form = useForm({
    hashtags: [...(props.selectedHashtags ?? []).map(h => h.id)]
})

const toggleHashtag = (hashtagId) => {
    const index = form.hashtags.indexOf(hashtagId)
    if (index !== -1) {
        form.hashtags.splice(index, 1)
    } else {
        form.hashtags.push(hashtagId)
    }
}

const save = () => {
    form.post(route('trip.place.hashtags.update', {
        trip: props.tripId,
        place: props.placeId
    }))
}
</script>

<template>
    <div class="my-8">
        <h2 class="text-xl font-semibold text-gray-700 mb-3">Hashtag del posto</h2>
        <div class="flex flex-wrap gap-2">
            <button
                v-for="hashtag in availableHashtags"
                :key="hashtag.id"
                @click="toggleHashtag(hashtag.id)"
                :style="{ backgroundColor: form.hashtags.includes(hashtag.id) ? hashtag.color : '#f3f4f6' }"
                class="px-3 py-1 rounded-full border text-sm font-medium transition"
                :class="form.hashtags.includes(hashtag.id) ? 'text-white' : 'text-gray-700 border-gray-300 hover:bg-gray-200'"
            >
                #{{ hashtag.name }}
            </button>
        </div>

        <button
            @click="save"
            class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
        >
            Salva Hashtag
        </button>
    </div>
</template>
