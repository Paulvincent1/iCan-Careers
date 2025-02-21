<script setup>
import { ref } from 'vue';

const props = defineProps({
  question: String,
});

const isOpen = ref(false); // Default should be closed

const toggleCollapse = () => {
  isOpen.value = !isOpen.value;
};
</script>

<template>
  <div class="border border-gray-300 rounded-lg shadow-md mb-2">
    <button
      class="w-full flex items-center justify-between px-4 py-3 bg-gray-100 text-gray-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
      @click="toggleCollapse"
    >
      <span>{{ question }}</span>
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6 transition-transform"
        :class="{ 'rotate-180': isOpen }"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>
    <div class="px-4 py-3 bg-white text-gray-700 transition-all duration-300 ease-in-out" :class="{ hidden: !isOpen }">
      <slot></slot>
    </div>
  </div>
</template>
