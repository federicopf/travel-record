<script setup>
import { Link } from '@inertiajs/vue3';
import ProfilePicture from '@/Components/Profile/Sections/ProfilePicture.vue';

defineProps({
    user: {
        type: Object,
        required: true
    },
    showActions: {
        type: Boolean,
        default: true
    },
    actionType: {
        type: String,
        default: 'follow', // 'follow', 'unfollow', 'accept-reject'
        validator: (value) => ['follow', 'unfollow', 'accept-reject'].includes(value)
    },
    isLoading: {
        type: Boolean,
        default: false
    },
    feedback: {
        type: String,
        default: ''
    }
});

const emit = defineEmits(['follow', 'unfollow', 'accept', 'reject']);

const handleAction = (action) => {
    if (isLoading) return;
    emit(action, user.id);
};
</script>

<template>
    <div class="bg-white shadow rounded-lg p-4 text-center flex flex-col items-center">
        <Link 
            :href="route('profile.public', user.username)"
            class="flex flex-col items-center"
        >
            <ProfilePicture
                :username="user.username"
                :name="user.name"
                :size="'w-16 h-16'"
                :font-size="'text-xl'"
                class="mb-2"
            />
            
            <h2 class="text-lg font-semibold text-gray-800">{{ user.name }}</h2>
            <p class="text-sm text-gray-500">@{{ user.username }}</p>
        </Link>

        <!-- Azioni -->
        <div v-if="showActions" class="mt-3 space-y-1">
            <!-- Follow/Unfollow -->
            <button
                v-if="actionType === 'follow' && !user.status"
                @click="handleAction('follow')"
                :disabled="isLoading"
                class="text-sm text-blue-600 underline hover:text-blue-800 disabled:opacity-50"
            >
                {{ isLoading ? '...' : 'Segui' }}
            </button>

            <button
                v-else-if="actionType === 'unfollow' || user.status === 'accepted'"
                @click="handleAction('unfollow')"
                :disabled="isLoading"
                class="text-sm text-red-600 underline hover:text-red-800 disabled:opacity-50"
            >
                {{ isLoading ? '...' : 'Togli follow' }}
            </button>

            <button
                v-else-if="user.status === 'pending'"
                @click="handleAction('unfollow')"
                :disabled="isLoading"
                class="text-sm text-orange-600 underline hover:text-orange-800 disabled:opacity-50"
            >
                {{ isLoading ? '...' : 'Annulla richiesta' }}
            </button>

            <!-- Accept/Reject -->
            <div v-else-if="actionType === 'accept-reject'" class="flex flex-col gap-2">
                <button
                    @click="handleAction('accept')"
                    :disabled="isLoading"
                    class="bg-green-600 text-white py-1 rounded hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Accetta
                </button>
                <button
                    @click="handleAction('reject')"
                    :disabled="isLoading"
                    class="bg-red-500 text-white py-1 rounded hover:bg-red-600 transition disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Rifiuta
                </button>
            </div>

            <!-- Feedback -->
            <p
                v-if="feedback"
                class="text-xs text-green-600 mt-1"
            >
                {{ feedback }}
            </p>
        </div>
    </div>
</template>
