<template>
    <section class="bg-white py-12">
        <div class="container mx-auto text-center">
            <h2 class="text-2xl font-semibold text-gray-800">Our Impact</h2>
            <div class="mt-6 flex flex-wrap justify-center gap-10">
                <div v-for="(stat, index) in stats" :key="index" class="p-4">
                    <p class="text-3xl font-bold text-[#fa8334]">{{ stat.value }}</p>
                    <p class="text-gray-600">{{ stat.label }}</p>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
defineProps({
    stats: Array
});
</script>
