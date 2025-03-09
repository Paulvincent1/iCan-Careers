<script setup>
import { ref, computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import DataTable from "vue3-easy-data-table";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faSearch, faBuilding, faFilter } from "@fortawesome/free-solid-svg-icons";

// Add icons
library.add(faSearch, faBuilding, faFilter);

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
        filtered = filtered.filter(employer => employer.employer_type.toLowerCase() === activeTab.value);
    }

    // Search Filtering
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(employer =>
            employer.user?.username.toLowerCase().includes(query) ||
            employer.user?.email.toLowerCase().includes(query) ||
            employer.business_information?.business_name.toLowerCase().includes(query) ||
            employer.employer_type.toLowerCase().includes(query)
        );
    }

    return filtered;
});

// Table Headers
const headers = [
     { text: "ID", value: "id", sortable: true },
    { text: "Employer Name", value: "user.username", sortable: false },
    { text: "Email", value: "user.email", sortable: false },
    { text: "Business Name", value: "business_information.business_name", sortable: false },
    { text: "Employer Type", value: "employer_type", sortable: false },
];
</script>

<template>
    <Head title="Employers | iCan Careers" />
    <div class="p-4 bg-white">
        <!-- Tabs for Employer Type Filters -->
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
            <font-awesome-icon :icon="['fas', 'building']" class="text-[#fa8334]" />
            Employer List
        </h1>

        <!-- Search Bar -->
        <div class="mb-4 flex items-center gap-2 bg-gray-100 p-3 rounded-md">
            <font-awesome-icon :icon="['fas', 'search']" class="text-gray-500" />
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Search by employer, email, or business name..."
                class="w-full bg-transparent outline-none"
            />
        </div>

        <!-- DataTable for Larger Screens -->
        <div class="hidden sm:block">
            <DataTable
                :headers="headers"
                :items="filteredEmployers"
                :rows-per-page="10"
                :sort-by="'id'"
                :sort-type="'asc'"
            >
                <!-- Custom slot for Employer Type -->
                <template #item-employer_type="{ employer_type }">
                    <span :class="employer_type === 'business' ? 'text-green-600' : 'text-blue-600'">
                        {{ employer_type }}
                    </span>
                </template>
            </DataTable>
        </div>

        <!-- Card Layout for Mobile -->
        <div class="sm:hidden space-y-4">
            <div v-for="employer in filteredEmployers" :key="employer.id"
                class="bg-white p-4 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold">{{ employer.user?.username || "N/A" }}</h2>
                <p class="text-gray-600"><strong>Email:</strong> {{ employer.user?.email || "N/A" }}</p>
                <p class="text-gray-600"><strong>Business:</strong> {{ employer.business_information?.business_name || "N/A" }}</p>
                <p class="text-gray-600">
                    <strong>Type:</strong>
                    <span :class="employer.employer_type === 'business' ? 'text-green-600' : 'text-blue-600'">
                        {{ employer.employer_type }}
                    </span>
                </p>
            </div>
        </div>
    </div>
</template>
