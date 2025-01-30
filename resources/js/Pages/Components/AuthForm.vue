<script setup>
defineProps({
    title: String,
});
</script>
<template>
    <form
        @submit.prevent
        class="mt-3 flex w-[360px] max-w-[400px] flex-col rounded-lg p-4 sm:mt-12 sm:w-[400px] sm:border sm:p-9 sm:shadow-lg"
    >
        <h1 class="mb-2 text-3xl font-semibold">{{ title }}</h1>
        <slot />
    </form>
</template>
