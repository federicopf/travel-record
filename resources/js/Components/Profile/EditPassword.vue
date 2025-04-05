<script setup>
import { ref } from 'vue'
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/solid'

const current = ref('')
const password = ref('')
const confirm = ref('')
const showPassword = ref(false)
const showCurrent = ref(false)

const toggleCurrent = () => (showCurrent.value = !showCurrent.value)
const togglePassword = () => (showPassword.value = !showPassword.value)

const save = () => {
  if (password.value !== confirm.value) {
    alert('Le password non coincidono!')
    return
  }

  alert(`Verifico attuale: ${current.value}\nNuova password: ${password.value}`)
}
</script>

<template>
  <div class="mt-4 border rounded p-4 shadow bg-white space-y-4">
    <h3 class="text-lg font-semibold text-gray-700">Cambia password</h3>

    <!-- Password attuale -->
    <div class="relative">
      <input
        :type="showCurrent ? 'text' : 'password'"
        v-model="current"
        class="w-full border rounded px-3 py-2 pr-10"
        placeholder="Password attuale"
      />
      <button type="button" @click="toggleCurrent" class="absolute right-3 top-2.5 text-gray-500">
        <component :is="showCurrent ? EyeSlashIcon : EyeIcon" class="w-5 h-5" />
      </button>
    </div>

    <!-- Nuova password -->
    <div class="relative">
      <input
        :type="showPassword ? 'text' : 'password'"
        v-model="password"
        class="w-full border rounded px-3 py-2 pr-10"
        placeholder="Nuova password"
      />
      <button type="button" @click="togglePassword" class="absolute right-3 top-2.5 text-gray-500">
        <component :is="showPassword ? EyeSlashIcon : EyeIcon" class="w-5 h-5" />
      </button>
    </div>

    <!-- Conferma password -->
    <div class="relative">
      <input
        :type="showPassword ? 'text' : 'password'"
        v-model="confirm"
        class="w-full border rounded px-3 py-2 pr-10"
        placeholder="Conferma nuova password"
      />
      <button type="button" @click="togglePassword" class="absolute right-3 top-2.5 text-gray-500">
        <component :is="showPassword ? EyeSlashIcon : EyeIcon" class="w-5 h-5" />
      </button>
    </div>

    <button
      @click="save"
      class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
    >
      Salva
    </button>
  </div>
</template>
