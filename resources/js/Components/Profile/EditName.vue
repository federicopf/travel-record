<script setup>
import { useForm, usePage } from '@inertiajs/vue3'

const props = defineProps({
  currentName: String,
})

const user = usePage().props.auth.user

const emit = defineEmits(['close'])

const form = useForm({
  name: props.currentName ?? ''
})

const save = () => {
  form.put(route('profile.update.name', { user: user.id }), {
    onSuccess: () => {
      emit('close')
    }
  })
}

</script>

<template>
  <div class="border rounded p-4 shadow bg-white space-y-4">
    <h3 class="text-lg font-semibold text-gray-700">Modifica nome</h3>

    <input
      v-model="form.name"
      type="text"
      class="w-full border rounded px-3 py-2"
      placeholder="Il tuo nome"
    />

    <button
      @click="save"
      class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
    >
      Salva
    </button>
  </div>
</template>
