<script setup>
defineProps({
    title: String,
});
</script>
<template>
    <form
        @submit.prevent
        class="mt-12 flex w-[400px] max-w-[400px] flex-col rounded-lg p-9 sm:border sm:shadow-lg"
    >
        <h1 class="mb-2 text-3xl font-semibold">{{ title }}</h1>
        <slot />
    </form>
</template>
