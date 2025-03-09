<script setup>
import { ref, computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import DataTable from "vue3-easy-data-table";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faSearch, faUser, faFilter } from "@fortawesome/free-solid-svg-icons";

// Add icons
library.add(faSearch, faUser, faFilter);

// Define Layout
defineOptions({ layout: AdminLayout });

// Get subscribed users from Laravel
const users = ref(usePage().props.subscribedUsers || []);

// Tabs for filtering subscription types
const tabs = [
    { id: "all", label: "All" },
    { id: "pro", label: "Pro" },
    { id: "premium", label: "Premium Plan" },
];

// Active tab state
const activeTab = ref("all");

// Search input state
const searchQuery = ref("");

// Filtered Users (Based on Plan and Search)
const filteredUsers = computed(() => {
    let filtered = users.value;

    // Filter by Plan
    if (activeTab.value !== "all") {
        filtered = filtered.filter(user => user.subscription_type.toLowerCase() === activeTab.value);
    }

    // Search Filtering
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(user =>
            user.employer.name.toLowerCase().includes(query) ||
            user.employer.email.toLowerCase().includes(query) ||
            user.subscription_type.toLowerCase().includes(query)
        );
    }

    return filtered;
});

// Table Headers
const headers = [
    { text: "ID", value: "id", sortable: true },
    { text: "Employer", value: "employer.name", sortable: false },
    { text: "Email", value: "employer.email", sortable: false },
    { text: "Plan", value: "subscription_type", sortable: false },
    { text: "Start Date", value: "start_date", sortable: false },
    { text: "Expiry Date", value: "expiry_date", sortable: false },
];
</script>

<template>
    <Head title="Subscribed Users | iCan Careers" />
    <div class="p-4 bg-white">
        <!-- Tabs for Subscription Filters -->
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
            <font-awesome-icon :icon="['fas', 'user']" class="text-[#fa8334]" />
            Subscribed Users
        </h1>

        <!-- Search Bar -->
        <div class="mb-4 flex items-center gap-2 bg-gray-100 p-3 rounded-md">
            <font-awesome-icon :icon="['fas', 'search']" class="text-gray-500" />
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Search by name, email, or plan..."
                class="w-full bg-transparent outline-none"
            />
        </div>

        <!-- DataTable for Larger Screens -->
        <div class="hidden sm:block">
            <DataTable
                :headers="headers"
                :items="filteredUsers"
                :rows-per-page="10"
                :sort-by="'id'"
                :sort-type="'asc'"
            >
                <!-- Custom slot for Subscription Type -->
                <template #item-subscription_type="{ subscription_type }">
                    <span :class="subscription_type === 'Premium' ? 'text-green-600' : 'text-blue-600'">
                        {{ subscription_type }}
                    </span>
                </template>
            </DataTable>
        </div>

        <!-- Card Layout for Mobile -->
        <div class="sm:hidden space-y-4">
            <div v-for="user in filteredUsers" :key="user.id"
                class="bg-white p-4 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold">{{ user.employer.name }}</h2>
                <p class="text-gray-600"><strong>Email:</strong> {{ user.employer.email }}</p>
                <p class="text-gray-600">
                    <strong>Plan:</strong>
                    <span :class="user.subscription_type === 'Premium' ? 'text-green-600' : 'text-blue-600'">
                        {{ user.subscription_type }}
                    </span>
                </p>
                <p class="text-gray-600"><strong>Start Date:</strong> {{ user.start_date }}</p>
                <p class="text-gray-600"><strong>Expiry Date:</strong> {{ user.expiry_date }}</p>
            </div>
        </div>
    </div>
</template>
