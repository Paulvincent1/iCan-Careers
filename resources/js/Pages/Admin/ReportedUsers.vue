<script setup>
import { ref, computed, onMounted } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import DataTable from "vue3-easy-data-table";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faSearch, faUser, faBan } from "@fortawesome/free-solid-svg-icons";

// Dark mode logic
const darkMode = ref(localStorage.getItem("theme") === "dark");

onMounted(() => {
    document.documentElement.classList.toggle("dark", darkMode.value);
    window.addEventListener("theme-changed", () => {
        darkMode.value = localStorage.getItem("theme") === "dark";
        document.documentElement.classList.toggle("dark", darkMode.value);
    });
});

const textSrc = computed(() => (darkMode.value ? 'text-white' : 'text-gray-900'));

// Add icons
library.add(faSearch, faUser, faBan);

// Define Layout
defineOptions({ layout: AdminLayout });

// Get reported users data from backend
const reportedUsers = ref(usePage().props.reports || []);

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
        filtered = filtered.filter(user => !user.reported.ban);
    } else if (activeTab.value === "banned") {
        filtered = filtered.filter(user => user.reported.ban);
    }

    // Search Filtering
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(user =>
            user.reporter?.name.toLowerCase().includes(query) ||
            user.reported?.name.toLowerCase().includes(query) ||
            user.reason.toLowerCase().includes(query)
        );
    }

    return filtered;
});

// Table Headers
const headers = [
    { text: "ID", value: "id", sortable: true },
    { text: "Reported By", value: "reporter.name", sortable: false },
    { text: "Reported User", value: "reported.name", sortable: false },
    { text: "Reason", value: "reason", sortable: false },
    { text: "Status", value: "reported.ban", sortable: false },
    { text: "Action", value: "action", sortable: false },
];

// Toggle Ban Function
const toggleBan = (reportedUserId) => {
    router.put(`/admin/reported-users/toggle-ban/${reportedUserId}`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            router.reload({ only: ['reports'] });
        },
    });
};
</script>

<template>
    <Head title="Reported Users | iCan Careers" />
    <div :class="['p-4', darkMode ? 'bg-gray-700' : 'bg-white']">
        <!-- Tabs Navigation -->
        <nav class="mb-6">
            <ul class="flex space-x-4 border-b overflow-x-auto">
                <li
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="{
                        'border-b-2 border-yellow-300 font-semibold': activeTab === tab.id,
                        'text-yellow-300': activeTab === tab.id && !darkMode,
                        'text-yellow-300': activeTab === tab.id && darkMode,
                        'text-gray-500': activeTab !== tab.id && !darkMode,
                        'text-white': activeTab !== tab.id && darkMode,
                    }"
                    class="cursor-pointer px-4 py-2 whitespace-nowrap"
                >
                    {{ tab.label }}
                </li>
            </ul>
        </nav>

        <h1 class="mb-4 text-xl font-bold flex items-center gap-2">
            <font-awesome-icon :icon="['fas', 'user']" class="text-[#fa8334]" />
            <p :class="textSrc">Reported Users Management</p>
        </h1>

        <!-- Search Bar -->
        <div class="mb-4 flex items-center gap-2 bg-gray-100 p-3 rounded-md">
            <font-awesome-icon :icon="['fas', 'search']" class="text-gray-500" />
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Search by reporter, reported user, or reason..."
                class="w-full bg-transparent outline-none"
            />
        </div>

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
                    <span :class="reported.ban ? 'bg-red-500 text-white' : 'bg-green-500 text-white'" class="px-3 py-1 rounded-full text-sm">
                        {{ reported.ban ? "Banned" : "Active" }}
                    </span>
                </template>

                <!-- Custom slot for Action -->
                <template #item-action="{ reported }">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" v-model="reported.ban" @change="toggleBan(reported.id)" class="sr-only" />
                        <div class="relative w-10 h-5 bg-gray-300 rounded-full transition duration-300"
                            :class="{'bg-red-500': reported.ban}">
                            <div class="absolute left-1 top-1 w-3 h-3 bg-white rounded-full transition-transform duration-300"
                                :class="{'translate-x-5': reported.ban }">
                            </div>
                        </div>
                    </label>
                </template>
            </DataTable>
        </div>

        <!-- Card Layout for Mobile -->
        <div class="sm:hidden space-y-4">
            <div
                v-for="user in filteredUsers"
                :key="user.id"
                class="bg-white p-4 rounded-lg shadow-md"
            >
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold">{{ user.reported?.name || "Unknown" }}</h2>
                    <span :class="user.reported.ban ? 'bg-red-500 text-white' : 'bg-green-500 text-white'" class="px-3 py-1 rounded-full text-sm">
                        {{ user.reported.ban ? "Banned" : "Active" }}
                    </span>
                </div>
                <p class="text-sm text-gray-600"><strong>Reported By:</strong> {{ user.reporter?.name || "Unknown" }}</p>
                <p class="text-sm text-gray-600"><strong>Reason:</strong> {{ user.reason }}</p>

                <div class="mt-3 flex justify-end">
                    <button
                        @click="toggleBan(user.reported.id)"
                        :class="user.reported.ban ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600'"
                        class="rounded px-4 py-2 text-white"
                    >
                        {{ user.reported.ban ? "Unban" : "Ban" }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
