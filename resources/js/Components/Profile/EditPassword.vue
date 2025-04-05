<script setup>
import { ref } from 'vue'

const password = ref('')
const confirm = ref('')

const save = () => {
  if (password.value !== confirm.value) {
    alert('Le password non coincidono!')
    return
  }

  alert(`Salverei la nuova password: ${password.value}`)
}
</script>

<template>
  <div class="border rounded p-4 shadow bg-white space-y-2">
    <h3 class="text-lg font-semibold text-gray-700">Cambia password</h3>

    <input
      v-model="password"
      type="password"
      class="w-full border rounded px-3 py-2"
      placeholder="Nuova password"
    />

    <input
      v-model="confirm"
      type="password"
      class="w-full border rounded px-3 py-2"
      placeholder="Conferma password"
    />

    <button
      @click="save"
      class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
    >
      Salva
    </button>
  </div>
</template>
