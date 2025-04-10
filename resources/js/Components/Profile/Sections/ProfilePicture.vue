<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  username: String,
  name: String,
  imgSrc: String, // preview opzionale
  size: { type: String, default: 'w-16 h-16' },
  fontSize: { type: String, default: 'text-xl' }
})


const imageUrl = computed(() => `/profile/${props.username}/photo`)

const initials = computed(() => {
  if (!props.name) return 'U'
  return props.name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
})

const imageError = ref(false)

const onError = () => {
  imageError.value = true
}
</script>

<template>
  <div
    v-if="!imageError"
    class="rounded-full overflow-hidden border shadow shrink-0"
    :class="size"
  >
    <img
     :src="imgSrc || imageUrl"
     :alt="`Foto profilo di ${name}`"
      class="w-full h-full object-cover"
      @error="onError"
    />
  </div>

  <div
    v-else
    class="flex items-center justify-center bg-gray-200 text-gray-600 font-semibold rounded-full border shadow shrink-0"
    :class="[size, fontSize]"
  >
    {{ initials }}
  </div>
</template>
