<script setup>
import { computed, ref } from "vue";

// Define props for course data
const props = defineProps({
  course: {
    type: Object,
    required: false,
  },
  course2: {
    type: Object,
    required: false,
  },
  course3: {
    type: Object,
    required: false,
  },
});

// Check if the image URL is a YouTube link
const isYouTubeLink = (url) => {
  return url.includes("youtube.com") || url.includes("youtu.be");
};

// Convert YouTube watch URL to embed URL
const getYouTubeEmbedUrl = (url) => {
  const videoId = url.split("v=")[1]?.split("&")[0]; // Extract video ID properly
  return `https://www.youtube.com/embed/${videoId}`;
};

// Determine which course to display
const activeCourse = computed(() => {
  return props.course || props.course2 || props.course3;
});

// State for expanding/collapsing the description
const showFullDescription = ref(false);

// Truncated description with 'See More'
const truncatedDescription = computed(() => {
  if (!activeCourse.value) return "";
  const desc = activeCourse.value.description;
  return desc.length > 100 ? desc.substring(0, 100) + "..." : desc;
});

// Check if the description contains "Watch here" and replace it with a clickable link
const formattedDescription = computed(() => {
  if (!activeCourse.value) return "";

  let desc = activeCourse.value.description;

  // Check if it's a YouTube video, then link the description
  if (isYouTubeLink(activeCourse.value.image)) {
    desc = desc.replace(
      /Watch here/gi,
      `<a href="${activeCourse.value.image}" target="_blank" class="text-blue-500 underline">Watch here</a>`
    );
  }

  return desc;
});
</script>

<template>
    <div
      class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition-transform transform hover:scale-105 w-80 h-80 flex flex-col"
    >
      <!-- Video Player or YouTube Embed -->
      <div class="w-full h-40 flex-shrink-0">
        <video
          v-if="activeCourse && !isYouTubeLink(activeCourse.image)"
          class="w-full h-full object-cover rounded-md"
          controls
        >
          <source :src="activeCourse.image" type="video/mp4" />
          Your browser does not support the video tag.
        </video>
        <iframe
          v-else-if="activeCourse && isYouTubeLink(activeCourse.image)"
          class="w-full h-full rounded-md"
          :src="getYouTubeEmbedUrl(activeCourse.image)"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen
        ></iframe>
      </div>

      <!-- Course Content -->
      <div class="mt-2 flex flex-col flex-grow">
        <!-- Course Title -->
        <h2 class="text-lg font-semibold text-black">
          {{ activeCourse.title }}
        </h2>

        <!-- Course Description with 'See More' -->
        <div class="text-md text-gray-500 mt-1 max-h-16 overflow-hidden text-ellipsis">
          <span v-html="showFullDescription ? formattedDescription : truncatedDescription"></span>
        </div>

        <button
          v-if="activeCourse.description.length > 100"
          @click="showFullDescription = !showFullDescription"
          class="text-blue-500 mt-auto hover:underline self-start"
        >
          {{ showFullDescription ? "See Less" : "See More" }}
        </button>
      </div>
    </div>
  </template>
