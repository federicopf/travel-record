<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

import MapSection from '@/Components/Profile/Public/MapSection.vue'
import TripSection from '@/Components/Profile/Public/TripSection.vue'

const props = defineProps({
  user: Object,
  places: Array,
  trips: Array
})


const isMe = computed(() => usePage().props.auth?.user?.id === props.user?.id)
</script>

<template>
  <AppLayout>
    <div class="max-w-3xl mx-auto px-6 py-8">

      <div v-if="user.preview" class="mb-4">
        <a
          :href="route('profile.index', user.id)"
          class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
        >
          Torna alla gestione profilo
        </a>
      </div>

      <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
          Profilo di {{ user.name }}
        </h1>
        <p class="text-gray-500 text-sm">
          @{{ user.username }}
        </p>
      </div>

      <div
        v-if="user.preview"
        class="mb-4 bg-yellow-100 text-yellow-800 text-sm px-4 py-2 rounded"
      >
        Stai visualizzando un’anteprima del tuo profilo pubblico.
      </div>

      <div
        v-if="!user.preview && user.private"
        class="text-center text-gray-500"
      >
        Questo profilo è privato.
      </div>

      <div v-else class="space-y-4">
        <div class="bg-white p-4 rounded shadow">
          <p><span class="font-semibold text-gray-700">Nome: </span>{{ user.name }}</p>
          <p><span class="font-semibold text-gray-700">Username: </span>@{{ user.username }}</p>
        </div>

        <MapSection v-if="!user.private" :places="places" class="mt-6" />

        <TripSection v-if="trips.length" :trips="trips" class="mt-6" />

        <!-- Qui in futuro potresti aggiungere altri contenuti pubblici come post, luoghi visitati, ecc -->
      </div>
    </div>
  </AppLayout>
</template>
