<script setup>
import { computed } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'success',
        validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
    },
    message: {
        type: String,
        required: true
    },
    show: {
        type: Boolean,
        default: true
    },
    autoHide: {
        type: Boolean,
        default: false
    },
    duration: {
        type: Number,
        default: 3000
    }
});

const emit = defineEmits(['close']);

const messageClasses = computed(() => {
    const baseClasses = 'p-3 rounded-md text-center transition-all duration-300';
    
    const typeClasses = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        warning: 'bg-orange-500 text-white',
        info: 'bg-blue-500 text-white'
    };
    
    return `${baseClasses} ${typeClasses[props.type]}`;
});

const closeMessage = () => {
    emit('close');
};

// Auto-hide functionality
if (props.autoHide && props.show) {
    setTimeout(() => {
        closeMessage();
    }, props.duration);
}
</script>

<template>
    <transition name="fade">
        <div v-if="show" :class="messageClasses">
            <div class="flex items-center justify-between">
                <span>{{ message }}</span>
                <button 
                    v-if="!autoHide"
                    @click="closeMessage"
                    class="ml-2 text-white hover:text-gray-200"
                >
                    ×
                </button>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
