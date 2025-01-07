<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

let page = usePage();
let isActive = ref(null);

defineProps({
    message: null,
    verificationType: null,
});
</script>
<template>
    <div v-if="message" class="font-semibold text-[#42423c]">
        <div
            class="xs container mx-auto flex items-center justify-between gap-2 rounded-xl border border-[#edf75a] bg-[#edf75a] p-4 px-8 xl:max-w-7xl"
        >
            <p>
                {{ message }}

                <Link class="underline">Click here!</Link>
            </p>
            <i class="bi bi-x-lg"></i>
        </div>
    </div>
</template>
