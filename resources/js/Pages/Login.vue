<script setup>
import { ref } from 'vue';
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/solid';

const username = ref('');
const password = ref('');
const showPassword = ref(false);
const error = ref(null);

const submitForm = () => {
    router.post(route('login.post'), { username: username.value, password: password.value }, {
        onError: (errors) => {
            if (errors.username) {
                error.value = errors.username;
            }
        },
        onSuccess: () => {
            error.value = null;
        }
    });
};

// Mostra/Nasconde la password
const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-r from-pink-400 to-pink-600">
      <div class="bg-white p-8 rounded-2xl shadow-xl w-96">
          <h1 class="text-3xl font-bold text-center text-pink-600 mb-2">Accedi</h1>
          <p class="text-gray-500 text-center mb-6">Inserisci le tue credenziali</p>

          <form @submit.prevent="submitForm">
              <div class="mb-4">
                  <input 
                      type="text"
                      v-model="username"
                      placeholder="Username"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400"
                      required
                  />
              </div>

              <!-- Campo Password con Occhio -->
              <div class="mb-4 relative">
                  <input 
                      :type="showPassword ? 'text' : 'password'"
                      v-model="password"
                      placeholder="Password"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400 pr-10"
                      required
                  />
                  <button type="button" @click="togglePasswordVisibility"
                      class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                      <EyeIcon v-if="!showPassword" class="w-5 h-5" />
                      <EyeSlashIcon v-else class="w-5 h-5" />
                  </button>
              </div>

              <button
                  type="submit"
                  class="w-full bg-pink-600 text-white py-2 rounded-lg hover:bg-pink-700 transition duration-300"
              >
                  Accedi
              </button>

              <p v-if="error" class="text-red-500 text-sm text-center mt-4">{{ error }}</p>

              <!-- Link alla Registrazione -->
              <p class="text-gray-500 text-sm text-center mt-4">
                  Non hai un account? 
                  <a :href="route('register')" class="text-pink-600 font-semibold hover:underline">Registrati</a>
              </p>
          </form>
      </div>
  </div>
</template>
