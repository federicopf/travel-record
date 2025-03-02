<script setup>
import { ref } from 'vue';
import { route } from 'ziggy-js';
import { router, useForm } from '@inertiajs/vue3';
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/solid';

const form = useForm({
    name: '',
    email: '',
    username: '',
    password: '',
    password_confirmation: ''
});

const showPassword = ref(false);

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const submitForm = () => {
    form.post(route('register.post'));
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-r from-pink-400 to-pink-600">
      <div class="bg-white p-8 rounded-2xl shadow-xl w-96">
          <h1 class="text-3xl font-bold text-center text-pink-600 mb-2">Registrati</h1>
          <p class="text-gray-500 text-center mb-6">Crea il tuo account</p>

          <form @submit.prevent="submitForm">
              <div class="mb-4">
                  <input 
                      type="text"
                      v-model="form.name"
                      placeholder="Nome"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400"
                      required
                  />
                  <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
              </div>

              <div class="mb-4">
                  <input 
                      type="email"
                      v-model="form.email"
                      placeholder="Email"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400"
                      required
                  />
                  <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
              </div>

              <div class="mb-4">
                  <input 
                      type="text"
                      v-model="form.username"
                      placeholder="Username"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400"
                      required
                  />
                  <p v-if="form.errors.username" class="text-red-500 text-sm mt-1">{{ form.errors.username }}</p>
              </div>

              <!-- Campo Password con Occhio -->
              <div class="mb-4 relative">
                  <input 
                      :type="showPassword ? 'text' : 'password'"
                      v-model="form.password"
                      placeholder="Password"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400 pr-10"
                      required
                  />
                  <button type="button" @click="togglePasswordVisibility"
                      class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                      <EyeIcon v-if="!showPassword" class="w-5 h-5" />
                      <EyeSlashIcon v-else class="w-5 h-5" />
                  </button>
                  <p v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</p>
              </div>

              <!-- Campo Conferma Password con Occhio -->
              <div class="mb-4 relative">
                  <input 
                      :type="showPassword ? 'text' : 'password'"
                      v-model="form.password_confirmation"
                      placeholder="Conferma Password"
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
                  Registrati
              </button>

              <!-- Link al Login -->
              <p class="text-gray-500 text-sm text-center mt-4">
                  Hai già un account? 
                  <a :href="route('login')" class="text-pink-600 font-semibold hover:underline">Accedi</a>
              </p>
          </form>
      </div>
  </div>
</template>
