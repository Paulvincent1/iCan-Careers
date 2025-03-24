<script setup>
import { ref, computed, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import DataTable from "vue3-easy-data-table";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faSearch, faUser, faFilter } from "@fortawesome/free-solid-svg-icons";
import EarningsCard from "../Components/Admin/EarningsCard.vue";

const darkMode = ref(localStorage.getItem("theme") === "dark");

onMounted(() => {
    // Apply theme on mount
    document.documentElement.classList.toggle("dark", darkMode.value);

    // Listen for changes from other components
    window.addEventListener("theme-changed", () => {
        darkMode.value = localStorage.getItem("theme") === "dark";
        document.documentElement.classList.toggle("dark", darkMode.value);
    });
});
const textSrc = computed(() =>
    darkMode.value ? "text-white" : "text-gray-900",
);
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
        filtered = filtered.filter(
            (user) => user.subscription_type.toLowerCase() === activeTab.value,
        );
    }

    // Search Filtering
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (user) =>
                user.employer.name.toLowerCase().includes(query) ||
                user.employer.email.toLowerCase().includes(query) ||
                user.subscription_type.toLowerCase().includes(query),
        );
    }

    return filtered;
});

// **Data for EarningsCards**
const totalSubscriberData = computed(() => {
    return [{ name: "Total Subscriber", value: users.value.length }];
});

// Compute Subscription Type Distribution from payments
const subscriptionTypeData = computed(() => {
    if (!users.value.length) return []; // Ensure data exists

    const counts = {};
    users.value.forEach((user) => {
        if (user.subscription_type) {
            // Prevent null values
            counts[user.subscription_type] =
                (counts[user.subscription_type] || 0) + 1;
        }
    });

    return Object.keys(counts).map((type) => ({
        name: type,
        value: counts[type],
    }));
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
    <div :class="['p-4', darkMode ? 'bg-gray-700' : 'bg-white']">
        <!-- Search Bar -->
        <div class="w-[500px] mb-4 flex items-center gap-2 rounded-md bg-gray-100 p-3">
            <font-awesome-icon
                :icon="['fas', 'search']"
                class="text-gray-500"
            />
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Search by name, email, or plan..."
                class="w-full bg-transparent outline-none"
            />
        </div>

        <h1 class="mb-4 flex items-center gap-2 text-xl font-bold">
            <font-awesome-icon :icon="['fas', 'user']" class="text-[#fa8334]" />
            <p :class="textSrc">Subscribe Users</p>
        </h1>
        <hr class="my-4 border-t-2 border-gray-200 dark:border-gray-600" />
        <!-- Earnings Cards -->
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <EarningsCard
                title="Total Employers"
                :data="totalSubscriberData"
                :showChart="true"
            />
            <EarningsCard
                title="Employer Type"
                :data="subscriptionTypeData"
                :showChart="true"
            />
        </div>
        <hr class="my-4 border-t-2 border-gray-200 dark:border-gray-600" />
        <!-- Tabs for Subscription Filters -->
        <nav class="mb-6">
            <ul class="flex space-x-4 overflow-x-auto border-b">
                <li
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="{
                        'border-b-2 border-yellow-300 font-semibold':
                            activeTab === tab.id,
                        'text-yellow-300': activeTab === tab.id && !darkMode, // Light mode active tab color
                        'text-yellow-300': activeTab === tab.id && darkMode, // Dark mode active tab color
                        'text-gray-500': activeTab !== tab.id && !darkMode, // Light mode inactive tab
                        'text-white': activeTab !== tab.id && darkMode,
                    }"
                    class="cursor-pointer whitespace-nowrap px-4 py-2"
                >
                    {{ tab.label }}
                </li>
            </ul>
        </nav>

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
                    <span
                        :class="
                            subscription_type === 'Premium'
                                ? 'text-green-600'
                                : 'text-blue-600'
                        "
                    >
                        {{ subscription_type }}
                    </span>
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
                <h2 class="text-lg font-semibold">{{ user.employer.name }}</h2>
                <p class="text-gray-600">
                    <strong>Email:</strong> {{ user.employer.email }}
                </p>
                <p class="text-gray-600">
                    <strong>Plan:</strong>
                    <span
                        :class="
                            user.subscription_type === 'Premium'
                                ? 'text-green-600'
                                : 'text-blue-600'
                        "
                    >
                        {{ user.subscription_type }}
                    </span>
                </p>
                <p class="text-gray-600">
                    <strong>Start Date:</strong> {{ user.start_date }}
                </p>
                <p class="text-gray-600">
                    <strong>Expiry Date:</strong> {{ user.expiry_date }}
                </p>
            </div>
        </div>
    </div>
</template>
