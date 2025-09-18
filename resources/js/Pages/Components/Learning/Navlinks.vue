<script setup>
import { computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faYoutube } from "@fortawesome/free-brands-svg-icons";

library.add(faYoutube);

defineProps({
  channelLink: {
    type: String,
    required: true,
  },
});

const links = [
  { name: "Job Seeking", url: "/health" },
  { name: "Health & Awareness", url: "/jobseeking" },
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
  <section
    class="relative bg-[#fa8334] py-12 md:py-16 overflow-hidden min-h-[550px]"
  >
  <!-- Gradient overlay (desktop only) -->
    <div
      class="hidden lg:block absolute inset-0 bg-gradient-to-l from-white via-white/40 to-transparent"
    ></div>
    <!-- Background image (desktop only) -->
    <div
      class="hidden lg:block absolute top-0 right-0 h-full w-2/5 rounded-l-[3rem] bg-cover bg-center "
      style="background-image: url('/assets/hero.jpg'); -webkit-mask-image: linear-gradient(to left, black 80%, transparent 100%); -webkit-mask-repeat: no-repeat; -webkit-mask-size: cover; mask-image: linear-gradient(to left, black 80%, transparent 100%); mask-repeat: no-repeat; mask-size: cover;"
    ></div>



    <!-- Content wrapper -->
    <div
      class="relative max-w-7xl mx-auto px-6 lg:px-12 grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center"
    >
      <!-- Left content -->
      <div>
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-white mb-4">
          Look for the right course for you
        </h2>
        <p class="text-base md:text-lg text-white mb-6 md:mb-8">
          Explore categories tailored to your career growth, skills development,
          and personal interests. Choose a path that fits your goals.
        </p>

        <a
          :href="channelLink"
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-6 py-3 text-red-600 rounded-xl transition duration-300 bg-white shadow hover:shadow-lg border hover:border-[#fa8334] hover:scale-105 font-medium text-sm md:text-base"
        >
          <font-awesome-icon :icon="['fab', 'youtube']" size="lg" />
          Click Here to Watch More
        </a>
      </div>

      <!-- Right side links -->
      <div class="relative z-[10] mt-14">
        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <li v-for="(link, index) in links" :key="index">
            <Link
              :href="link.url"
              class="block w-full px-4 md:px-6 py-3 md:py-4 rounded-xl transition duration-300 bg-white shadow hover:shadow-lg text-gray-800 border hover:border-[#fa8334] hover:scale-105 text-center font-medium text-sm md:text-base"
              :class="{
                'bg-[#fa8334] text-white font-semibold': isActive(link.url).value,
              }"
            >
              {{ link.name }}
            </Link>
          </li>
        </ul>
      </div>
    </div>
  </section>
</template>
