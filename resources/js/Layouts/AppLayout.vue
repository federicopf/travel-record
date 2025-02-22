<script setup>
import Topbar from '@/Components/Topbar.vue';
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <Topbar />

        <main class="p-6">
            <slot />
        </main>
    </div>
</template>
