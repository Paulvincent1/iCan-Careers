<template>
  <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-md hover:shadow-lg transition">
    <img :src="course.image" alt="Course Thumbnail" class="rounded-lg mb-4 h-40 w-full object-cover" />
    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ course.title }}</h2>
    <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">{{ course.description }}</p>
    <button class="mt-4 px-4 py-2 bg-[#fa8334] text-white rounded-lg transition">
      Start Learning
    </button>
  </div>
</template>

<script>
export default {
  props: ["course"],
};
</script>
