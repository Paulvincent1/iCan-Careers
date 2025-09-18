<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

// Props
const props = defineProps({
    jobs: Array
});

// Function to split jobs into chunks of 3 per row
const chunkedJobs = computed(() => {
    const chunkSize = 3;
    let result = [];
    for (let i = 0; i < props.jobs.length; i += chunkSize) {
        result.push(props.jobs.slice(i, i + chunkSize));
    }
    return result;
});
</script>

<template>
  <section class="p-16 bg-gray-50">
    <h2 class="text-3xl font-semibold text-center text-gray-800">
      Featured Jobs
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
      <template v-for="(row, rowIndex) in chunkedJobs" :key="rowIndex">
        <div
          v-for="job in row"
          :key="job.id"
          class="p-6 bg-white shadow-lg rounded-xl transition-transform transform hover:scale-105"
        >
          <!-- Each card is clickable -->
          <Link
            :href="route('register.create')"
            class="block hover:no-underline"
          >
            <h3 class="text-lg font-bold text-[#fa8334]">{{ job.title }}</h3>
            <p class="text-sm text-gray-600">{{ job.company }}</p>
            <p class="mt-2 text-gray-700">{{ job.description }}</p>
          </Link>
        </div>
      </template>
    </div>
  </section>
</template>
