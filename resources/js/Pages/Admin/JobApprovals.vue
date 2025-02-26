<script setup>
import { ref, computed } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import DataTable from "vue3-easy-data-table";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faSearch } from "@fortawesome/free-solid-svg-icons";

// Add the search icon
library.add(faSearch);

// Import styles for the DataTable component
import "vue3-easy-data-table/dist/style.css";

defineOptions({ layout: AdminLayout });

// Fetch jobs from Laravel
const jobs = ref(usePage().props.jobs);

// Tabs for filtering jobs
const tabs = [
    { id: "all", label: "All" },
    { id: "approved", label: "Approved" },
    { id: "pending", label: "Pending" },
];

// Active tab state
const activeTab = ref("all");

// Search input state
const searchQuery = ref("");

// Filter jobs based on status and search query
const filteredJobs = computed(() => {
    let filtered = jobs.value;

    // Filter by tab
    if (activeTab.value !== "all") {
        filtered = filtered.filter(job =>
            activeTab.value === "approved" ? job.job_status === "Open" : job.job_status === "Pending"
        );
    }

    // Search filtering
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(job =>
            job.job_title.toLowerCase().includes(query) ||
            job.employer?.email?.toLowerCase().includes(query) ||
            job.job_status.toLowerCase().includes(query)
        );
    }

    return filtered;
});

// Table Headers
const headers = [
    { text: "ID", value: "id", sortable: true },
    { text: "Profile", value: "profile", sortable: false },
    { text: "Job Title", value: "job_title", sortable: false },
    { text: "Email", value: "employer.email", sortable: false },
    { text: "Status", value: "job_status", sortable: false},
    { text: "Actions", value: "actions", sortable: false },
];

// Toggle job approval
const toggleApproval = (id, newStatus) => {
    router.put(`/admin/job-approvals/${id}/update`, { status: newStatus }, {
        preserveScroll: true,
        onSuccess: () => {
            const job = jobs.value.find(job => job.id === id);
            if (job) job.job_status = newStatus;
        },
        onError: (errors) => console.error("Error updating job status:", errors),
    });
};
</script>

<template>
    <Head title="Job Approvals | iCan Careers" />
    <div class="p-4">
        <!-- Tab Navigation -->
        <nav class="mb-6">
            <ul class="flex space-x-4 border-b overflow-x-auto">
                <li v-for="tab in tabs" :key="tab.id" 
                    @click="activeTab = tab.id"
                    :class="{
                        'border-b-2 border-blue-500 font-semibold': activeTab === tab.id,
                        'text-gray-500 hover:text-gray-700': activeTab !== tab.id
                    }"
                    class="cursor-pointer px-4 py-2 whitespace-nowrap">
                    {{ tab.label }}
                </li>
            </ul>
        </nav>

        <h1 class="mb-4 text-xl font-bold flex items-center gap-2">
            <font-awesome-icon :icon="['fas', 'clipboard-check']" class="text-blue-500" />
            Job Approval
        </h1>

        <!-- Search Bar -->
        <div class="mb-4 flex items-center gap-2 bg-gray-100 p-3 rounded-md">
            <font-awesome-icon :icon="['fas', 'search']" class="text-gray-500" />
            <input 
                v-model="searchQuery" 
                type="text" 
                placeholder="Search job title, employer, or status..."
                class="w-full bg-transparent outline-none"
            />
        </div>

        <!-- DataTable for larger screens -->
        <div class="hidden sm:block">
            <DataTable
                :headers="headers"
                :items="filteredJobs"
                :rows-per-page="10"
                :sort-by="'id'"
                :sort-type="'asc'"
            >
                <!-- Custom slot for Profile (View Post) -->
                <template #item-profile="{ id }">
                    <a :href="`/admin/job-posts/${id}`" 
                       class="text-blue-500 hover:underline">
                        View Post
                    </a>
                </template>

                <!-- Custom slot for status -->
                <template #item-job_status="{ job_status }">
                    <span :class="job_status === 'Open' ? 'text-green-600' : 'text-red-600'">
                        {{ job_status }}
                    </span>
                </template>

                <!-- Custom slot for actions -->
                <template #item-actions="{ id, job_status }">
                    <div class="flex items-center space-x-2">
                        <button v-if="job_status !== 'Open'"
                            @click="toggleApproval(id, 'Open')"
                            class="rounded bg-blue-500 px-3 py-1 text-white hover:bg-blue-600">
                            Approve
                        </button>
                        <button v-else
                            @click="toggleApproval(id, 'Pending')"
                            class="rounded bg-red-500 px-3 py-1 text-white hover:bg-red-600">
                            Revoke
                        </button>
                    </div>
                </template>
            </DataTable>
        </div>

        <!-- Card layout for mobile screens -->
        <div class="sm:hidden space-y-4">
            <div v-for="job in filteredJobs" :key="job.id" 
                class="bg-white p-4 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold">{{ job.job_title }}</h2>
                <p class="text-gray-600">Email: {{ job.employer?.email}}</p>
                <p class="text-gray-600">
                    Status: 
                    <span :class="job.job_status === 'Open' ? 'text-green-600' : 'text-red-600'">
                        {{ job.job_status }}
                    </span>
                </p>
                <div class="mt-3 flex justify-between items-center">
                    <a :href="`/admin/job-posts/${job.id}`" class="text-blue-500 hover:underline">View Post</a>
                    <button v-if="job.job_status !== 'Open'"
                        @click="toggleApproval(job.id, 'Open')"
                        class="rounded bg-blue-500 px-3 py-1 text-white hover:bg-blue-600">
                        Approve
                    </button>
                    <button v-else
                        @click="toggleApproval(job.id, 'Pending')"
                        class="rounded bg-red-500 px-3 py-1 text-white hover:bg-red-600">
                        Revoke
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>