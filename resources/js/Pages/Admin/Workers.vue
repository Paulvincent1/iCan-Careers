<script setup>
import { ref, computed } from "vue";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";

defineOptions({
    layout: AdminLayout,
});

// Dummy data for workers
const workers = ref([
    { id: 1, name: "John Doe", verified: false },
    { id: 2, name: "Jane Smith", verified: true },
    { id: 3, name: "Michael Lee", verified: false },
    { id: 4, name: "Alice Brown", verified: true },
]);

// Tabs for sub-navigation
const tabs = [
    { id: "all", label: "All" },
    { id: "verified", label: "Verified" },
    { id: "unverified", label: "Unverified" },
];

// Active tab state
const activeTab = ref("all");

// Filter workers based on active tab
const filteredWorkers = computed(() => {
    if (activeTab.value === "all") {
        return workers.value;
    } else if (activeTab.value === "verified") {
        return workers.value.filter((worker) => worker.verified);
    } else {
        return workers.value.filter((worker) => !worker.verified);
    }
});

// Function to verify a worker
const verifyWorker = (id) => {
    const worker = workers.value.find((w) => w.id === id);
    if (worker) {
        worker.verified = true;
    }
};
</script>

<template>
    <div>
        <!-- Sub-navigation -->
        <nav class="mb-6">
            <ul class="flex space-x-4 border-b">
                <li
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="{
                        'border-b-2 border-blue-500': activeTab === tab.id,
                        'text-gray-500 hover:text-gray-700':
                            activeTab !== tab.id,
                    }"
                    class="cursor-pointer px-4 py-2"
                >
                    {{ tab.label }}
                </li>
            </ul>
        </nav>

        <!-- Main content -->
        <h1 class="mb-4 text-2xl font-bold">Workers Management</h1>
        <div class="overflow-x-auto">
            <table class="min-w-full rounded-lg bg-white shadow-md">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="p-3">ID</th>
                        <th class="p-3">Name</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="worker in filteredWorkers"
                        :key="worker.id"
                        class="border-b"
                    >
                        <td class="p-3">{{ worker.id }}</td>
                        <td class="p-3">{{ worker.name }}</td>
                        <td class="p-3">
                            <span
                                :class="
                                    worker.verified
                                        ? 'text-green-600'
                                        : 'text-red-600'
                                "
                            >
                                {{
                                    worker.verified ? "Verified" : "Unverified"
                                }}
                            </span>
                        </td>
                        <td class="p-3">
                            <button
                                v-if="!worker.verified"
                                @click="verifyWorker(worker.id)"
                                class="rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600"
                            >
                                Verify
                            </button>
                            <span v-else class="text-gray-500"
                                >✔ Verified</span
                            >
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
