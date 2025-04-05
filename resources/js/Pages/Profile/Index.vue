<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'

const moment = window.moment

const props = defineProps({
  user: Object
})
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
          <Link href="#" class="text-sm text-blue-600 hover:underline">Modifica</Link>
        </div>
        <div class="space-y-1">
          <p><span class="font-medium text-gray-600">Nome:</span> {{ user.name }}</p>
          <p v-if="user.partner_name">
            <span class="font-medium text-gray-600">Partner:</span> {{ user.partner_name }}
          </p>
          <p><span class="font-medium text-gray-600">Username:</span> {{ user.username }}</p>
        </div>
      </div>

      <!-- Sezione email -->
      <div class="border-t pt-4">
        <div class="flex items-center justify-between mb-2">
          <h2 class="text-xl font-semibold text-gray-700">Email</h2>
          <Link href="#" class="text-sm text-blue-600 hover:underline">Cambia email</Link>
        </div>
        <div class="flex items-center gap-2">
          <p>{{ user.email }}</p>
          <span
            class="text-xs px-2 py-1 rounded-full"
            :class="user.email_verified_at ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
          >
            {{ user.email_verified_at ? 'Verificata' : 'Non verificata' }}
          </span>
        </div>
      </div>

      <!-- Password -->
      <div class="border-t pt-4">
        <div class="flex items-center justify-between mb-2">
          <h2 class="text-xl font-semibold text-gray-700">Sicurezza</h2>
          <Link href="#" class="text-sm text-blue-600 hover:underline">Cambia password</Link>
        </div>
        <p class="text-gray-600 text-sm">Mantieni la tua password al sicuro.</p>
      </div>

      <!-- Visibilità profilo -->
      <div class="border-t pt-4">
        <div class="flex items-center justify-between mb-2">
          <h2 class="text-xl font-semibold text-gray-700">Privacy</h2>
          <Link href="#" class="text-sm text-blue-600 hover:underline">Modifica visibilità</Link>
        </div>
        <p>
          <span class="font-medium text-gray-600">Stato:</span>
          <span
            class="font-semibold"
            :class="user.private_profile ? 'text-red-600' : 'text-green-600'"
          >
            {{ user.private_profile ? 'Privato (richiede approvazione)' : 'Pubblico (seguibile liberamente)' }}
          </span>
        </p>
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
