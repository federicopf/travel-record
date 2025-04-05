<script setup>
import { ref } from 'vue'

const props = defineProps({ currentName: String })
const name = ref(props.currentName)

const save = () => {
  alert(`Salverei il nome: ${name.value}`)
}
</script>

<template>
  <div class="border rounded p-4 shadow bg-white space-y-2">
    <h3 class="text-lg font-semibold text-gray-700">Modifica nome</h3>
    <input
      v-model="name"
      type="text"
      class="w-full border rounded px-3 py-2"
      placeholder="Inserisci il tuo nome"
    />
    <button
      @click="save"
      class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
    >
      Salva
    </button>
  </div>
</template>
