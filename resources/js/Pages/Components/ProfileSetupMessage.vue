<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

let page = usePage();
let isActive = ref(null);

defineProps({
    info: null,
});
let emit = defineEmits(["close"]);
</script>
<template>
    <div class="z-50 font-semibold text-[#42423c]">
        <div
            class="xs container mx-auto flex items-center justify-between gap-2 rounded-xl border border-[#edf75a] bg-[#edf75a] p-4 px-8 xl:max-w-7xl"
        >
            <p>
                {{ info.message }}

                <Link class="underline" :href="route(info.route)"
                    >Click here!</Link
                >
            </p>
            <button @click="emit('close', true)">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>
</template>
