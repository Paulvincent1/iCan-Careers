<script setup>
import { usePage } from "@inertiajs/vue3";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import {
    BriefcaseIcon,
    MapPinIcon,
    ClockIcon,
    CurrencyDollarIcon,
    AcademicCapIcon,
    CheckIcon,
    XCircleIcon,
} from "@heroicons/vue/24/outline";

defineOptions({
    layout: AdminLayout,
});

// Fetch job details from Inertia props
const jobPost = usePage().props.job;
</script>

<template>
    <Head :title="`Job Details - ${jobPost.job_title}`" />
    <div class="mx-auto max-w-4xl rounded-lg bg-white p-6 shadow-md">
        <div class="mb-4 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-800">
                {{ jobPost.job_title }}
            </h1>
            <span
                class="rounded-full px-3 py-1 text-sm font-semibold"
                :class="{
                    'bg-green-100 text-green-600':
                        jobPost.job_status === 'Open',
                    'bg-yellow-100 text-yellow-600':
                        jobPost.job_status === 'Pending',
                    'bg-red-100 text-red-600': jobPost.job_status === 'Closed',
                }"
            >
                {{ jobPost.job_status }}
            </span>
        </div>

        <p class="text-gray-600">
            Posted by:
            <span class="font-semibold">{{ jobPost.employer?.name }}</span>
        </p>

        <!-- Job Details Section -->
        <div class="mt-6 space-y-4">
            <div class="flex items-center">
                <BriefcaseIcon class="mr-2 h-6 w-6 text-blue-500" />
                <p class="text-gray-700">
                    <span class="font-semibold">Type:</span>
                    {{ jobPost.job_type }}
                </p>
            </div>

            <p class="text-gray-600">
                Location:
                <span v-if="Array.isArray(jobPost.location)">
                    {{ jobPost.location.join(", ") }}
                </span>
                <span
                    v-else-if="
                        typeof jobPost.location === 'object' &&
                        jobPost.location !== null
                    "
                >
                    {{ jobPost.location.city || "" }},
                    {{ jobPost.location.state || "" }}
                </span>
                <span v-else>
                    {{ jobPost.location || "Not specified" }}
                </span>
            </p>

            <div class="flex items-center">
                <ClockIcon class="mr-2 h-6 w-6 text-purple-500" />
                <p class="text-gray-700">
                    <span class="font-semibold">Work Hours:</span>
                    {{ jobPost.hour_per_day }} hours/day
                </p>
            </div>

            <div class="flex items-center">
                <CurrencyDollarIcon class="mr-2 h-6 w-6 text-yellow-500" />
                <p class="text-gray-700">
                    <span class="font-semibold">Salary:</span> ${{
                        jobPost.salary
                    }}
                    / month
                </p>
            </div>

            <div class="flex items-center">
                <AcademicCapIcon class="mr-2 h-6 w-6 text-indigo-500" />
                <p class="text-gray-700">
                    <span class="font-semibold">Education:</span>
                    {{ jobPost.preferred_educational_attainment }}
                </p>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Skills Required
                </h2>
                <div class="mt-1 flex flex-wrap gap-2">
                    <span
                        v-for="skill in jobPost.skills"
                        :key="skill"
                        class="rounded-full bg-gray-200 px-3 py-1 text-sm text-gray-700"
                    >
                        {{ skill }}
                    </span>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Preferred Worker Types
                </h2>
                <div class="mt-1 flex flex-wrap gap-2">
                    <span
                        v-for="type in jobPost.preferred_worker_types"
                        :key="type"
                        class="rounded-full bg-blue-200 px-3 py-1 text-sm text-blue-800"
                    >
                        {{ type }}
                    </span>
                </div>
            </div>

            <div class="border-t pt-4">
                <h2 class="text-lg font-semibold text-gray-800">
                    Job Description
                </h2>
                <p class="mt-2 text-gray-600">{{ jobPost.description }}</p>
            </div>
        </div>

        <div class="mt-6">
            <a
                href="/admin/job-approvals"
                class="flex items-center font-semibold text-blue-600 hover:text-blue-800"
            >
                ← Back to Job Approvals
            </a>
        </div>
    </div>
</template>
