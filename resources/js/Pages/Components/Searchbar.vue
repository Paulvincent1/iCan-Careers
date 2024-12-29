<script setup>
import { ref } from "vue";

let search = ref("");

defineProps({
    endpoint: null,
});
</script>
<template>
    <div class="relative">
        <input
            type="text"
            class="w-full rounded-3xl border p-4 px-10"
            placeholder="Search Talent"
            v-model="search"
        />
        <i
            class="bi bi-search absolute left-[5%] top-[50%] translate-y-[-50%]"
        ></i>
        <Link
            :href="endpoint"
            as="button"
            method="get"
            :data="{ search }"
            class="absolute right-[5%] top-[50%] translate-y-[-50%] rounded-3xl bg-blue-500 px-4 py-2 text-white"
            >Search</Link
        >
    </div>
</template>
