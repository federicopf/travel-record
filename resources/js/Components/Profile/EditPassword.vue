<script setup>
import { ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/solid'

const emit = defineEmits(['close'])

const user = usePage().props.auth.user

const showCurrent = ref(false)
const showPassword = ref(false)
const showSuccess = ref(false)

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const toggleCurrent = () => (showCurrent.value = !showCurrent.value)
const togglePassword = () => (showPassword.value = !showPassword.value)

const save = () => {
  if (form.password !== form.password_confirmation) {
    form.setError('password_confirmation', 'Le password non coincidono!')
    return
  }

  form.put(route('profile.update.password', { user: user.id }), {
    onSuccess: () => {
      showSuccess.value = true

      setTimeout(() => {
        showSuccess.value = false
        emit('close') // chiude dopo il toast
        form.reset()
      }, 3000)
    }
  })
}

</script>

<template>
  <div class="mt-4 border rounded p-4 shadow bg-white space-y-4 relative">
    <h3 class="text-lg font-semibold text-gray-700">Cambia password</h3>

    <div
      v-if="showSuccess"
      class="absolute top-0 right-0 mt-2 mr-2 bg-green-500 text-white text-sm px-4 py-2 rounded shadow transition-opacity duration-300"
    >
      Password aggiornata!
    </div>

    <div class="relative">
      <input
        :type="showCurrent ? 'text' : 'password'"
        v-model="form.current_password"
        class="w-full border rounded px-3 py-2 pr-10"
        placeholder="Password attuale"
      />
      <button type="button" @click="toggleCurrent" class="absolute right-3 top-2.5 text-gray-500">
        <component :is="showCurrent ? EyeSlashIcon : EyeIcon" class="w-5 h-5" />
      </button>
    </div>
    <p v-if="form.errors.current_password" class="text-sm text-red-600">{{ form.errors.current_password }}</p>

    <!-- Nuova password -->
    <div class="relative">
      <input
        :type="showPassword ? 'text' : 'password'"
        v-model="form.password"
        class="w-full border rounded px-3 py-2 pr-10"
        placeholder="Nuova password"
      />
      <button type="button" @click="togglePassword" class="absolute right-3 top-2.5 text-gray-500">
        <component :is="showPassword ? EyeSlashIcon : EyeIcon" class="w-5 h-5" />
      </button>
    </div>
    <p v-if="form.errors.password" class="text-sm text-red-600">{{ form.errors.password }}</p>

    <!-- Conferma nuova password -->
    <div class="relative">
      <input
        :type="showPassword ? 'text' : 'password'"
        v-model="form.password_confirmation"
        class="w-full border rounded px-3 py-2 pr-10"
        placeholder="Conferma nuova password"
      />
      <button type="button" @click="togglePassword" class="absolute right-3 top-2.5 text-gray-500">
        <component :is="showPassword ? EyeSlashIcon : EyeIcon" class="w-5 h-5" />
      </button>
    </div>
    <p v-if="form.errors.password_confirmation" class="text-sm text-red-600">{{ form.errors.password_confirmation }}</p>

    <button
      @click="save"
      :disabled="form.processing"
      class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition disabled:opacity-50"
    >
      Salva
    </button>
  </div>
</template>
