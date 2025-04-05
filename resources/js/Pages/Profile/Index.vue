<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  user: Object
})
</script>

<template>
  <AppLayout>
    <div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow">
      <h1 class="text-2xl font-bold text-gray-800 mb-6">Profilo</h1>

      <div class="space-y-6">
        <!-- Nome -->
        <div>
          <p class="text-sm text-gray-500">Nome</p>
          <p class="text-lg text-gray-800 font-medium">{{ user.name }}</p>
        </div>

        <!-- Nome partner -->
        <div v-if="user.partner_name">
          <p class="text-sm text-gray-500">Partner</p>
          <p class="text-lg text-gray-800 font-medium">{{ user.partner_name }}</p>
        </div>

        <!-- Username -->
        <div>
          <p class="text-sm text-gray-500">Username</p>
          <p class="text-lg text-gray-800 font-medium">{{ user.username }}</p>
        </div>

        <!-- Email -->
        <div>
          <p class="text-sm text-gray-500">Email</p>
          <p class="text-lg text-gray-800 font-medium">{{ user.email }}</p>
        </div>

        <!-- Profilo privato -->
        <div>
          <p class="text-sm text-gray-500">Visibilità profilo</p>
          <p class="text-lg font-medium" :class="user.private_profile ? 'text-red-600' : 'text-green-600'">
            {{ user.private_profile ? 'Privato (richiede approvazione)' : 'Pubblico (seguibile liberamente)' }}
          </p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
