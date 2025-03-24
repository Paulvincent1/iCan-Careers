<script setup>
import { ref, computed, onMounted } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import DataTable from "vue3-easy-data-table";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import StatsCard from "../Components/Admin/StatsCard.vue";
import {
    faSearch,
    faUser,
    faBan,
    faExclamationTriangle,
    faUsers,
    faUserSlash,
} from "@fortawesome/free-solid-svg-icons";

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

const reportedUsers = ref(usePage().props.reports || []);

// Function to count reports per day (last 7 days)
const generateReportData = () => {
    const last7Days = Array.from({ length: 7 }, (_, i) => {
        const date = new Date();
        date.setDate(date.getDate() - i);
        return date.toISOString().split("T")[0]; // Format: YYYY-MM-DD
    }).reverse();

    const reportCounts = last7Days.map((day) => {
        return reportedUsers.value.filter((report) => {
            return report.created_at.startsWith(day);
        }).length;
    });

    return { labels: last7Days, data: reportCounts };
};

// Computed properties to update chart dynamically
const reportGraph = computed(() => generateReportData());
// Tabs for filtering users
const tabs = [
    { id: "all", label: "All" },
    { id: "active", label: "Active" },
    { id: "banned", label: "Banned" },
];

// Active tab state
const activeTab = ref("all");

// Search input state
const searchQuery = ref("");

// Filtered Users (Based on Tab and Search)
const filteredUsers = computed(() => {
    let filtered = reportedUsers.value;

    // Filter by Tab
    if (activeTab.value === "active") {
        filtered = filtered.filter((user) => !user.reported.ban);
    } else if (activeTab.value === "banned") {
        filtered = filtered.filter((user) => user.reported.ban);
    }

    // Search Filtering
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (user) =>
                user.reporter?.name.toLowerCase().includes(query) ||
                user.reported?.name.toLowerCase().includes(query) ||
                user.reason.toLowerCase().includes(query),
        );
    }

    return filtered;
});


// Table Headers
const headers = [
    { text: "ID", value: "id", sortable: true },
    { text: "Reported By", value: "reporter.name", sortable: false },
    { text: "Reported User", value: "reported.name", sortable: false },
    { text: "View Profile", value: "view-profile", sortable: false },
    { text: "Reason", value: "reason", sortable: false },
    { text: "Status", value: "reported.ban", sortable: false },
    { text: "Action", value: "action", sortable: false },
];

// Toggle Ban Function
const toggleBan = (reportedUserId) => {
    router.put(
        `/admin/reported-users/toggle-ban/${reportedUserId}`,
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                router.reload({ only: ["reports"] });
            },
        },
    );
};
</script>

<template>
    <Head title="Reported Users | iCan Careers" />
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
                placeholder="Search by reporter, reported user, or reason..."
                class="w-full bg-transparent outline-none"
            />
        </div>
        <h1 class="mb-4 flex items-center gap-2 text-xl font-bold">
            <font-awesome-icon :icon="['fas', 'user-slash']" class="text-[#fa8334]" />
            <p :class="textSrc">Reported Users Management</p>
        </h1>
        <hr class="my-4 border-t-2 border-gray-200 dark:border-gray-600">

        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <StatsCard
                title="Total Reports"
                :value="reportedUsers.length"
                :icon="{ prefix: 'fas', iconName: 'exclamation-triangle' }"
                :chartData="reportGraph.data"
                :labels="reportGraph.labels"
            />
            <StatsCard
                title="No. of Reporters"
                :value="reportedUsers.length"
                :icon="faUsers"
                :chartData="reportGraph.data"
                :labels="reportGraph.labels"
            />
            <StatsCard
                title="No. of Reported Users"
                :value="filteredUsers.length"
                :icon="faUserSlash"
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
                :items="filteredUsers"
                :rows-per-page="10"
                :sort-by="'id'"
                :sort-type="'asc'"
            >
                <!-- Custom slot for Status -->
                <template #item-reported.ban="{ reported }">
                    <span
                        :class="
                            reported.ban
                                ? 'bg-red-500 text-white'
                                : 'bg-green-500 text-white'
                        "
                        class="rounded-full px-3 py-1 text-sm"
                    >
                        {{ reported.ban ? "Banned" : "Active" }}
                    </span>
                </template>

                <template #item-view-profile="{ reported }">
                    <Link
                        :href="
                            route('admin.view-profile-user', {
                                id: reported.id,
                            })
                        "
                        class="text-blue-500"
                        >View Profile</Link
                    >
                </template>

                <!-- Custom slot for Action -->
                <template #item-action="{ reported }">
                    <label class="flex cursor-pointer items-center">
                        <input
                            type="checkbox"
                            v-model="reported.ban"
                            @change="toggleBan(reported.id)"
                            class="sr-only"
                        />
                        <div
                            class="relative h-5 w-10 rounded-full bg-gray-300 transition duration-300"
                            :class="{ 'bg-red-500': reported.ban }"
                        >
                            <div
                                class="absolute left-1 top-1 h-3 w-3 rounded-full bg-white transition-transform duration-300"
                                :class="{ 'translate-x-5': reported.ban }"
                            ></div>
                        </div>
                    </label>
                </template>
            </DataTable>
        </div>

        <!-- Card Layout for Mobile -->
        <div class="space-y-4 sm:hidden">
            <div
                v-for="user in filteredUsers"
                :key="user.id"
                class="rounded-lg bg-white p-4 shadow-md"
            >
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">
                        {{ user.reported?.name || "Unknown" }}
                    </h2>
                    <span
                        :class="
                            user.reported.ban
                                ? 'bg-red-500 text-white'
                                : 'bg-green-500 text-white'
                        "
                        class="rounded-full px-3 py-1 text-sm"
                    >
                        {{ user.reported.ban ? "Banned" : "Active" }}
                    </span>
                </div>
                <p class="text-sm text-gray-600">
                    <strong>Reported By:</strong>
                    {{ user.reporter?.name || "Unknown" }}
                </p>
                <p class="text-sm text-gray-600">
                    <strong>Reason:</strong> {{ user.reason }}
                </p>

                <div class="mt-3 flex justify-end">
                    <button
                        @click="toggleBan(user.reported.id)"
                        :class="
                            user.reported.ban
                                ? 'bg-green-500 hover:bg-green-600'
                                : 'bg-red-500 hover:bg-red-600'
                        "
                        class="rounded px-4 py-2 text-white"
                    >
                        {{ user.reported.ban ? "Unban" : "Ban" }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
