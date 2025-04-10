<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

import ProfilePicture from '@/Components/Profile/Sections/ProfilePicture.vue'

const props = defineProps({
  userName: String,
  userId: Number
})

const form = useForm({ photo: null })
const localPreview = ref(null)

const handleFileChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    form.photo = file
    const reader = new FileReader()
    reader.onload = (e) => {
      localPreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const submit = () => {
  form.post(route('profile.update.photo', { user: props.userId }), {
    preserveScroll: true
  })
}
</script>

<template>
  <div class="border-t pt-6">
    <h2 class="text-xl font-semibold text-gray-700 mb-4">Foto profilo</h2>

    <div class="flex items-center gap-6 flex-wrap sm:flex-nowrap">
      <!-- Usa il componente riutilizzabile -->
      <ProfilePicture
        :username="props.userName"
        :name="props.userName"
        :size="'w-24 h-24'"
        :font-size="'text-3xl'"
        :img-src="localPreview"
      />

      <div class="flex flex-col gap-2 w-full max-w-sm">
        <label class="block text-sm font-medium text-gray-600">Seleziona una nuova immagine</label>
        <input
          type="file"
          accept="image/png, image/jpeg"
          @change="handleFileChange"
          class="block w-full text-sm text-gray-700 border border-gray-300 rounded-md shadow-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 transition"
        />

        <button
          @click="submit"
          :disabled="form.processing || !form.photo"
          class="mt-2 inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Salva immagine
        </button>

        <p class="text-xs text-gray-500 mt-1">
          PNG o JPG. Max 5MB.
        </p>
      </div>
    </div>
  </div>
</template>
