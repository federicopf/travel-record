<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/solid';

const isOpen = ref(false);
const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const form = useForm({
    current_password: '',
    new_password: '',
    new_password_confirmation: ''
});

// Sincronizza la visibilità di "Nuova Password" e "Conferma Password"
const toggleNewPasswordVisibility = () => {
    showNewPassword.value = !showNewPassword.value;
};

// Mostra/Nasconde la password attuale
const toggleCurrentPasswordVisibility = () => {
    showCurrentPassword.value = !showCurrentPassword.value;
};

const openModal = () => isOpen.value = true;
const closeModal = () => isOpen.value = false;

const submit = () => {
    form.post(route('password.update'), {
        onSuccess: () => {
            form.reset();
            closeModal();
        }
    });
};

defineExpose({ openModal });
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-xl font-semibold text-center mb-4">Cambia Password</h2>

            <form @submit.prevent="submit">
                <!-- Password Attuale -->
                <div class="mb-4 relative">
                    <label class="block text-gray-700">Password Attuale</label>
                    <div class="relative">
                        <input :type="showCurrentPassword ? 'text' : 'password'" v-model="form.current_password" 
                            class="w-full p-2 border rounded pr-10" required>
                        <button type="button" @click="toggleCurrentPasswordVisibility"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                            <EyeIcon v-if="!showCurrentPassword" class="w-5 h-5" />
                            <EyeSlashIcon v-else class="w-5 h-5" />
                        </button>
                    </div>
                    <p v-if="form.errors.current_password" class="text-red-500 text-sm">{{ form.errors.current_password }}</p>
                </div>

                <!-- Nuova Password -->
                <div class="mb-4 relative">
                    <label class="block text-gray-700">Nuova Password</label>
                    <div class="relative">
                        <input :type="showNewPassword ? 'text' : 'password'" v-model="form.new_password" 
                            class="w-full p-2 border rounded pr-10" required>
                        <button type="button" @click="toggleNewPasswordVisibility"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                            <EyeIcon v-if="!showNewPassword" class="w-5 h-5" />
                            <EyeSlashIcon v-else class="w-5 h-5" />
                        </button>
                    </div>
                    <p v-if="form.errors.new_password" class="text-red-500 text-sm">{{ form.errors.new_password }}</p>
                </div>

                <!-- Conferma Nuova Password -->
                <div class="mb-4 relative">
                    <label class="block text-gray-700">Conferma Nuova Password</label>
                    <div class="relative">
                        <input :type="showNewPassword ? 'text' : 'password'" v-model="form.new_password_confirmation"
                            class="w-full p-2 border rounded pr-10" required>
                        <button type="button" @click="toggleNewPasswordVisibility"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                            <EyeIcon v-if="!showNewPassword" class="w-5 h-5" />
                            <EyeSlashIcon v-else class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                <div class="flex justify-between">
                    <button type="button" @click="closeModal" class="bg-gray-300 px-4 py-2 rounded">Annulla</button>
                    <button type="submit" :class="`bg-${$colorScheme}-500 text-white px-4 py-2 rounded`">Aggiorna</button>
                </div>
            </form>
        </div>
    </div>
</template>
