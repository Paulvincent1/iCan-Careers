<script setup>
import { ref, computed } from "vue";
import { usePage, router, Link } from "@inertiajs/vue3";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import DataTable from "vue3-easy-data-table";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faSearch, faUserCheck, faUserTimes } from "@fortawesome/free-solid-svg-icons";

// Add icons
library.add(faSearch, faUserCheck, faUserTimes);

// Define Layout
defineOptions({ layout: AdminLayout });

// Get workers from Laravel
const workers = ref(usePage().props.workers || []);

// Tabs for filtering verification status
const tabs = [
    { id: "all", label: "All" },
    { id: "verified", label: "Verified" },
    { id: "not_verified", label: "Not Verified" },
];

// Active tab state
const activeTab = ref("all");

// Search input state
const searchQuery = ref("");

// Computed property for filtering workers
const filteredWorkers = computed(() => {
    let filtered = workers.value;

    // Filter by verification status
    if (activeTab.value === "verified") {
        filtered = filtered.filter(worker => worker.verified);
    } else if (activeTab.value === "not_verified") {
        filtered = filtered.filter(worker => !worker.verified);
    }

    // Apply search filtering
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(worker =>
            worker.name.toLowerCase().includes(query) ||
            worker.email.toLowerCase().includes(query) ||
            (worker.verified ? "verified" : "not verified").includes(query)
        );
    }

    return filtered;
});

// Table headers
const headers = [
    { text: "ID", value: "id", sortable: true },
    { text: "Name", value: "name", sortable: false },
    { text: "Email", value: "email", sortable: false },
    { text: "Profile", value: "profile", sortable: false },
    { text: "Verified", value: "verified", sortable: false },
    { text: "Action", value: "actions", sortable: false },
];

// Toggle verification status
const toggleVerification = (id) => {
    router.put(
        `/admin/workers/${id}/verify`,
        {},
        {
            onSuccess: () => {
                const worker = workers.value.find(w => w.id === id);
                if (worker) {
                    worker.verified = !worker.verified;
                }
            },
        }
    );
};
</script>

<template>
    <Head title="Workers | iCan Careers" />
    <div class="p-4">
        <!-- Tabs for Filtering Workers -->
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
            <font-awesome-icon :icon="['fas', 'user-check']" class="text-[#fa8334]" />
            Workers Management
        </h1>

        <!-- Search Bar -->
        <div class="mb-4 flex items-center gap-2 bg-gray-100 p-3 rounded-md">
            <font-awesome-icon :icon="['fas', 'search']" class="text-gray-500" />
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Search by name, email, or status..."
                class="w-full bg-transparent outline-none"
            />
        </div>

        <!-- DataTable for Larger Screens -->
        <div class="hidden sm:block">
            <DataTable
                :headers="headers"
                :items="filteredWorkers"
                :rows-per-page="10"
                :sort-by="'name'"
                :sort-type="'asc'"
            >
                <!-- Custom slot for Profile -->
                <template #item-profile="{ id }">
                    <Link
                        :href="`/admin/workers/${id}/verification`"
                        class="text-blue-500 hover:underline"
                    >
                        View Verification
                    </Link>
                </template>

                <!-- Custom slot for Verified status -->
                <template #item-verified="{ verified }">
                    <span :class="verified ? 'text-green-500' : 'text-red-500'">
                        {{ verified ? "Verified" : "Not Verified" }}
                    </span>
                </template>

                <!-- Custom slot for Actions -->
                <template #item-actions="{ id, verified }">
                    <button
                        @click="toggleVerification(id)"
                        class="rounded px-3 py-1 text-white flex items-center gap-1"
                        :class="verified ? 'bg-red-500 hover:bg-red-600' : 'bg-blue-500 hover:bg-blue-600'">
                        <font-awesome-icon :icon="verified ? ['fas', 'user-times'] : ['fas', 'user-check']" />
                        {{ verified ? "Unverify" : "Verify" }}
                    </button>
                </template>
            </DataTable>
        </div>

        <!-- Mobile Card Layout -->
        <div class="sm:hidden space-y-4 mt-4">
            <div v-for="worker in filteredWorkers" :key="worker.id"
                class="bg-white p-4 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold">{{ worker.name }}</h2>
                <p class="text-gray-600"><strong>Email:</strong> {{ worker.email }}</p>
                <p class="text-gray-600">
                    <strong>Profile:</strong>
                    <Link
                        :href="`/admin/workers/${worker.id}/verification`"
                        class="text-blue-500 hover:underline"
                    >
                        View Verification
                    </Link>
                </p>
                <p class="text-gray-600">
                    <strong>Status:</strong>
                    <span :class="worker.verified ? 'text-green-500' : 'text-red-500'">
                        {{ worker.verified ? "Verified" : "Not Verified" }}
                    </span>
                </p>
                <button
                    @click="toggleVerification(worker.id)"
                    class="mt-3 w-full rounded px-4 py-2 text-white text-center flex items-center justify-center gap-2"
                    :class="worker.verified ? 'bg-red-500 hover:bg-red-700' : 'bg-blue-500 hover:bg-blue-700'">
                    <font-awesome-icon :icon="worker.verified ? ['fas', 'user-times'] : ['fas', 'user-check']" />
                    {{ worker.verified ? "Unverify" : "Verify" }}
                </button>
            </div>
        </div>
    </div>
</template>