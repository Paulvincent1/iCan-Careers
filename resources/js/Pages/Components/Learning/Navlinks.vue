<script setup>
import { computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

const links = [
  { name: "Job Seeking", url: "/jobseeking" },
  { name: "Health & Awareness", url: "/health" },
  { name: "Creative & Design", url: "/creative" },
];

const isActive = (url) => computed(() => usePage().url.startsWith(url)).value;
</script>

<style scoped>
/* Additional styling (if needed) */
</style>
<template>

    <nav class="bg-[#fa8334] h-[500px] shadow-md p-4 border-t border-[#fa8334]">
        <div class="text-center">
            <h2>Look for the right course for you</h2>

        </div>
      <ul class="flex justify-center space-x-6">

        <li v-for="(link, index) in links" :key="index">
          <Link
            :href="link.url"
            class="px-4 py-2 rounded transition-transform duration-300 hover:scale-105 text-white border border-black"
            :class="{ 'font-semibold border-b-2 border-[#fa8334]': isActive(link.url) }"
          >
            {{ link.name }}
          </Link>
        </li>
      </ul>
    </nav>
  </template>


