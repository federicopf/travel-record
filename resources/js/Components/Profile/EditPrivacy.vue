<script setup>
import { ref, watch } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'

const props = defineProps({ privateProfile: Boolean })
const user = usePage().props.auth.user

const form = useForm({
  private_profile: props.privateProfile
})

watch(() => form.private_profile, (val) => {
  form.put(route('profile.update.privacy', { user: user.id }), {
    preserveScroll: true
  })
})
</script>

<template>
  <label class="flex items-center gap-2 p-2 bg-gray-100 rounded">
    <input
      type="checkbox"
      v-model="form.private_profile"
      class="accent-blue-600"
    />
    <span class="text-sm text-gray-700">Attiva profilo privato</span>
  </label>
</template>
