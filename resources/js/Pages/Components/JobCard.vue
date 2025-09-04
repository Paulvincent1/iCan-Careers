<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import dayjs from "dayjs";
import { computed, ref } from "vue";

let props = defineProps({
    job: {
        type: null,
        required: true,
    },
});

let emit = defineEmits(["saveJob"]);

let isSaved = ref(props.job.users_who_saved.length);

function saveJob() {
    emit("saveJob");
    isSaved.value = isSaved.value ? 0 : 1;
}

let datePosted = computed(() => {
    return dayjs(props.job.created_at).format("MMMM DD, YYYY");
});

// Check if job is specifically for Seniors or PWD
const isForSeniors = computed(() => {
    return props.job.preferred_worker_types.includes("Seniors Citizens");
});

const isForPWD = computed(() => {
    return props.job.preferred_worker_types.includes("PWD");
});

// Get company name
const companyName = computed(() => {
    return props.job.employer.employer_profile.business_information
        ? props.job.employer.employer_profile.business_information.business_name
        : props.job.employer.name;
});

// Truncate description
const truncatedDescription = computed(() => {
    const maxLength = 120;
    return props.job.description.length > maxLength
        ? props.job.description.substring(0, maxLength) + '...'
        : props.job.description;
});
</script>
<template>
    <div class="rounded-lg bg-white p-4 shadow-md hover:shadow-lg transition-shadow duration-300 border border-gray-100 flex flex-col h-full">
        <!-- Header with date and save button -->
        <div class="flex justify-between items-start mb-3">
            <p class="text-sm text-gray-500">{{ datePosted }}</p>
            <Link
                @click="saveJob"
                as="button"
                method="post"
                preserve-scroll
                :href="route('jobsearch.save.job', job.id)"
                class="text-orange-500 hover:text-orange-600 transition-colors"
                :title="isSaved ? 'Remove from saved jobs' : 'Save this job'"
            >
                <i
                    :class="[
                        'text-xl',
                        {
                            'bi-bookmark-fill': isSaved,
                            'bi-bookmark': !isSaved,
                        },
                    ]"
                ></i>
            </Link>
        </div>

        <!-- Company info and job title -->
        <div class="mb-4 flex gap-3 items-start">
            <div class="h-14 w-14 flex-shrink-0 rounded-md overflow-hidden border border-gray-200">
                <img
                    :src="
                        job.employer.employer_profile.business_information
                            ? job.employer.employer_profile.business_information.business_logo
                            : '/assets/logo-placeholder-image.png'
                    "
                    :alt="companyName + ' logo'"
                    class="h-full w-full object-cover"
                />
            </div>
            <div class="min-w-0 flex-1">
                <h3 class="font-bold text-lg truncate" :title="job.job_title">
                    {{ job.job_title }}
                </h3>
                <p class="text-gray-600 truncate" :title="companyName">
                    {{ companyName }}
                </p>
            </div>
        </div>

        <!-- Special badges for Senior/PWD -->
        <div class="mb-3 flex flex-wrap gap-2">
            <span 
                v-if="isForSeniors" 
                class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full flex items-center gap-1"
            >
                <i class="bi bi-person-check-fill"></i>
                Senior-Friendly
            </span>
            <span 
                v-if="isForPWD" 
                class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full flex items-center gap-1"
            >
                <i class="bi bi-wheelchair"></i>
                PWD-Friendly
            </span>
        </div>

        <!-- Job details icons -->
        <div class="grid grid-cols-2 gap-3 mb-4 text-sm">
            <div class="flex items-center gap-2 text-gray-600">
                <i class="bi bi-briefcase text-orange-500"></i>
                <span>{{ job.job_type }}</span>
            </div>
            <div class="flex items-center gap-2 text-gray-600">
                <i class="bi bi-layers text-orange-500"></i>
                <span>{{ job.work_arrangement }}</span>
            </div>
            <div class="flex items-center gap-2 text-gray-600">
                <i class="bi bi-cash-coin text-orange-500"></i>
                <span>{{ job.hourly_rate }}/hr</span>
            </div>
            <div class="flex items-center gap-2 text-gray-600">
                <i class="bi bi-person text-orange-500"></i>
                <span>{{ job.experience }}</span>
            </div>
        </div>

        <!-- Job description -->
        <div class="mb-4 flex-1">
            <p class="text-sm text-gray-700 line-clamp-3">
                {{ truncatedDescription }}
            </p>
        </div>

        <!-- Skills -->
        <div class="mb-4 flex flex-wrap gap-2">
            <span
                v-for="(skill, index) in job.skills.slice(0, 3)"
                :key="index"
                class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded"
            >
                {{ skill }}
            </span>
            <span 
                v-if="job.skills.length > 3" 
                class="px-2 py-1 bg-gray-100 text-gray-500 text-xs font-medium rounded"
            >
                +{{ job.skills.length - 3 }} more
            </span>
        </div>

       
    </div>
</template>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>