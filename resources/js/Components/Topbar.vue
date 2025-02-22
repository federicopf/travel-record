<script setup>
import { ref, onMounted } from 'vue';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';

const isMobileMenuOpen = ref(false);
const isOpenTest1 = ref(false);
const isOpenTest2 = ref(false);

const appName = import.meta.env.VITE_APP_NAME || 'Gestionale';

const closeMenus = () => {
    isOpenTest1.value = false;
    isOpenTest2.value = false;
};

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

onMounted(() => {
    document.addEventListener('click', closeMenus);
});
</script>

<template>
    <header class="bg-gradient-to-r from-pink-500 to-pink-700 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-6 flex items-center align-start py-4">
            <button @click.stop="toggleMobileMenu" class="md:hidden text-white focus:outline-none text-lg font-bold">
                {{ isMobileMenuOpen ? 'Chiudi' : 'Menu' }}
            </button>

            <h1 class="text-2xl font-bold tracking-wide hidden md:block">{{ appName }}</h1>

            <nav class="hidden md:flex items-center space-x-6">
                <Menu as="div" class="relative">
                    <MenuButton class="flex items-center text-lg font-semibold hover:text-gray-200 transition">
                        Test1
                    </MenuButton>
                    <transition
                        enter-active-class="transition duration-200 ease-out transform scale-95 opacity-0"
                        enter-to-class="transform scale-100 opacity-100"
                        leave-active-class="transition duration-150 ease-in transform scale-100 opacity-100"
                        leave-to-class="transform scale-95 opacity-0"
                    >
                        <MenuItems class="absolute left-0 mt-2 w-48 bg-white text-gray-800 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                            <MenuItem>
                                <button class="block w-full text-left px-4 py-2 hover:bg-gray-100">Opzione 1</button>
                            </MenuItem>
                            <MenuItem>
                                <button class="block w-full text-left px-4 py-2 hover:bg-gray-100">Opzione 2</button>
                            </MenuItem>
                        </MenuItems>
                    </transition>
                </Menu>

                <Menu as="div" class="relative">
                    <MenuButton class="flex items-center text-lg font-semibold hover:text-gray-200 transition">
                        Test2
                    </MenuButton>
                    <transition
                        enter-active-class="transition duration-200 ease-out transform scale-95 opacity-0"
                        enter-to-class="transform scale-100 opacity-100"
                        leave-active-class="transition duration-150 ease-in transform scale-100 opacity-100"
                        leave-to-class="transform scale-95 opacity-0"
                    >
                        <MenuItems class="absolute left-0 mt-2 w-48 bg-white text-gray-800 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                            <MenuItem>
                                <button class="block w-full text-left px-4 py-2 hover:bg-gray-100">Opzione A</button>
                            </MenuItem>
                            <MenuItem>
                                <button class="block w-full text-left px-4 py-2 hover:bg-gray-100">Opzione B</button>
                            </MenuItem>
                        </MenuItems>
                    </transition>
                </Menu>
            </nav>
        </div>

        <div v-if="isMobileMenuOpen" class="md:hidden bg-pink-600">
            <nav class="flex flex-col space-y-2 py-4 px-6">
                <button class="text-white text-lg font-semibold hover:text-gray-300" @click="toggleMobileMenu">
                    Test1
                </button>
                <button class="text-white text-lg font-semibold hover:text-gray-300" @click="toggleMobileMenu">
                    Test2
                </button>
                <button class="text-white text-lg font-semibold hover:text-gray-300">
                    Logout
                </button>
            </nav>
        </div>
    </header>
</template>
