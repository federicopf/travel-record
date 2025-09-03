<template>
  <div id="app">
    <router-view />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/Stores/Auth/User'
import { useThemeStore } from '@/Stores/Profile/Settings'

// Initialize stores
const authStore = useAuthStore()
const themeStore = useThemeStore()

onMounted(() => {
  // Initialize app
  authStore.initialize()
  themeStore.initialize()
})
</script>

<style>
@import '@/Assets/Styles/Layout/base.css';
@import '@/Assets/Styles/Components/common.css';
</style>
