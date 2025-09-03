<script setup>
import { ref, watch } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';
import UserCard from '@/Components/Friends/UserCard.vue';

const props = defineProps({
    query: String,
    results: Array
});

const search = ref(props.query ?? '');
const sendingRequest = ref(null);
const feedbacks = ref({});

let debounceTimer = null;

watch(search, (value) => {
    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(() => {
        if (value.trim().length >= 3) {
            router.get(route('friends.search'), { q: value }, {
                preserveState: true,
                replace: true
            });
        }
    }, 500);
});

const sendFollowRequest = async (userId) => {
    sendingRequest.value = userId;
    
    try {
        const response = await axios.post(route('friends.follow', userId));
        const status = response.data.status;

        const index = props.results.findIndex(user => user.id === userId);
        if (index !== -1) {
            props.results[index].status = status;
        }
    } catch (error) {
        console.error(error);
    } finally {
        sendingRequest.value = null;
    }
};

const cancelFollow = async (userId) => {
    sendingRequest.value = userId;
    
    try {
        await axios.delete(route('friends.unfollow', userId));

        const index = props.results.findIndex(user => user.id === userId);
        if (index !== -1) {
            props.results[index].status = null;
            feedbacks.value[userId] = 'Follow annullato';
            setTimeout(() => delete feedbacks.value[userId], 3000);
        }
    } catch (error) {
        console.error(error);
    } finally {
        sendingRequest.value = null;
    }
};
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto px-6 py-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Cerca utenti</h1>

            <div class="mb-4">
                <Link
                    :href="route('friends.index')"
                    class="inline-block text-sm text-gray-700 underline hover:text-gray-900"
                >
                    ← Torna alla lista amici
                </Link>
            </div>

            <div class="mb-6">
                <input
                    type="text"
                    v-model="search"
                    placeholder="Cerca per username (min 3 caratteri)..."
                    class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300"
                />
            </div>

            <div v-if="results.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <UserCard 
                    v-for="user in results" 
                    :key="user.id"
                    :user="user"
                    :action-type="user.status === 'accepted' ? 'unfollow' : 'follow'"
                    :is-loading="sendingRequest === user.id"
                    :feedback="feedbacks[user.id]"
                    @follow="sendFollowRequest"
                    @unfollow="cancelFollow"
                />
            </div>

            <p
                v-else-if="search.trim().length >= 3"
                class="text-gray-500 text-center mt-6"
            >
                Nessun utente trovato con "{{ search }}"
            </p>
        </div>
    </AppLayout>
</template>
