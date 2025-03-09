<script setup>
import { ref, computed } from "vue";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";

defineOptions({
    layout: AdminLayout,
});

// Dummy reported users data
const reportedUsers = ref([
    { id: 1, name: "John Doe", reason: "Harassment", status: "Pending" },
    { id: 2, name: "Jane Smith", reason: "Scam", status: "Warned" },
    { id: 3, name: "Charlie Johnson", reason: "Spam", status: "Pending" },
    { id: 4, name: "Alice Brown", reason: "Fraud", status: "Banned" },
]);

// Tabs for filtering users
const tabs = [
    { id: "all", label: "All" },
    { id: "pending", label: "Pending" },
    { id: "warned", label: "Warned" },
    { id: "banned", label: "Banned" },
];

// Active tab state
const activeTab = ref("all");

// Filter users based on active tab
const filteredUsers = computed(() => {
    return activeTab.value === "all"
        ? reportedUsers.value
        : reportedUsers.value.filter(
              (user) => user.status.toLowerCase() === activeTab.value
          );
});

// Function to determine status styles
const statusClass = (status) => {
    return {
        "text-gray-700 bg-gray-200 px-2 py-1 rounded-full": status === "Pending",
        "text-yellow-700 bg-yellow-200 px-2 py-1 rounded-full": status === "Warned",
        "text-red-700 bg-red-200 px-2 py-1 rounded-full": status === "Banned",
    };
};

// Warn user function
const warnUser = (id) => {
    const user = reportedUsers.value.find((u) => u.id === id);
    if (user) user.status = "Warned";
};

// Ban user function
const banUser = (id) => {
    const user = reportedUsers.value.find((u) => u.id === id);
    if (user) user.status = "Banned";
};
</script>

<template>
    <Head title="ReportedUser | iCan Careers" />
    <div class="p-4 bg-white">
        <!-- Tabs Navigation -->
        <nav class="mb-6">
            <ul class="flex w-full flex-wrap gap-2 border-b overflow-x-auto">
                <li
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="{
                        'border-b-2 border-blue-500 font-semibold': activeTab === tab.id,
                        'text-gray-500 hover:text-gray-700': activeTab !== tab.id,
                    }"
                    class="cursor-pointer px-4 py-2 whitespace-nowrap"
                >
                    {{ tab.label }}
                </li>
            </ul>
        </nav>

        <h1 class="mb-4 text-2xl font-bold">Reported Users</h1>

        <!-- Desktop Table (Hidden on Mobile) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full bg-white shadow-md rounded-lg">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="p-3">ID</th>
                        <th class="p-3">Name</th>
                        <th class="p-3">Reason</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in filteredUsers" :key="user.id" class="border-b">
                        <td class="p-3">{{ user.id }}</td>
                        <td class="p-3">{{ user.name }}</td>
                        <td class="p-3">{{ user.reason }}</td>
                        <td class="p-3">
                            <span :class="statusClass(user.status)">{{ user.status }}</span>
                        </td>
                        <td class="p-3 flex gap-2">
                            <button
                                v-if="user.status === 'Pending'"
                                @click="warnUser(user.id)"
                                class="rounded bg-yellow-500 px-4 py-2 text-white hover:bg-yellow-600"
                            >
                                Warn
                            </button>
                            <button
                                v-if="user.status !== 'Banned'"
                                @click="banUser(user.id)"
                                class="rounded bg-red-500 px-4 py-2 text-white hover:bg-red-600"
                            >
                                Ban
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mobile Layout (Hidden on Desktop) -->
        <div class="md:hidden space-y-4">
            <div
                v-for="user in filteredUsers"
                :key="user.id"
                class="bg-white p-4 rounded-lg shadow border"
            >
                <p class="text-lg font-semibold text-gray-800">{{ user.name }}</p>
                <p class="text-sm text-gray-600"><strong>Reason:</strong> {{ user.reason }}</p>
                <p class="text-sm text-gray-600 flex items-center gap-1">
                    <strong>Status:</strong>
                    <span :class="statusClass(user.status)">{{ user.status }}</span>
                </p>
                <div class="mt-3 flex flex-col sm:flex-row sm:gap-2">
                    <button
                        v-if="user.status === 'Pending'"
                        @click="warnUser(user.id)"
                        class="rounded bg-yellow-500 px-4 py-2 text-white hover:bg-yellow-600 sm:w-1/2"
                    >
                        Warn
                    </button>
                    <button
                        v-if="user.status !== 'Banned'"
                        @click="banUser(user.id)"
                        class="rounded bg-red-500 px-4 py-2 text-white hover:bg-red-600 sm:w-1/2"
                    >
                        Ban
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
