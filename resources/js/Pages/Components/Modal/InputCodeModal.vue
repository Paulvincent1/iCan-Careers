<script setup>
const emit = defineEmits(["closeModal"]);
</script>

<template>
  <div
    class="fixed inset-0 z-50 grid place-items-center backdrop-brightness-50"
  >
    <div
      ref="modalContent"
      class="relative rounded-lg bg-white p-6 shadow-lg"
    >
      <!-- ❌ Close button -->
      <button
        @click="emit('closeModal')"
        class="absolute right-2 top-2 text-gray-500 hover:text-gray-800"
      >
        ✕
      </button>

      <!-- Slot for modal content -->
      <slot></slot>
    </div>
  </div>
</template>
