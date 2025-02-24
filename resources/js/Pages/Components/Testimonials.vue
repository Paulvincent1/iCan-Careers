<template>
    <section class="py-12">
        <div class="container mx-auto text-center">
            <h2 class="text-2xl font-semibold text-gray-800">Success Stories</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <template v-for="(row, rowIndex) in chunkedTestimonials" :key="rowIndex">
                    <div v-for="testimonial in row" :key="testimonial.name" class="p-6 bg-white shadow-md rounded-lg">
                        <p class="text-gray-700">"{{ testimonial.message }}"</p>
                        <p class="mt-2 text-sm text-gray-600">- {{ testimonial.name }}</p>
                    </div>
                </template>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed } from "vue";

// Props
const props = defineProps({
    testimonials: Array
});

// Function to split testimonials into chunks of 3 per row
const chunkedTestimonials = computed(() => {
    const chunkSize = 3;
    let result = [];
    for (let i = 0; i < props.testimonials.length; i += chunkSize) {
        result.push(props.testimonials.slice(i, i + chunkSize));
    }
    return result;
});
</script>