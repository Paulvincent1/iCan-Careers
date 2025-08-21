<script setup>
import { usePage, router } from "@inertiajs/vue3";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import { ref, computed } from "vue";
import Maps from "../Components/Maps.vue";
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

// Fetch job details
const jobPost = usePage().props.job;

// Computed property to check if the job is approved
const isApproved = computed(() => jobPost.job_status === "Open");

// Function to toggle job approval
const toggleApproval = () => {
    const newStatus = isApproved.value ? "Pending" : "Open";
    router.put(
        `/admin/job-approvals/${jobPost.id}/update`,
        { status: newStatus },
        {
            preserveScroll: true,
            onSuccess: () => {
                jobPost.job_status = newStatus; // Update UI state
            },
            onError: (errors) =>
                console.error("Error updating job status:", errors),
        },
    );
};
</script>

<template>
    <Head :title="`Job Details - ${jobPost.job_title}`" />

    <div class="mx-auto max-w-4xl rounded-lg bg-white p-6 shadow-md">
        <!-- Job Title & Status -->
        <div class="mb-6 flex items-center justify-between">
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

        <!-- Employer -->
        <p class="mb-6 text-gray-600">
            Posted by:
            <span class="font-semibold">{{ jobPost.employer?.name }}</span>
        </p>

        <!-- Job Details Section -->
        <div class="space-y-6">
            <h2 class="border-b pb-2 text-lg font-semibold text-gray-800">
                Job Information
            </h2>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="flex items-center">
                    <BriefcaseIcon class="mr-2 h-6 w-6 text-blue-500" />
                    <p class="text-gray-700">
                        <span class="font-semibold">Type:</span>
                        {{ jobPost.job_type }}
                    </p>
                </div>

                <!-- Location -->
                <div class="sm:col-span-2">
                    <div class="mb-2 flex items-center">
                        <MapPinIcon class="mr-2 h-6 w-6 text-green-500" />
                        <p class="text-gray-700">
                            <span class="font-semibold">Location:</span>
                            {{ jobPost.location }}
                        </p>
                    </div>
                    <Maps
                        :center-props="jobPost.location"
                        :marked-coordinates-props="jobPost.location"
                        :disable-search="true"
                        :disable-set-maker="true"
                        class="w-full rounded-xl shadow-md"
                        style="height: 300px"
                    />
                </div>

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
            </div>

            <!-- Skills Required -->
            <div>
                <h2 class="border-b pb-2 text-lg font-semibold text-gray-800">
                    Skills Required
                </h2>
                <div class="mt-2 flex flex-wrap gap-2">
                    <span
                        v-for="skill in jobPost.skills"
                        :key="skill"
                        class="rounded-full bg-gray-200 px-3 py-1 text-sm text-gray-700"
                    >
                        {{ skill }}
                    </span>
                </div>
            </div>

            <!-- Preferred Worker Types -->
            <div>
                <h2 class="border-b pb-2 text-lg font-semibold text-gray-800">
                    Preferred Worker Types
                </h2>
                <div class="mt-2 flex flex-wrap gap-2">
                    <span
                        v-for="type in jobPost.preferred_worker_types"
                        :key="type"
                        class="rounded-full bg-blue-200 px-3 py-1 text-sm text-blue-800"
                    >
                        {{ type }}
                    </span>
                </div>
            </div>

            <!-- Job Description -->
            <div>
                <h2 class="border-b pb-2 text-lg font-semibold text-gray-800">
                    Job Description
                </h2>
                <p class="mt-2 text-gray-600">{{ jobPost.description }}</p>
            </div>
        </div>

        <!-- Toggle Approval Button -->
        <div class="mt-8 flex items-center justify-between">
            <a
                href="/admin/job-approvals"
                class="flex items-center font-semibold text-blue-600 hover:text-blue-800"
            >
                ← Back to Job Approvals
            </a>

            <button
                @click="toggleApproval"
                class="flex items-center gap-2 rounded px-4 py-2 text-white transition"
                :class="
                    isApproved
                        ? 'bg-red-500 hover:bg-red-600'
                        : 'bg-blue-500 hover:bg-blue-600'
                "
            >
                <CheckIcon v-if="!isApproved" class="h-5 w-5" />
                <XCircleIcon v-else class="h-5 w-5" />
                {{ isApproved ? "Revoke Approval" : "Approve Job" }}
            </button>
        </div>
    </div>
</template>
