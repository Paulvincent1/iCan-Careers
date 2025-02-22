<script setup>
import { ref } from 'vue';

const props = defineProps({
  question: String,
});

const isOpen = ref(false);
</script>

<template>
  <div class="border border-gray-300 rounded-lg shadow-md mb-2 transition-all duration-300">
    <!-- FAQ Question Button -->
    <button
      class="w-full flex items-center justify-between px-4 py-3 bg-gray-100 text-gray-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
      @click="isOpen = !isOpen"
    >
      <span>{{ question }}</span>
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6 transition-transform duration-200"
        :class="{ 'rotate-180': isOpen }"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- FAQ Answer -->
    <div
      class="px-4 bg-white text-gray-700 transition-all duration-300 ease-in-out overflow-hidden"
      :class="{ 'max-h-0 opacity-0': !isOpen, 'max-h-96 opacity-100': isOpen }"
    >
      <p v-html="$slots.default ? $slots.default()[0].children : ''"></p>
    </div>
  </div>
</template>
