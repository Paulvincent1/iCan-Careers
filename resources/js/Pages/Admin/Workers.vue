<script setup>
import { ref, computed, onMounted } from "vue";
import { usePage, router, Link } from "@inertiajs/vue3";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import DataTable from "vue3-easy-data-table";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
    faSearch,
    faUserCheck,
    faUserTimes,
} from "@fortawesome/free-solid-svg-icons";
import EarningsCard from "../Components/Admin/EarningsCard.vue";

// Dark mode state
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

// Text color for dark mode
const textSrc = computed(() =>
    darkMode.value ? "text-white" : "text-gray-900",
);

// Add icons
library.add(faSearch, faUserCheck, faUserTimes);

// Define Layout
defineOptions({ layout: AdminLayout });

// Get workers from Laravel
const workers = ref(usePage().props.workers || []);

// Tabs for filtering verification status
const tabs = [
    { id: "all", label: "All" },
    { id: "pending", label: "Pending" },
    { id: "not_verified", label: "Not Verified" },
];

// Active tab state
const activeTab = ref("all");

// Search input state
const searchQuery = ref("");

// Computed property for filtering workers
const filteredWorkers = computed(() => {
    let filtered = workers.value;

    if (activeTab.value === "verified") {
        filtered = filtered.filter((worker) => worker.verified);
    } else if (activeTab.value === "not_verified") {
        filtered = filtered.filter((worker) => !worker.verified);
    } else if (activeTab.value === "pending") {
        filtered = filtered.filter((worker) => worker.verification_id);
    }

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (worker) =>
                worker.name.toLowerCase().includes(query) ||
                worker.email.toLowerCase().includes(query) ||
                (worker.verified ? "verified" : "not verified").includes(
                    query,
                ) ||
                (worker.verification_id ? "pending" : "none").includes(query),
        );
    }

    return filtered;
});

// **Data for EarningsCards**
const totalWorkersData = computed(() => {
    return [{ name: "Total Workers", value: workers.value.length }];
});

const workerVerified = computed(() => {
    const verified = workers.value.filter(
        (worker) => workers.verified === "Verified",
    ).length;
    const not_verified = workers.value.filter(
        (worker) => workers.verified === "Not_Verified",
    ).length;

    return [
        { name: "Verified", value: verified },
        { name: "Not Verified", value: not_verified },
    ];
});
const workerVerificationID = computed(() => {
    const pending = workers.value.filter(
        (worker) => worker.verification_id === "Pending",
    ).length;
    const none = workers.value.filter(
        (worker) => worker.verification_id === "None",
    ).length;

    return [
        { name: "Pending", value: pending },
        { name: "None", value: none },
    ];
});

// Table headers
const headers = [
    { text: "ID", value: "id", sortable: true },
    { text: "Name", value: "name", sortable: false },
    { text: "Email", value: "email", sortable: false },
    { text: "Profile", value: "profile", sortable: false },
    { text: "Verified", value: "verified", sortable: false },
    { text: "Verification ID", value: "verification_id", sortable: false },
    { text: "Action", value: "actions", sortable: false },
];

// Toggle verification status
const toggleVerification = (id) => {
    router.put(
        `/admin/workers/${id}/verify`,
        {},
        {
            onSuccess: () => {
                const worker = workers.value.find((w) => w.id === id);
                if (worker) {
                    worker.verified = !worker.verified;
                }
            },
        },
    );
};
</script>

<template>
    <Head title="Workers | iCan Careers" />
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
                placeholder="Search by name, email, or status..."
                class="w-full bg-transparent outline-none"
            />
        </div>
        <h1 class="mb-4 flex items-center gap-2 text-xl font-bold">
            <font-awesome-icon
                :icon="['fas', 'user-check']"
                class="text-[#fa8334]"
            />
            <p :class="textSrc">Workers Management</p>
        </h1>
        <hr class="my-4 border-t-2 border-gray-200 dark:border-gray-600" />
        <!-- Earnings Cards -->
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <EarningsCard
                title="Total Workers"
                :data="totalWorkersData"
                :showChart="true"
            />
            <EarningsCard
                title="Worker Status"
                :data="workerVerified"
                :showChart="true"
            />
            <EarningsCard
                title="Workers Verification"
                :data="workerVerificationID"
                :showChart="true"
            />
        </div>
        <hr class="my-4 border-t-2 border-gray-200 dark:border-gray-600" />
        <!-- Tabs for Filtering Workers -->
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
                :items="filteredWorkers"
                :rows-per-page="10"
                :sort-by="'id'"
                :sort-type="'asc'"
                :class="darkMode ? 'bg-gray-800 text-white' : ''"
            >
                <template #item-profile="{ id }">
                    <Link
                        :href="`/admin/workers/${id}/verification`"
                        class="text-blue-500 hover:underline"
                    >
                        View Verification
                    </Link>
                </template>

                <template #item-verified="{ verified }">
                    <span :class="verified ? 'text-green-500' : 'text-red-500'">
                        {{ verified ? "Verified" : "Not Verified" }}
                    </span>
                </template>

                <template #item-verification_id="{ verification_id }">
                    <span
                        :class="
                            verification_id ? 'text-green-500' : 'text-red-500'
                        "
                        >{{ verification_id ? "Pending" : "None" }}</span
                    >
                </template>

                <template #item-actions="{ id, verified }">
                    <button
                        @click="toggleVerification(id)"
                        class="flex items-center gap-1 rounded px-3 py-1 text-white"
                        :class="
                            verified
                                ? 'bg-red-500 hover:bg-red-600'
                                : 'bg-blue-500 hover:bg-blue-600'
                        "
                    >
                        <font-awesome-icon
                            :icon="
                                verified
                                    ? ['fas', 'user-times']
                                    : ['fas', 'user-check']
                            "
                        />
                        {{ verified ? "Unverify" : "Verify" }}
                    </button>
                </template>
            </DataTable>
        </div>
    </div>
</template>
