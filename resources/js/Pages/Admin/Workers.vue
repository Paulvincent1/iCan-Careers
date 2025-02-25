<script setup>
import { ref, computed } from "vue";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import { usePage, router, Link } from "@inertiajs/vue3";
import DataTable from "vue3-easy-data-table";

// Import styles for DataTable
import "vue3-easy-data-table/dist/style.css";

defineOptions({ layout: AdminLayout });

// Store workers in a reactive ref()
const workers = ref([...usePage().props.workers]);

// Search query
const searchQuery = ref("");

// Active tab (all, verified, not_verified)
const activeTab = ref("all");

// Table Headers
const headers = [
    { text: "Name", value: "name", sortable: true },
    { text: "Email", value: "email", sortable: true },
    { text: "Profile", value: "profile", sortable: false },
    { text: "Verified", value: "verified", sortable: true },
    { text: "Action", value: "actions", sortable: false },
];

// Computed property for filtering and searching
const filteredWorkers = computed(() => {
    let filtered = workers.value;

    // Apply tab filtering
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

// Toggle worker verification
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
        <h1 class="mb-4 text-2xl font-bold">Workers Management</h1>

        <!-- Tabs -->
        <div class="mb-4 flex space-x-4 border-b pb-2">
            <button
                @click="activeTab = 'all'"
                :class="activeTab === 'all' ? 'border-blue-500 text-blue-500' : 'text-gray-600'"
                class="border-b-2 px-4 py-2 font-semibold"
            >
                All
            </button>
            <button
                @click="activeTab = 'verified'"
                :class="activeTab === 'verified' ? 'border-blue-500 text-blue-500' : 'text-gray-600'"
                class="border-b-2 px-4 py-2 font-semibold"
            >
                Verified
            </button>
            <button
                @click="activeTab = 'not_verified'"
                :class="activeTab === 'not_verified' ? 'border-blue-500 text-blue-500' : 'text-gray-600'"
                class="border-b-2 px-4 py-2 font-semibold"
            >
                Not Verified
            </button>
        </div>

        <!-- Search Bar -->
        <div class="mb-4">
            <input 
                v-model="searchQuery" 
                type="text" 
                placeholder="Search by name, email, or status..."
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
            />
        </div>

        <!-- DataTable for larger screens -->
        <div class="hidden sm:block">
            <DataTable
                :headers="headers"
                :items="filteredWorkers"
                :rows-per-page="10"
                :sort-by="'name'"
                :sort-type="'asc'"
            >
                <!-- Custom slot for Profile (View Verification) -->
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
                        class="rounded px-3 py-1 text-white"
                        :class="verified ? 'bg-red-500 hover:bg-red-600' : 'bg-blue-500 hover:bg-blue-600'"
                    >
                        {{ verified ? "Unverify" : "Verify" }}
                    </button>
                </template>
            </DataTable>
        </div>

        <!-- Mobile Card Layout -->
        <div class="sm:hidden space-y-4 mt-4">
            <div v-for="worker in filteredWorkers" :key="worker.id" class="bg-white p-4 rounded-lg shadow-md">
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
                    class="mt-3 w-full rounded px-4 py-2 text-white text-center"
                    :class="worker.verified ? 'bg-red-500 hover:bg-red-700' : 'bg-blue-500 hover:bg-blue-700'"
                >
                    {{ worker.verified ? "Unverify" : "Verify" }}
                </button>
            </div>
        </div>
    </div>
</template>
