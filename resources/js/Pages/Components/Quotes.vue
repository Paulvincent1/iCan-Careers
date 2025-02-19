<script setup>
import { onMounted, ref } from "vue";

const quotes = ref([
    {
        active: true,
        message:
            '"Your unique strengths and resilience open doors to endless opportunities — never stop reaching for them."',
    },
    {
        active: false,
        message:
            '"You are never too old to set another goal or to dream a new dream." – C.S. Lewis',
    },
    {
        active: false,
        message:
            '"The only disability in life is a bad attitude." – Scott Hamilton',
    },
]);

let totalQuotes = quotes.value.length;
let currentIndex = 1;
onMounted(() => {
    setInterval(() => {
        currentIndex > 0 ? (quotes.value[currentIndex - 1].active = false) : "";
        // currentIndex > totalQuotes - 1 ? (currentIndex = 0) : "";
        currentIndex = currentIndex % totalQuotes;
        if (currentIndex <= totalQuotes - 1) {
            quotes.value[currentIndex].active = true;
            currentIndex++;
        }
    }, 8000);
});
</script>
<template>
    <div v-for="(quote, index) in quotes" :key="index" class="relative">
        <Transition
            enter-active-class="animate__animated animate__fadeInUp"
            leave-active-class="animate__animated animate__fadeOutUp"
        >
            <p v-if="quote.active" class="absolute">{{ quote.message }}</p>
        </Transition>
    </div>
</template>

<style>
@import "https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css";
</style>
