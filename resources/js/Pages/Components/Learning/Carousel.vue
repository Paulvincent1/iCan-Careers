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
      
      :autoplay="{ delay: 3000 }"
      class="mb-8 rounded-xl overflow-hidden"
    >
      <SwiperSlide v-for="slide in slides" :key="slide.id">
        <div class="relative h-[450px]">
          <img :src="slide.image" class="w-full h-full object-cover brightness-100" />

        </div>
      </SwiperSlide>
    </Swiper>


  </div>
</template>
