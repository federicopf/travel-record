<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import ChangePasswordModal from '@/Components/Modal/ChangePasswordModal.vue';
import ChangeMapPointerModal from '@/Components/Modal/ChangeMapPointerModal.vue';

// Stati per menu e modali
const isMobileMenuOpen = ref(false);
const isOptionsOpen = ref(false);
const changePasswordModal = ref(null);
const changePointerModal = ref(null);

// Dati dell'utente
const page = usePage();
const user = page.props.auth?.user ?? null;
const selectedTheme = ref(user?.theme_id ?? -1);

// Temi disponibili
const themes = [
    { id: 0, name: 'Rosso' },
    { id: 1, name: 'Blu' },
    { id: 2, name: 'Verde' },
    { id: 3, name: 'Rosa' }
];

// Aprire e chiudere il menu
const toggleMobileMenu = () => (isMobileMenuOpen.value = !isMobileMenuOpen.value);
const toggleOptionsMenu = (event) => {
    event.stopPropagation();
    isOptionsOpen.value = !isOptionsOpen.value;
};
const closeMenus = () => {
    isMobileMenuOpen.value = false;
    isOptionsOpen.value = false;
};

// Event listeners
onMounted(() => document.addEventListener('click', closeMenus));
onUnmounted(() => document.removeEventListener('click', closeMenus));

// Logout
const logout = () => router.post(route('logout'));

// Apertura delle modali
const openPasswordModal = () => changePasswordModal.value.openModal();
const openPointerModal = () => changePointerModal.value.openModal();

// Cambia il tema e aggiorna la pagina
const changeTheme = () => {
    router.post(route('theme.change'), { theme_id: selectedTheme.value }, {
        preserveState: true,
        onSuccess: () => window.location.reload()
    });
};
</script>

<template>
    <header :class="`bg-gradient-to-r from-${$colorScheme}-400 to-${$colorScheme}-600 text-white shadow-md`">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between py-4">
            <nav class="hidden md:flex items-center space-x-8">
                <Link :href="route('home')" class="text-lg font-semibold hover:text-gray-200 transition">Home</Link>
                <Link :href="route('map')" class="text-lg font-semibold hover:text-gray-200 transition cursor-pointer">Mappa</Link>
            </nav>

            <div class="flex items-center space-x-4">
                <button @click.stop="toggleMobileMenu" class="md:hidden text-white text-lg font-bold focus:outline-none">
                    {{ isMobileMenuOpen ? 'Chiudi' : 'Menu' }}
                </button>
                
                <div class="relative">
                    <button @click.stop="toggleOptionsMenu" class="text-white text-lg font-bold focus:outline-none">
                        Opzioni
                    </button>
                    <div v-if="isOptionsOpen" 
                        class="absolute left-0 md:left-auto md:right-0 mt-2 w-48 bg-white text-gray-800 shadow-lg rounded-lg overflow-hidden z-50">
                    <!-- Cambia tema -->
                        <div class="px-4 py-2">
                            <label for="themeSelect" class="block text-sm font-medium text-gray-700">Tema:</label>
                            <select id="themeSelect" v-model="selectedTheme" @change="changeTheme" @click.stop
                                class="block w-full mt-1 p-2 border border-gray-300 rounded-lg">
                                <option value="-1" disabled>Cambia tema...</option>
                                <option v-for="theme in themes" :key="theme.id" :value="theme.id">
                                    {{ theme.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Cambia Map Pointer -->
                        <button @click="openPointerModal" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
                            Cambia segnaposto
                        </button>

                        <!-- Cambia Password -->
                        <button @click="openPasswordModal" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
                            Cambia Password
                        </button>

                        <!-- Logout -->
                        <button @click="logout" class="w-full text-left px-4 py-2 hover:bg-gray-100">
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="isMobileMenuOpen" :class="`md:hidden bg-${$colorScheme}-500 py-4`">
            <nav class="flex flex-col space-y-2 px-6">
                <Link :href="route('home')" class="text-white text-lg font-semibold hover:text-gray-300">Home</Link>
                <Link :href="route('map')" class="text-white text-lg font-semibold hover:text-gray-300">Mappa</Link>
            </nav>
        </div>
    </header>

    <ChangePasswordModal ref="changePasswordModal" />
    <ChangeMapPointerModal ref="changePointerModal" />
</template>
