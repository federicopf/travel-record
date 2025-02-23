<script setup>
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';

const isMobileMenuOpen = ref(false);

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

onMounted(() => {
    document.addEventListener('click', () => {
        isMobileMenuOpen.value = false;
    });
});
</script>

<template>
    <header class="bg-gradient-to-r from-pink-400 to-pink-600 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between py-4">
            <!-- Navbar Desktop -->
            <nav class="hidden md:flex items-center space-x-8">
                <Link :href="route('home')" class="text-lg font-semibold hover:text-gray-200 transition">
                    Home
                </Link>
                <span class="text-lg font-semibold hover:text-gray-200 transition cursor-pointer">
                    Mappa
                </span>
            </nav>

            <!-- Bottone Mobile -->
            <button @click.stop="toggleMobileMenu" class="md:hidden text-white text-lg font-bold focus:outline-none">
                {{ isMobileMenuOpen ? '✖ Chiudi' : '☰ Menu' }}
            </button>
        </div>

        <!-- Menu Mobile -->
        <div v-if="isMobileMenuOpen" class="md:hidden bg-pink-500 py-4">
            <nav class="flex flex-col space-y-2 px-6">
                <Link :href="route('home')" class="text-white text-lg font-semibold hover:text-gray-300">
                    Home
                </Link>
                <span class="text-white text-lg font-semibold hover:text-gray-300 cursor-pointer">
                    Mappa
                </span>
            </nav>
        </div>
    </header>
</template>
