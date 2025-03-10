<script setup>
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/pagination";
import "swiper/css/navigation";
import { Pagination, Autoplay, Navigation } from "swiper/modules";

defineProps(["slides"]);
</script>

<template>
  <div class="relative">
    <Swiper
      :modules="[Pagination, Autoplay, Navigation]"
      :slides-per-view="1"
      :space-between="20"
      :pagination="{ clickable: true }"
      :navigation="true"
      :autoplay="{ delay: 3000 }"
      class="mb-8 rounded-xl overflow-hidden shadow-lg"
    >
      <SwiperSlide v-for="slide in slides" :key="slide.id">
        <div class="relative h-64">
          <img :src="slide.image" class="w-full h-full object-cover brightness-75" />
          <div class="absolute inset-0 flex flex-col items-center justify-center text-white text-center">
            <h2 class="text-2xl font-bold">{{ slide.title }}</h2>
            <p class="text-lg mt-2">{{ slide.description }}</p>
          </div>
        </div>
      </SwiperSlide>
    </Swiper>

   
  </div>
</template>
