<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import ChangePasswordModal from '@/Components/ChangePasswordModal.vue';

const isMobileMenuOpen = ref(false);
const isOptionsOpen = ref(false);
const changePasswordModal = ref(null);

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const toggleOptionsMenu = (event) => {
    event.stopPropagation();
    isOptionsOpen.value = !isOptionsOpen.value;
};

const closeMenus = () => {
    isMobileMenuOpen.value = false;
    isOptionsOpen.value = false;
};

onMounted(() => document.addEventListener('click', closeMenus));
onUnmounted(() => document.removeEventListener('click', closeMenus));

const logout = () => {
    router.post(route('logout'));
};

const openPasswordModal = () => {
    changePasswordModal.value.openModal();
};
</script>

<template>
    <header class="bg-gradient-to-r from-pink-400 to-pink-600 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between py-4">
            <nav class="hidden md:flex items-center space-x-8">
                <Link :href="route('home')" class="text-lg font-semibold hover:text-gray-200 transition">
                    Home
                </Link>
                <Link :href="route('map')" class="text-lg font-semibold hover:text-gray-200 transition cursor-pointer">
                    Mappa
                </Link>
            </nav>

            <div class="flex items-center space-x-4">
                <div class="relative">
                    <button @click.stop="toggleOptionsMenu" class="text-white text-lg font-bold focus:outline-none">
                        Opzioni
                    </button>
                    <div v-if="isOptionsOpen" class="absolute right-0 mt-2 w-48 bg-white text-gray-800 shadow-lg rounded-lg overflow-hidden z-50">
                        <button @click="openPasswordModal" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
                            Cambia Password
                        </button>
                        <button @click="logout" class="w-full text-left px-4 py-2 hover:bg-gray-100">
                            Logout
                        </button>
                    </div>
                </div>

                <button @click.stop="toggleMobileMenu" class="md:hidden text-white text-lg font-bold focus:outline-none">
                    {{ isMobileMenuOpen ? 'Chiudi' : 'Menu' }}
                </button>
            </div>
        </div>

        <div v-if="isMobileMenuOpen" class="md:hidden bg-pink-500 py-4">
            <nav class="flex flex-col space-y-2 px-6">
                <Link :href="route('home')" class="text-white text-lg font-semibold hover:text-gray-300">
                    Home
                </Link>
                <Link :href="route('map')" class="text-white text-lg font-semibold hover:text-gray-300">
                    Mappa
                </Link>
            </nav>
        </div>
    </header>

    <ChangePasswordModal ref="changePasswordModal" />
</template>
