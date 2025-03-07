<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps(['trip']);
const isSubmitting = ref(false);

const form = useForm({
    title: props.trip.title || '',
    start_date: props.trip.start_date || '',
    end_date: props.trip.end_date || '',
});

const submit = () => {
    isSubmitting.value = true;

    router.post(route('trip.update', props.trip.id), form, {
        onFinish: () => {
            isSubmitting.value = false;
        }
    });
};
</script>

<template>
    <AppLayout>
        <div class="container mx-auto px-4 py-6">
            <h1 :class="`text-3xl font-bold text-${$colorScheme}-600 mb-6`">Modifica Viaggio</h1>

            <div class="flex my-4">
                <Link :href="route('trip.show', trip.id)"
                    :class="`bg-gray-500 text-white font-semibold px-6 py-2 rounded-lg shadow-lg hover:bg-gray-600 transition duration-300`">
                    Indietro
                </Link>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-lg">
                <label class="block mb-2 text-gray-700">Destinazione</label>
                <input v-model="form.title" type="text"
                    :class="`w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-${$colorScheme}-300`">

                <label class="block mb-2 text-gray-700">Data di Inizio</label>
                <input v-model="form.start_date" type="date"
                    :class="`w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-${$colorScheme}-300`">

                <label class="block mb-2 text-gray-700">Data di Fine</label>
                <input v-model="form.end_date" type="date"
                    :class="`w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-${$colorScheme}-300`">

                <button @click="submit"
                    :class="`px-4 py-2 bg-${$colorScheme}-600 text-white rounded hover:bg-${$colorScheme}-700 transition flex items-center gap-2`"
                    :disabled="isSubmitting">
                    
                    <span v-if="isSubmitting">Salvataggio...</span>
                    <span v-else>Salva Modifiche</span>
                </button>
            </div>
        </div>
    </AppLayout>
</template>
