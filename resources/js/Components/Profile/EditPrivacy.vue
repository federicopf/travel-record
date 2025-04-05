<script setup>
import { ref, watch } from 'vue'

const props = defineProps({ privateProfile: Boolean })
const isPrivate = ref(props.privateProfile)

watch(isPrivate, (val) => {
  alert(`Profilo aggiornato come: ${val ? 'Privato' : 'Pubblico'}`)
})
</script>

<template>
  <label class="flex items-center gap-2 p-2 bg-gray-100 rounded">
    <input type="checkbox" v-model="isPrivate" class="accent-blue-600" />
    <span class="text-sm text-gray-700">Attiva profilo privato</span>
  </label>
</template>
