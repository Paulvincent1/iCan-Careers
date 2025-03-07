<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import dayjs from "dayjs";
import { computed, onUpdated, ref, watch } from "vue";

let props = defineProps({
    job: {
        type: null,
        required: true,
    },
});

let emit = defineEmits(["saveJob"]);

// watch(
//     () => props.job,
//     () => {
//         console.log("hi");
//     },
// );

let isSaved = ref(props.job.users_who_saved.length);

function saveJob() {
    emit("saveJob");
    if (isSaved.value === 0) {
        isSaved.value = 1;
        console.log("hui");
    } else {
        console.log("hello");

        isSaved.value = 0;
    }
}

onUpdated(() => {
    console.log("his");
});

let datePosted = computed(() => {
    return dayjs(props.job.created_at).format("MMMM DD, YYYY");
});
</script>
<template>
    <div class="rounded-lg bg-white p-3 shadow-lg ring-1 ring-gray-50">
        <div class="mb-3 flex justify-between">
            <p class="text-gray-500">{{ datePosted }}</p>
            <Link
                @click="saveJob"
                as="button"
                method="post"
                preserve-scroll
                :href="route('jobsearch.save.job', job.id)"
            >
                <i
                    @click=""
                    :class="[
                        'bi bi-bookmark-dash-fill text-green-500 hover:cursor-pointer',
                        {
                            'bi-bookmark-dash-fill': isSaved,
                            'bi-bookmark-dash': !isSaved,
                        },
                    ]"
                ></i>
            </Link>
        </div>
        <div class="mb-4 flex gap-3">
            <div class="h-14 min-h-14 w-14 min-w-14">
                <img
                    :src="
                        job.employer.employer_profile.business_information
                            ? job.employer.employer_profile.business_information
                                  .business_logo
                            : '/assets/images.png'
                    "
                    alt=""
                    class="h-full w-full rounded object-cover"
                />
            </div>
            <div>
                <p
                    class="h-[28px] w-full overflow-hidden text-ellipsis text-lg font-bold"
                >
                    {{ job.job_title }}
                </p>
                <p>
                    {{
                        job.employer.employer_profile.business_information
                            ? job.employer.employer_profile.business_information
                                  .business_name
                            : job.employer.name
                    }}
                </p>
            </div>
        </div>
        <div class="mb-4 flex justify-around text-gray-600">
            <p><i class="bi bi-briefcase"></i> {{ job.job_type }}</p>
            <p><i class="bi bi-layers"></i> {{ job.work_arrangement }}</p>
            <p><i class="bi bi-cash"></i> {{ job.hourly_rate }}</p>
            <p><i class="bi bi-person"></i> {{ job.experience }}</p>
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
            <p
                v-for="skill in job.skills"
                class="w-fit rounded bg-gray-300 p-1 text-center font-bold"
            >
                {{ skill }}
            </p>
        </div>
        <div class="mt-auto flex justify-center gap-3">
            <Link
                :href="route('jobsearch.show', job.id)"
                as="button"
                class="flex-1 rounded-lg border border-green-500 p-3 text-green-500"
            >
                Details
            </Link>
            <Link
                @click="saveJob"
                as="button"
                method="post"
                preserve-scroll
                :href="route('jobsearch.save.job', job.id)"
                class="flex-1 rounded-lg border bg-green-500 p-3 text-white"
            >
                {{ isSaved === 1 ? "Saved" : "Save for later" }}
            </Link>
        </div>
    </div>
</template>
