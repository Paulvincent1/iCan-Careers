<script setup>
import { ref, computed, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import DataTable from "vue3-easy-data-table";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
    faSearch,
    faBuilding,
    faFilter,
    faUserTie,
} from "@fortawesome/free-solid-svg-icons";
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
library.add(faSearch, faBuilding, faFilter, faUserTie);

// Define Layout
defineOptions({ layout: AdminLayout });

// Get employers data from Laravel
const employers = ref(usePage().props.employers || []);

// Tabs for filtering employer types
const tabs = [
    { id: "all", label: "All" },
    { id: "business", label: "Business" },
    { id: "individual", label: "Individual" },
];

// Active tab state
const activeTab = ref("all");

// Search input state
const searchQuery = ref("");

// Filtered Employers (Based on Type and Search)
const filteredEmployers = computed(() => {
    let filtered = employers.value;

    // Filter by Employer Type
    if (activeTab.value !== "all") {
        filtered = filtered.filter(
            (employer) =>
                employer.employer_type.toLowerCase() === activeTab.value,
        );
    }

    // Search Filtering
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (employer) =>
                employer.user?.username.toLowerCase().includes(query) ||
                employer.user?.email.toLowerCase().includes(query) ||
                employer.business_information?.business_name
                    .toLowerCase()
                    .includes(query) ||
                employer.employer_type.toLowerCase().includes(query),
        );
    }

    return filtered;
});

// **Data for EarningsCards**
const totalEmployersData = computed(() => {
    return [{ name: "Total Employers", value: employers.value.length }];
});

const employerType = computed(() => {
    const individual = employers.value.filter(
        (employer) => employer.employer_type === "Individual",
    ).length;
    const business = employers.value.filter(
        (employer) => employer.employer_type === "Business",
    ).length;

    return [
        { name: "Individual", value: individual },
        { name: "Business", value: business },
    ];
});

// Table Headers
const headers = [
    { text: "ID", value: "id", sortable: true },
    { text: "Employer Name", value: "user.username", sortable: false },
    { text: "Email", value: "user.email", sortable: false },
    {
        text: "Business Name",
        value: "business_information.business_name",
        sortable: false,
    },
    { text: "Employer Type", value: "employer_type", sortable: false },
];
</script>

<template>
    <Head title="Employers | iCan Careers" />
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
                placeholder="Search by employer, email, or business name..."
                class="w-full bg-transparent outline-none"
            />
        </div>

        <h1 class="mb-4 flex items-center gap-2 text-xl font-bold">
            <font-awesome-icon
                :icon="['fas', 'user-tie']"
                class="text-[#fa8334]"
            />
            <p :class="textSrc">Employers Management</p>
        </h1>
        <hr class="my-4 border-t-2 border-gray-200 dark:border-gray-600" />

        <!-- Earnings Cards -->
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <EarningsCard
                title="Total Employers"
                :data="totalEmployersData"
                :showChart="true"
            />
            <EarningsCard
                title="Employer Type"
                :data="employerType"
                :showChart="true"
            />
        </div>
        <hr class="my-4 border-t-2 border-gray-200 dark:border-gray-600" />
        <!-- Tabs for Employer Type Filters -->
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
        <div class="relative z-0 hidden sm:block">
            <DataTable
                :headers="headers"
                :items="filteredEmployers"
                :rows-per-page="10"
                :sort-by="'id'"
                :sort-type="'asc'"
            >
                <!-- Custom slot for Employer Type -->
                <template #item-employer_type="{ employer_type }">
                    <span
                        :class="
                            employer_type === 'business'
                                ? 'text-green-600'
                                : 'text-blue-600'
                        "
                    >
                        {{ employer_type }}
                    </span>
                </template>
            </DataTable>
        </div>

        <!-- Card Layout for Mobile -->
        <div class="space-y-4 sm:hidden">
            <div
                v-for="employer in filteredEmployers"
                :key="employer.id"
                class="rounded-lg bg-white p-4 shadow-md"
            >
                <h2 class="text-lg font-semibold">
                    {{ employer.user?.username || "N/A" }}
                </h2>
                <p class="text-gray-600">
                    <strong>Email:</strong> {{ employer.user?.email || "N/A" }}
                </p>
                <p class="text-gray-600">
                    <strong>Business:</strong>
                    {{ employer.business_information?.business_name || "N/A" }}
                </p>
                <p class="text-gray-600">
                    <strong>Type:</strong>
                    <span
                        :class="
                            employer.employer_type === 'business'
                                ? 'text-green-600'
                                : 'text-blue-600'
                        "
                    >
                        {{ employer.employer_type }}
                    </span>
                </p>
            </div>
        </div>
    </div>
</template>
