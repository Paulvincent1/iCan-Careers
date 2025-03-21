<script setup>
import { ref, computed, onMounted } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import DataTable from "vue3-easy-data-table";
import EarningsCard from "../Components/Admin/EarningsCard.vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faSearch } from "@fortawesome/free-solid-svg-icons";

const darkMode = ref(localStorage.getItem("theme") === "dark");

onMounted(() => {
    document.documentElement.classList.toggle("dark", darkMode.value);
    window.addEventListener("theme-changed", () => {
        darkMode.value = localStorage.getItem("theme") === "dark";
        document.documentElement.classList.toggle("dark", darkMode.value);
    });
});

const textSrc = computed(() =>
    darkMode.value ? "text-white" : "text-gray-900",
);

library.add(faSearch);

import "vue3-easy-data-table/dist/style.css";

defineOptions({ layout: AdminLayout });

const jobs = ref(usePage().props.jobs);

const tabs = [
    { id: "all", label: "All" },
    { id: "approved", label: "Approved" },
    { id: "pending", label: "Pending" },
];

const activeTab = ref("all");
const searchQuery = ref("");

// **Filtered Jobs Based on Search and Tabs**
const filteredJobs = computed(() => {
    let filtered = jobs.value;

    if (activeTab.value !== "all") {
        filtered = filtered.filter((job) =>
            activeTab.value === "approved"
                ? job.job_status === "Open"
                : job.job_status === "Pending",
        );
    }

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (job) =>
                job.job_title.toLowerCase().includes(query) ||
                job.employer?.email?.toLowerCase().includes(query) ||
                job.job_status.toLowerCase().includes(query),
        );
    }

    return filtered;
});

// **Data for EarningsCards**
const totalJobsData = computed(() => {
    return [{ name: "Total Jobs", value: jobs.value.length }];
});

const jobStatusData = computed(() => {
    const openJobs = jobs.value.filter(
        (job) => job.job_status === "Open",
    ).length;
    const pendingJobs = jobs.value.filter(
        (job) => job.job_status === "Pending",
    ).length;

    return [
        { name: "Approved", value: openJobs },
        { name: "Pending", value: pendingJobs },
    ];
});

const employerData = computed(() => {
    const uniqueEmployers = new Set(jobs.value.map((job) => job.employer.email))
        .size;
    return [{ name: "Employers", value: uniqueEmployers }];
});

const headers = [
    { text: "ID", value: "id", sortable: true },
    { text: "Profile", value: "profile", sortable: false },
    { text: "Job Title", value: "job_title", sortable: false },
    { text: "Email", value: "employer.email", sortable: false },
    { text: "Status", value: "job_status", sortable: false },
    { text: "Actions", value: "actions", sortable: false },
];

const toggleApproval = (id, newStatus) => {
    router.put(
        `/admin/job-approvals/${id}/update`,
        { status: newStatus },
        {
            preserveScroll: true,
            onSuccess: () => {
                const job = jobs.value.find((job) => job.id === id);
                if (job) job.job_status = newStatus;
            },
            onError: (errors) =>
                console.error("Error updating job status:", errors),
        },
    );
};
</script>

<template>
    <Head title="Job Approvals | iCan Careers" />
    <div :class="['p-4', darkMode ? 'bg-gray-700' : 'bg-white']">
        <!-- Search Bar -->
        <div class="mb-4 flex items-center gap-2 rounded-md bg-gray-100 p-3">
            <font-awesome-icon
                :icon="['fas', 'search']"
                class="text-gray-500"
            />
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Search job title, employer, or status..."
                class="w-full bg-transparent outline-none"
            />
        </div>

        <h1 class="mb-4 flex items-center gap-2 text-xl font-bold">
            <font-awesome-icon
                :icon="['fas', 'clipboard-check']"
                class="text-[#fa8334]"
            />
            <p :class="textSrc">Job Approvals</p>
        </h1>

        <hr class="my-4 border-t-2 border-gray-200 dark:border-gray-600" />

        <!-- Earnings Cards -->
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <EarningsCard
                title="Total Jobs"
                :data="totalJobsData"
                :showChart="true"
            />
            <EarningsCard
                title="Job Status"
                :data="jobStatusData"
                :showChart="true"
            />
            <EarningsCard
                title="Employer"
                :data="employerData"
                :showChart="true"
            />
        </div>
        <hr class="my-4 border-t-2 border-gray-200 dark:border-gray-600" />

        <!-- Tab Navigation -->
        <nav class="mb-6">
            <ul class="flex space-x-4 overflow-x-auto border-b">
                <li
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="{
                        'border-b-2 border-yellow-300 font-semibold':
                            activeTab === tab.id,
                        'text-yellow-300': activeTab === tab.id && !darkMode,
                        'text-yellow-300': activeTab === tab.id && darkMode,
                        'text-gray-500': activeTab !== tab.id && !darkMode,
                        'text-white': activeTab !== tab.id && darkMode,
                    }"
                    class="cursor-pointer whitespace-nowrap px-4 py-2"
                >
                    {{ tab.label }}
                </li>
            </ul>
        </nav>

        <!-- DataTable for larger screens -->
        <div class="hidden sm:block">
            <DataTable
                :headers="headers"
                :items="filteredJobs"
                :rows-per-page="10"
                :sort-by="'id'"
                :sort-type="'asc'"
            >
                <template #item-profile="{ id }">
                    <a
                        :href="`/admin/job-posts/${id}`"
                        class="text-blue-500 hover:underline"
                        >View Post</a
                    >
                </template>

                <template #item-job_status="{ job_status }">
                    <span
                        :class="
                            job_status === 'Open'
                                ? 'text-green-600'
                                : 'text-red-600'
                        "
                    >
                        {{ job_status }}
                    </span>
                </template>

                <template #item-actions="{ id, job_status }">
                    <div class="flex items-center space-x-2">
                        <button
                            v-if="job_status !== 'Open'"
                            @click="toggleApproval(id, 'Open')"
                            class="rounded bg-blue-500 px-3 py-1 text-white hover:bg-blue-600"
                        >
                            Approve
                        </button>
                        <button
                            v-else
                            @click="toggleApproval(id, 'Pending')"
                            class="rounded bg-red-500 px-3 py-1 text-white hover:bg-red-600"
                        >
                            Revoke
                        </button>
                    </div>
                </template>
            </DataTable>
        </div>
    </div>
</template>
