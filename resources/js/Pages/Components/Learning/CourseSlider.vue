<script setup>
import { ref } from "vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import { Navigation, Pagination } from "swiper/modules";

const props = defineProps({
  courses: {
    type: Array,
    required: true,
  },
  title: {
    type: String,
    required: true,
  },
});

const selectedVideo = ref(null);

// Extract YouTube video ID
const getYouTubeVideoId = (url) => {
  return url.split("v=")[1]?.split("&")[0] || "";
};

// Get thumbnail
const getYouTubeThumbnail = (url) => {
  const videoId = getYouTubeVideoId(url);
  return videoId ? `https://img.youtube.com/vi/${videoId}/0.jpg` : "";
};

// Get embed link
const getYouTubeEmbedUrl = (url) => {
  const videoId = getYouTubeVideoId(url);
  return videoId ? `https://www.youtube.com/embed/${videoId}?autoplay=1` : "";
};

// Handle click
const playVideo = (course) => {
  selectedVideo.value = getYouTubeEmbedUrl(course.image);
};
</script>

<template>
  <div class="my-8">
    <!-- Title -->
    <h2 class="text-2xl font-bold text-black mb-4">{{ title }}</h2>

    <!-- Swiper Slider -->
    <Swiper
      :modules="[Navigation, Pagination]"
      :slides-per-view="1"
      :space-between="20"
      :navigation="true"

      :breakpoints="{
        640: { slidesPerView: 2 },
        1024: { slidesPerView: 4 },
      }"
      class="w-full pb-12"
    >
      <!-- Loop through courses -->
      <SwiperSlide
        v-for="course in courses"
        :key="course.id"
        @click="playVideo(course)"
        class="cursor-pointer"
      >
        <div class="relative group rounded-lg overflow-hidden shadow-lg">
          <!-- Thumbnail -->
          <img
            :src="getYouTubeThumbnail(course.image)"
            :alt="course.title"
            class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105"
          />

          <!-- Overlay Play Button -->
          <div
            class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition"
          >
            <span
              class="bg-red-600 text-white px-4 py-2 rounded-full text-lg font-bold shadow-lg"
            >
              ▶ Play
            </span>
          </div>
        </div>

        <!-- Title -->
        <h3 class="mt-2 text-center font-semibold text-gray-800">
          {{ course.title }}
        </h3>
      </SwiperSlide>
    </Swiper>

    <!-- Video Player Modal -->
<div
  v-if="selectedVideo"
  class="fixed inset-0 z-[99999] flex items-center justify-center bg-black bg-opacity-75 px-4"
>
  <div class="w-full max-w-5xl bg-white p-4 rounded-lg shadow-lg">
    <!-- Responsive iframe -->
    <div class="relative w-full aspect-video">
      <iframe
        :src="selectedVideo"
        class="absolute inset-0 w-full h-full rounded-lg"
        frameborder="0"
        allow="autoplay; encrypted-media"
        allowfullscreen
      ></iframe>
    </div>

    <!-- Close button -->
    <button
      @click="selectedVideo = null"
      class="mt-4 bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition"
    >
      Close
    </button>
    
  </div>
</div>
  </div>
</template>

<style scoped>
.swiper-slide {
  display: flex;
  flex-direction: column;
  align-items: center;
}
</style>
