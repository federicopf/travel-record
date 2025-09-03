<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
        validator: (value) => ['button', 'submit', 'reset'].includes(value)
    },
    variant: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'danger', 'success', 'warning'].includes(value)
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    disabled: {
        type: Boolean,
        default: false
    },
    loading: {
        type: Boolean,
        default: false
    },
    loadingText: {
        type: String,
        default: 'Caricamento...'
    }
});

const emit = defineEmits(['click']);

const handleClick = (event) => {
    if (!disabled && !loading) {
        emit('click', event);
    }
};

const buttonClasses = computed(() => {
    const baseClasses = 'font-semibold rounded transition disabled:opacity-50 disabled:cursor-not-allowed';
    
    const sizeClasses = {
        sm: 'px-3 py-1 text-sm',
        md: 'px-4 py-2',
        lg: 'px-6 py-3 text-lg'
    };
    
    const variantClasses = {
        primary: `bg-${$colorScheme}-600 text-white hover:bg-${$colorScheme}-700`,
        secondary: 'bg-gray-500 text-white hover:bg-gray-600',
        danger: 'bg-red-500 text-white hover:bg-red-600',
        success: 'bg-green-600 text-white hover:bg-green-700',
        warning: 'bg-orange-500 text-white hover:bg-orange-600'
    };
    
    return `${baseClasses} ${sizeClasses[size]} ${variantClasses[variant]}`;
});
</script>

<template>
    <button
        :type="type"
        :disabled="disabled || loading"
        :class="buttonClasses"
        @click="handleClick"
    >
        <slot v-if="!loading" />
        <span v-else>{{ loadingText }}</span>
    </button>
</template>
