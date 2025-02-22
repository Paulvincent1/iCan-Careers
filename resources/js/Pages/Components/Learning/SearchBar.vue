<template>
  <div class="mb-6 flex justify-between items-center">
    <input
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      type="text"
      placeholder="Search courses..."
      class="w-full md:w-1/2 px-4 py-2 border rounded-lg focus:ring focus:ring-blue-300"
    />
  </div>
</template>

<script>
export default {
  props: ["modelValue"],
  emits: ["update:modelValue"],
};
</script>
