<script setup>
defineProps(["modelValue"]);
defineEmits(["update:modelValue"]);
</script>

<template>
    <div class="mb-6 flex justify-center">
      <div class="relative w-full md:w-1/2">
        <!-- Search Icon -->
        <i class="bi bi-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>

        <!-- Search Input -->
        <input
          :value="modelValue"
          @input="$emit('update:modelValue', $event.target.value)"
          type="text"
          placeholder="Search courses..."
          class="w-full px-10 py-2 border rounded-lg focus:ring focus:ring-blue-300"
        />
      </div>
    </div>
  </template>

