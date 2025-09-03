<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import UserCard from '@/Components/Friends/UserCard.vue';

const props = defineProps({
    requests: Array
});

const processingUserId = ref(null);
const feedbacks = ref({});

const handleRequest = async (userId, action) => {
    processingUserId.value = userId;

    try {
        if (action === 'accept') {
            await axios.put(route('friends.accept', userId));
            feedbacks.value[userId] = 'Richiesta accettata!';
        } else {
            await axios.delete(route('friends.reject', userId));
            feedbacks.value[userId] = 'Richiesta rifiutata.';
        }

        setTimeout(() => {
            const index = props.requests.findIndex(u => u.id === userId);
            if (index !== -1) {
                props.requests.splice(index, 1);
            }
            delete feedbacks.value[userId];
        }, 3000);
    } catch (e) {
        feedbacks.value[userId] = 'Errore. Riprova.';
        setTimeout(() => delete feedbacks.value[userId], 3000);
    } finally {
        processingUserId.value = null;
    }
};
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto px-6 py-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Richieste di amicizia</h1>

            <div class="mb-4">
                <Link
                    :href="route('friends.index')"
                    class="inline-block text-sm text-gray-700 underline hover:text-gray-900"
                >
                    ← Torna alla lista amici
                </Link>
            </div>

            <div v-if="requests.length" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <UserCard 
                    v-for="user in requests" 
                    :key="user.id"
                    :user="user"
                    action-type="accept-reject"
                    :is-loading="processingUserId === user.id"
                    :feedback="feedbacks[user.id]"
                    @accept="(userId) => handleRequest(userId, 'accept')"
                    @reject="(userId) => handleRequest(userId, 'reject')"
                />
            </div>

            <p v-else class="text-gray-500 text-center mt-6">
                Nessuna richiesta in attesa.
            </p>
        </div>
    </AppLayout>
</template>
