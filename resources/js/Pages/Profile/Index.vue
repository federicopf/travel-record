<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'

import EditName from '@/Components/Profile/EditName.vue'
import EditPassword from '@/Components/Profile/EditPassword.vue'
import EditPrivacy from '@/Components/Profile/EditPrivacy.vue'

const moment = window.moment

const props = defineProps({
  user: Object
})

// toggle per i blocchi
const showNameForm = ref(false)
const showPasswordForm = ref(false)
const showPrivacyForm = ref(false)
</script>

<template>
  <AppLayout>
    <div class="max-w-3xl mx-auto p-6 bg-white rounded-xl shadow space-y-6">
      <h1 class="text-3xl font-bold text-gray-800 mb-2">Profilo utente</h1>
      <p class="text-sm text-gray-500">
        Ultimo aggiornamento: {{ moment(user.updated_at).format('DD/MM/YYYY [alle] HH:mm') }}
      </p>

      <!-- Sezione anagrafica -->
      <div class="border-t pt-4">
        <div class="flex items-center justify-between mb-2">
          <h2 class="text-xl font-semibold text-gray-700">Informazioni personali</h2>
          <button @click="showNameForm = !showNameForm" class="text-sm text-blue-600 hover:underline">
            {{ showNameForm ? 'Annulla' : 'Modifica' }}
          </button>
        </div>

        <div v-if="!showNameForm" class="space-y-1">
          <p><span class="font-medium text-gray-600">Nome: </span>{{ user.name }}</p>
          <p v-if="user.partner_name">
            <span class="font-medium text-gray-600">Partner: </span>{{ user.partner_name }}
          </p>
        </div>

        <EditName v-if="showNameForm" :current-name="user.name" />
      </div>

      <!-- Sezione username -->
      <div class="border-t pt-4">
        <h2 class="text-xl font-semibold text-gray-700 mb-2">Username</h2>
        <p class="text-lg text-gray-800 font-medium">{{ user.username }}</p>
        <p class="text-sm text-gray-500 mt-1">L'username non può essere modificato.</p>
      </div>

      <!-- Sezione email -->
      <div class="border-t pt-4">
        <h2 class="text-xl font-semibold text-gray-700 mb-2">Email</h2>
        <div class="flex items-center gap-2">
          <p>{{ user.email }}</p>
          <span
            class="text-xs px-2 py-1 rounded-full"
            :class="user.email_verified_at ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
          >
            {{ user.email_verified_at ? 'Verificata' : 'Non verificata' }}
          </span>
        </div>
        <p class="text-sm text-gray-500 mt-1">L'email non può essere modificata.</p>
      </div>

      <!-- Password -->
      <div class="border-t pt-4">
        <div class="flex items-center justify-between mb-2">
          <h2 class="text-xl font-semibold text-gray-700">Sicurezza</h2>
          <button @click="showPasswordForm = !showPasswordForm" class="text-sm text-blue-600 hover:underline">
            {{ showPasswordForm ? 'Annulla' : 'Cambia password' }}
          </button>
        </div>
        <p class="text-gray-600 text-sm">Mantieni la tua password al sicuro.</p>

        <EditPassword v-if="showPasswordForm" />
      </div>

      <!-- Visibilità profilo -->
      <div class="border-t pt-4">
        <div class="flex items-center justify-between mb-2">
          <h2 class="text-xl font-semibold text-gray-700">Privacy</h2>
          <button @click="showPrivacyForm = !showPrivacyForm" class="text-sm text-blue-600 hover:underline">
            {{ showPrivacyForm ? 'Annulla' : 'Modifica visibilità' }}
          </button>
        </div>
        <p>
          <span class="font-medium text-gray-600">Stato: </span>
          <span
            class="font-semibold"
            :class="user.private_profile ? 'text-red-600' : 'text-green-600'"
          >
            {{ user.private_profile ? 'Privato' : 'Pubblico' }}
          </span>
        </p>

        <div class="mt-2 bg-gray-100 text-gray-600 text-sm rounded px-4 py-2">
          Un profilo privato richiede l'approvazione per essere seguito. Se è pubblico, può essere seguito liberamente.
        </div>

        <EditPrivacy v-if="showPrivacyForm" :private-profile="user.private_profile" class="mt-4" />
      </div>

      <!-- Dati tecnici -->
      <div class="border-t pt-4">
        <h2 class="text-xl font-semibold text-gray-700 mb-2">Dati account</h2>
        <p class="text-sm text-gray-500">
          Creato il: {{ moment(user.created_at).format('DD/MM/YYYY [alle] HH:mm') }}
        </p>
        <p class="text-sm text-gray-500">
          Ultima modifica: {{ moment(user.updated_at).format('DD/MM/YYYY [alle] HH:mm') }}
        </p>
      </div>
    </div>
  </AppLayout>
</template>
