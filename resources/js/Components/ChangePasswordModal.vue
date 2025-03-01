<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const isOpen = ref(false);
const form = useForm({
    current_password: '',
    new_password: '',
    new_password_confirmation: ''
});

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

defineExpose({ openModal }); // Permette di chiamare `openModal()` dall'header
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-xl font-semibold text-center mb-4">Cambia Password</h2>

            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label class="block text-gray-700">Password Attuale</label>
                    <input type="password" v-model="form.current_password" class="w-full p-2 border rounded" required>
                    <p v-if="form.errors.current_password" class="text-red-500 text-sm">{{ form.errors.current_password }}</p>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Nuova Password</label>
                    <input type="password" v-model="form.new_password" class="w-full p-2 border rounded" required>
                    <p v-if="form.errors.new_password" class="text-red-500 text-sm">{{ form.errors.new_password }}</p>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Conferma Nuova Password</label>
                    <input type="password" v-model="form.new_password_confirmation" class="w-full p-2 border rounded" required>
                </div>

                <div class="flex justify-between">
                    <button type="button" @click="closeModal" class="bg-gray-300 px-4 py-2 rounded">Annulla</button>
                    <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded">Aggiorna</button>
                </div>
            </form>
        </div>
    </div>
</template>
