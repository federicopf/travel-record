<script setup>
    import { ref } from 'vue';
    import { route } from 'ziggy-js';
    import { useForm } from '@inertiajs/vue3';
    import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/solid';

    const step = ref(1);
    const isCouple = ref(false);
    const showPassword = ref(false);

    const form = useForm({
        type: 'individual', // individual | couple
        name: '',
        partner_name: '',
        email: '',
        username: '',
        password: '',
        password_confirmation: ''
    });

    // Cambia la visibilità della password
    const togglePasswordVisibility = () => {
        showPassword.value = !showPassword.value;
    };

    // Procedi allo step successivo con validazione lato frontend
    const nextStep = () => {
        // Reset degli errori precedenti
        form.errors.name = '';
        form.errors.partner_name = '';

        // Validazione per "Individuale"
        if (form.type === 'individual' && !form.name) {
            form.errors.name = 'Il nome è obbligatorio.';
            return;
        }

        // Validazione per "Coppia"
        if (form.type === 'couple') {
            if (!form.name) {
                form.errors.name = 'Il nome è obbligatorio.';
            }
            if (!form.partner_name) {
                form.errors.partner_name = 'Il nome del partner è obbligatorio.';
            }
            if (form.errors.name || form.errors.partner_name) {
                return;
            }
        }

        step.value = 2;
    };


    // Torna indietro allo step 1
    const prevStep = () => {
        step.value = 1;
    };

    const submitForm = () => {
        form.post(route('register.post'), {
            onError: (errors) => {
                console.log("Errori di validazione:", errors);
            }
        });
    };
</script>

<template>  
    <div class="min-h-screen flex-col flex items-center justify-start pt-14 bg-gradient-to-r from-blue-400 to-blue-600">
        <!-- Logo come sfondo di un div -->
        <div 
            class="w-52 h-52 mx-auto mb-0 bg-no-repeat bg-center bg-contain"
            style="background-image: url('/assets/icon/Logo/White/Travel%20Record%20Logo%20(White).png');">
        </div>
        
        <div class="bg-white p-8 rounded-2xl shadow-xl w-96">
            
            <h1 class="text-3xl font-bold text-center text-blue-600 mb-2">
                {{ step === 1 ? 'Registrazione' : 'Credenziali' }}
            </h1>

            <p class="text-gray-500 text-center mb-6">
                {{ step === 1 ? 'Scegli il tipo di registrazione' : 'Completa la registrazione' }}
            </p>

            <form @submit.prevent="submitForm">
                <div v-if="step === 1">
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold">Tipo di registrazione</label>
                        <div class="flex gap-4 mt-2">
                            <button 
                                type="button" 
                                @click="form.type = 'individual'; isCouple = false"
                                :class="form.type === 'individual' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                                class="w-1/2 py-2 rounded-lg transition"
                            >
                                Individuale
                            </button>
                            <button 
                                type="button" 
                                @click="form.type = 'couple'; isCouple = true"
                                :class="form.type === 'couple' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                                class="w-1/2 py-2 rounded-lg transition"
                            >
                                Coppia
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <input 
                            type="text"
                            v-model="form.name"
                            placeholder="Nome"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                        />
                        <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
                    </div>

                    <!-- Nome Partner (se coppia) -->
                    <div v-if="isCouple" class="mb-4">
                        <input 
                            type="text"
                            v-model="form.partner_name"
                            placeholder="Nome Partner"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                        />
                        <p v-if="form.errors.partner_name" class="text-red-500 text-sm mt-1">{{ form.errors.partner_name }}</p>
                    </div>


                    <button
                        type="button"
                        @click="nextStep"
                        class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-300"
                    >
                        Avanti
                    </button>

                    <!-- Link alla Registrazione -->
                    <p class="text-gray-500 text-sm text-center mt-4">
                        Hai già un account? 
                        <a :href="route('login')" class="text-blue-600 font-semibold hover:underline">Login</a>
                    </p>
                </div>

                <!-- Step 2: Credenziali -->
                <div v-else>
                    <div class="mb-4">
                        <input 
                            type="text"
                            v-model="form.username"
                            placeholder="Username"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                            required
                        />
                        <p v-if="form.errors.username" class="text-red-500 text-sm mt-1">{{ form.errors.username }}</p>
                    </div>

                    <div class="mb-4">
                        <input 
                            type="email"
                            v-model="form.email"
                            placeholder="Email"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                            required
                        />
                        <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
                    </div>

                    <!-- Campo Password con Occhio -->
                    <div class="mb-4 relative">
                        <input 
                            :type="showPassword ? 'text' : 'password'"
                            v-model="form.password"
                            placeholder="Password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 pr-10"
                            required
                        />
                        <button type="button" @click="togglePasswordVisibility"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                            <EyeIcon v-if="!showPassword" class="w-5 h-5" />
                            <EyeSlashIcon v-else class="w-5 h-5" />
                        </button>
                        <p v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</p>
                    </div>

                    <!-- Campo Conferma Password con Occhio -->
                    <div class="mb-4 relative">
                        <input 
                            :type="showPassword ? 'text' : 'password'"
                            v-model="form.password_confirmation"
                            placeholder="Conferma Password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 pr-10"
                            required
                        />
                        <button type="button" @click="togglePasswordVisibility"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                            <EyeIcon v-if="!showPassword" class="w-5 h-5" />
                            <EyeSlashIcon v-else class="w-5 h-5" />
                        </button>
                        <p v-if="form.errors.password_confirmation" class="text-red-500 text-sm mt-1">{{ form.errors.password_confirmation }}</p>
                    </div>

                    <!-- Pulsanti Indietro e Registrati -->
                    <div class="flex gap-4">
                        <button
                            type="button"
                            @click="prevStep"
                            class="w-1/2 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400 transition duration-300"
                        >
                            Indietro
                        </button>

                        <button
                            type="submit"
                            class="w-1/2 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-300"
                        >
                            Registrati
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template> 
