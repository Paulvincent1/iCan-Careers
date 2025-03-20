<script setup>
import { ref, computed, onMounted } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import DataTable from "vue3-easy-data-table";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faSearch, faUser, faBan, faUsers } from "@fortawesome/free-solid-svg-icons";
import StatsCard from "../Components/Admin/StatsCard.vue";

// Dark mode logic
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

// Add icons
library.add(faSearch, faUser, faBan);

// Define Layout
defineOptions({ layout: AdminLayout });

// Get reported posts data from backend
const reportedPosts = ref(usePage().props.reports || []);
// Function to count reports per day (last 7 days)
const generateReportData = () => {
    const last7Days = Array.from({ length: 7 }, (_, i) => {
        const date = new Date();
        date.setDate(date.getDate() - i);
        return date.toISOString().split("T")[0]; // Format: YYYY-MM-DD
    }).reverse();

    const reportCounts = last7Days.map((day) => {
        return reportedPosts.value.filter((report) => {
            return report.created_at.startsWith(day);
        }).length;
    });

    return { labels: last7Days, data: reportCounts };
};
const reportGraph = computed(() => generateReportData());

// Tabs for filtering posts
const tabs = [{ id: "all", label: "All" }];

// Active tab state
const activeTab = ref("all");

// Search input state
const searchQuery = ref("");

// Filtered Posts (Based on Tab and Search)
const filteredPosts = computed(() => {
    let filtered = reportedPosts.value;

    // Filter by Tab
    if (activeTab.value !== "all") {
        filtered = filtered.filter(
            (post) => post.status.toLowerCase() === activeTab.value,
        );
    }

    // Search Filtering
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (post) =>
                post.reporter?.name.toLowerCase().includes(query) ||
                post.reported_job_post?.title.toLowerCase().includes(query) ||
                post.reason.toLowerCase().includes(query),
        );
    }

    return filtered;
});

// Table Headers
const headers = [
    { text: "ID", value: "id", sortable: true },
    { text: "Reported By", value: "reporter.name", sortable: false },
    {
        text: "Reported Post",
        value: "reported_job_post.title",
        sortable: false,
    },
    { text: "Reason", value: "reason", sortable: false },
];
</script>

<template>
    <Head title="Reported Posts | iCan Careers" />
    <div :class="['h-full p-4', darkMode ? 'bg-gray-700' : 'bg-white']">
        
    
        <!-- Search Bar -->
        <div class="w-[500px] mb-4 flex items-center gap-2 rounded-md bg-gray-100 p-3">
            <font-awesome-icon
                :icon="['fas', 'search']"
                class="text-gray-500"
            />
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Search by reporter, post title, or reason..."
                class="w-full bg-transparent outline-none"
            />
        </div>
        
        <h1 class="mb-4 flex items-center gap-2 text-xl font-bold">
            <font-awesome-icon :icon="['fas', 'file-circle-exclamation']" class="text-[#fa8334]" />
            <p :class="textSrc">Reported Posts Management</p>
        </h1>
        <hr class="my-4 border-t-2 border-gray-200 dark:border-gray-600">
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <StatsCard
                title="Total Reports"
                :value="reportedPosts.length"
                :icon="{ prefix: 'fas', iconName: 'exclamation-triangle' }"
                :chartData="reportGraph.data"
                :labels="reportGraph.labels"
            />
            <StatsCard
                title="No. of Reporter Users"
                :value="reportedPosts.length"
                :icon="faUsers"
                :chartData="reportGraph.data"
                :labels="reportGraph.labels"
            />
            <StatsCard
                title="No. of Reported POSTS"
                :value="filteredPosts.length"
                :icon="{ prefix: 'fas', iconName: 'file-circle-exclamation' }"
                :chartData="reportGraph.data"
                :labels="reportGraph.labels"
            />
        </div>
        <hr class="my-4 border-t-2 border-gray-200 dark:border-gray-600">

          <!-- Tabs Navigation -->
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
        <!-- DataTable for Larger Screens -->
        <div class="relative z-0 hidden sm:block">
            <DataTable
                :headers="headers"
                :items="filteredPosts"
                :rows-per-page="10"
                :sort-by="'id'"
                :sort-type="'asc'"
            >
                <!-- Custom slot for Reported Post -->
                <template #item-reported_job_post.title="{ reported_job_post }">
                    <a
                        :href="`/admin/reported-job-posts/${reported_job_post?.id}`"
                        class="text-blue-500 hover:underline"
                    >
                        View Post
                    </a>
                </template>
            </DataTable>
        </div>

        <!-- Card Layout for Mobile -->
        <div class="space-y-4 sm:hidden">
            <div
                v-for="post in filteredPosts"
                :key="post.id"
                class="rounded-lg bg-white p-4 shadow-md"
            >
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">
                        {{ post.reported_job_post?.title || "N/A" }}
                    </h2>
                    <span
                        :class="
                            post.reported_job_post?.employer?.ban
                                ? 'bg-red-500'
                                : 'bg-green-500'
                        "
                        class="rounded-full px-3 py-1 text-sm text-white"
                    >
                        {{
                            post.reported_job_post?.employer?.ban
                                ? "Banned"
                                : "Active"
                        }}
                    </span>
                </div>
                <p class="text-sm text-gray-600">
                    <strong>Reported By:</strong>
                    {{ post.reporter?.name || "N/A" }}
                </p>
                <p class="text-sm text-gray-600">
                    <strong>Reason:</strong> {{ post.reason }}
                </p>
            </div>
        </div>
    </div>
</template>
