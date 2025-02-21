<template>
  <section class="mb-5">
    <div class="">
      <h2 class="text-center m-10 text-gray-500 text-[40px]">
        Frequently Asked Questions
      </h2>
    </div>

    <div class="mx-4 sm:mx-10 md:mx-20 lg:mx-40 mt-0">
      <div v-for="(section, sectionIndex) in faqs" :key="sectionIndex">
        <span class="text-lg font-bold text-gray-500 mt-5 block">{{ section.sectionTitle }}</span>
        <FAQitem
          v-for="(faq, index) in section.faqs"
          :key="index"
          :question="faq.question"
        >
          {{ faq.answer }}
        </FAQitem>
      </div>
    </div>
  </section>
</template>

<script setup>
import FAQitem from './FAQItem.vue';

defineProps({
  faqs: Array,
});
</script>
