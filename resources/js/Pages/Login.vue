<script setup>
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const password = ref('');
const error = ref(null);

const submitForm = () => {
    Inertia.post('/login', { password: password.value }, {
        onError: (errors) => {
            error.value = errors.password;
        }
    });
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h1 class="text-2xl font-bold text-center text-gray-800 mb-4">Accesso</h1>
            <p class="text-gray-600 text-center mb-6">Inserisci la password per accedere</p>

            <form @submit.prevent="submitForm">
                <div class="mb-4">
                    <input 
                        type="password"
                        v-model="password"
                        placeholder="Password"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-200"
                        required
                    />
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition duration-300"
                >
                    Accedi
                </button>

                <p v-if="error" class="text-red-500 text-sm text-center mt-4">{{ error }}</p>
            </form>
        </div>
    </div>
</template>
