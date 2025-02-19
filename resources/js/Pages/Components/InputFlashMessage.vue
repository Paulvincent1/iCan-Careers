<script setup>
defineProps({
    message: String,
    type: String,
});
</script>
<template>
    <p
        :class="[
            {
                'text-red-500': type === 'error',
                'font-semibold text-green-500': type === 'success',
            },
        ]"
        v-if="message"
    >
        {{ message }}
    </p>
</template>
