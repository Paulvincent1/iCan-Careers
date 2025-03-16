<script setup>
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import { Navigation, Pagination } from "swiper/modules";
import CourseCard from "./CourseCard.vue";

defineProps({
  courses: {
    type: Array,
    required: true,
  },
  title: {
    type: String,
    required: true,
  },
});
</script>

<template>
  <div class="my-8">
    <!-- Title for the course section -->
    <h2 class="text-2xl font-bold text-black mb-4">{{ title }}</h2>

    <!-- Swiper Slider -->
    <Swiper
      :modules="[Navigation, Pagination]"
      :slides-per-view="1"
      :space-between="20"
      :navigation="true"
      :pagination="{ clickable: true }"
      :breakpoints="{
        640: {
          slidesPerView: 2,
        },
        1024: {
          slidesPerView: 3,
        },
      }"
      class="w-full pb-12"
    >
      <!-- Loop through courses and create slides -->
      <SwiperSlide v-for="course in courses" :key="course.id">
        <CourseCard :course="course" />
      </SwiperSlide>
    </Swiper>
  </div>
</template>

<style scoped>
/* Custom styles for the slider */
.swiper-slide {
  display: flex;
  justify-content: center;
  align-items: center;
  
}
</style>
