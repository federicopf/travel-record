<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';
import UserCard from '@/Components/Friends/UserCard.vue';
import ActionButton from '@/Components/UI/ActionButton.vue';

const props = defineProps({
    friends: Array,
    requests_count: Number
});

const sendingRequest = ref(null);
const feedbacks = ref({});

const cancelFollow = async (userId) => {
    sendingRequest.value = userId;
    
    try {
        await axios.delete(route('friends.unfollow', userId));
        
        const index = props.friends.findIndex(user => user.id === userId);
        if (index !== -1) {
            props.friends.splice(index, 1);
            feedbacks.value[userId] = 'Follow rimosso';
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
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">I tuoi amici</h1>
                <div class="flex gap-3">
                    <ActionButton 
                        :href="route('friends.search')"
                        variant="primary"
                        size="sm"
                    >
                        Cerca amici
                    </ActionButton>
                    <ActionButton 
                        :href="route('friends.requests')"
                        variant="warning"
                        size="sm"
                    >
                        Vedi richieste
                        <span 
                            v-if="requests_count > 0" 
                            class="ml-1 bg-white text-orange-600 rounded-full px-2 text-xs font-bold"
                        >
                            {{ requests_count }}
                        </span>
                    </ActionButton>
                </div>
            </div>

            <div v-if="friends.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <UserCard 
                    v-for="friend in friends" 
                    :key="friend.id"
                    :user="friend"
                    action-type="unfollow"
                    :is-loading="sendingRequest === friend.id"
                    :feedback="feedbacks[friend.id]"
                    @unfollow="cancelFollow"
                />
            </div>

            <p v-else class="text-gray-500 mt-6 text-center">
                Non stai ancora seguendo nessuno.
            </p>
        </div>
    </AppLayout>
</template>
