<script setup>
import { computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

const links = [
  { name: "Job Seeking", url: "/jobseeking" },
  { name: "Health & Awareness", url: "/health" },
  { name: "Creative & Design", url: "/creative" },
  { name: "Technology & IT", url: "/technology" },
  { name: "Business & Marketing", url: "/business" },
  { name: "Education & Training", url: "/education" },
  { name: "Finance & Accounting", url: "/finance" },
  { name: "Personal Development", url: "/personal" },
];

const isActive = (url) => computed(() => usePage().url.startsWith(url));
</script>

<template>
  <nav class="bg-[#fa8334] py-6 shadow-md border-t border-[#fa8334]">
    <div class="text-center text-white mb-4">
      <h2 class="text-2xl font-semibold">Look for the right course for you</h2>
    </div>

    <div class="flex justify-center items-start px-6">
      <!-- Buttons Grid -->
      <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        <li v-for="(link, index) in links" :key="index">
          <Link
            :href="link.url"
            class="block w-full px-4 py-2 rounded-lg transition duration-300 hover:scale-105 text-white border border-white hover:bg-white hover:text-black text-center"
            :class="{ 'font-semibold bg-white text-[#fa8334]': isActive(link.url).value }"
          >
            {{ link.name }}
          </Link>
        </li>
      </ul>
    </div>
  </nav>
</template>
