<script setup>
import dayjs from "dayjs";
import { computed } from "vue";

let props = defineProps({
    job: {
        type: null,
        required: true,
    },
});

let datePosted = computed(() => {
    return dayjs(props.job.created_at).format("MMMM DD, YYYY");
});
</script>
<template>
    <div class="rounded-lg border p-3">
        <div class="mb-3 flex justify-between">
            <p class="text-gray-500">{{ datePosted }}</p>
            <i class="bi bi-bookmark-dash text-green-500"></i>
        </div>
        <div class="mb-4 flex gap-3">
            <div class="h-14 w-14">
                <img
                    src="/storage/images/images.png"
                    alt=""
                    class="h-full w-full object-cover"
                />
            </div>
            <div>
                <p class="text-lg font-bold">{{ job.job_title }}</p>
                <p>
                    {{ job.employer.business_information ?? job.employer.name }}
                </p>
            </div>
        </div>
        <div class="mb-4 flex justify-around text-gray-600">
            <p>{{ job.job_type }}</p>
            <p>{{ job.work_arrangement }}</p>
            <p>{{ job.hour_per_day }}</p>
            <p>{{ job.experience }}</p>
        </div>
        <div class="mb-5">
            <p class="mb-2">Job Description</p>
            <p class="h-20 overflow-hidden text-sm text-gray-600">
                {{ job.description }}
            </p>
        </div>
        <div
            class="mb-3 flex max-h-[64px] flex-wrap gap-2 overflow-hidden text-sm"
        >
            <p class="w-fit rounded bg-gray-300 p-1 text-center font-bold">
                Ads
            </p>

            <p class="w-fit rounded bg-gray-300 p-1 text-center font-bold">
                Video Editing
            </p>

            <p class="w-fit rounded bg-gray-300 p-1 text-center font-bold">
                Graphic design
            </p>

            <p class="w-fit rounded bg-gray-300 p-1 text-center font-bold">
                Graphic design
            </p>
        </div>
        <div class="flex justify-center gap-3">
            <button
                class="flex-1 rounded-lg border border-green-500 p-3 text-green-500"
            >
                Details
            </button>
            <button
                class="flex-1 rounded-lg border bg-green-500 p-3 text-white"
            >
                Save for later
            </button>
        </div>
    </div>
</template>
