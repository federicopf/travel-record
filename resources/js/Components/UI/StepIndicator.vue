<script setup>
defineProps({
    currentStep: {
        type: Number,
        required: true
    },
    totalSteps: {
        type: Number,
        required: true
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    }
});

const stepSizes = {
    sm: 'w-2 h-2',
    md: 'w-3 h-3',
    lg: 'w-4 h-4'
};
</script>

<template>
    <div class="flex justify-center">
        <div class="flex space-x-2">
            <div 
                v-for="step in totalSteps" 
                :key="step"
                :class="[
                    stepSizes[size],
                    'rounded-full transition-colors duration-300',
                    step <= currentStep ? `bg-${$colorScheme}-600` : 'bg-gray-300'
                ]"
            ></div>
        </div>
    </div>
</template>
