<script setup>
import { ref } from 'vue'

const props = defineProps({ privateProfile: Boolean })
const isPrivate = ref(props.privateProfile)

const save = () => {
  alert(`Profilo aggiornato come: ${isPrivate.value ? 'Privato' : 'Pubblico'}`)
}
</script>

<template>
  <div class="border rounded p-4 shadow bg-white space-y-2">
    <h3 class="text-lg font-semibold text-gray-700">Impostazioni privacy</h3>

    <label class="flex items-center gap-2">
      <input type="checkbox" v-model="isPrivate" />
      <span>Profilo privato (richiede approvazione per essere seguito)</span>
    </label>

    <button
      @click="save"
      class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
    >
      Salva
    </button>
  </div>
</template>
