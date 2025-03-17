<script setup>
import { ref, computed, onMounted, nextTick } from "vue";
import UseCourses from "./UseCourses.vue";

const props = defineProps({
  category: String, // Define category prop to filter courses
});

const courseComponent = ref(null);
const allCourses = ref([]);
const selectedVideo = ref(null);


onMounted(async () => {
  await nextTick(); // Wait for the DOM to update

  if (courseComponent.value) {
    allCourses.value = [
      ...(courseComponent.value.courses || []),
      ...(courseComponent.value.courses2 || []),
      ...(courseComponent.value.courses3 || []),
      ...(courseComponent.value.courses4 || []),
      ...(courseComponent.value.courses5 || []),
      ...(courseComponent.value.courses6 || []),
      ...(courseComponent.value.courses7 || []),
      ...(courseComponent.value.courses8 || []),
      
    ];
  }
});

// Compute courses based on selected category
const filteredCourses = computed(() => {
  if (!props.category || props.category === "all") return allCourses.value;
  return allCourses.value.filter(course => course.category === props.category);
});

// Function to extract YouTube video ID
const getYouTubeEmbedUrl = (url) => {
  const videoId = url.split("v=")[1]?.split("&")[0];
  return videoId ? `https://www.youtube.com/embed/${videoId}` : "";
};

// Function to play video
const playVideo = (course) => {
  selectedVideo.value = getYouTubeEmbedUrl(course.image);
};
</script>

<template>
  <UseCourses ref="courseComponent" /> <!-- Fetch courses -->

  <div class="overflow-y-auto h-[600px] p-4 space-y-4 p-5">
    <!-- Course List -->
    <div v-for="course in filteredCourses" :key="course.id"
      class="flex items-center bg-white rounded-lg shadow-md p-4 cursor-pointer transition-transform transform hover:scale-105"
      @click="playVideo(course)">

      <!-- Thumbnail Image -->
      <img :src="`https://img.youtube.com/vi/${course.image.split('v=')[1]?.split('&')[0]}/0.jpg`"
        :alt="course.title" class="w-32 h-32 object-cover rounded-lg mr-4" />

      <!-- Title and Description -->
      <div>
        <h3 class="text-xl font-semibold mb-2">{{ course.title }}</h3>
        <p class="text-gray-600" v-html="course.description"></p>
      </div>
    </div>

    <!-- Video Player Modal -->
    <div v-if="selectedVideo" class="fixed top-0 left-0 w-full h-full flex items-center justify-center bg-black bg-opacity-75 z-50">
      <div class="bg-white p-4 rounded-lg shadow-lg">
        <iframe :src="selectedVideo" class="w-[560px] h-[315px] rounded-lg" frameborder="0" allowfullscreen></iframe>
        <button @click="selectedVideo = null" class="mt-4 bg-red-500 text-white px-4 py-2 rounded-lg">Close</button>
      </div>
    </div>
  </div>
</template>
