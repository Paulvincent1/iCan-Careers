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

// Tabs for sub-navigation
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
    if (activeTab.value === "all") {
        return reportedUsers.value;
    } else {
        return reportedUsers.value.filter(
            (user) => user.status.toLowerCase() === activeTab.value,
        );
    }
});

// Function to change status styles
const statusClass = (status) => {
    return {
        "text-gray-600": status === "Pending",
        "text-yellow-600": status === "Warned",
        "text-red-600": status === "Banned",
    };
};

// Warn user function
const warnUser = (id) => {
    const user = reportedUsers.value.find((u) => u.id === id);
    if (user) {
        user.status = "Warned";
    }
};

// Ban user function
const banUser = (id) => {
    const user = reportedUsers.value.find((u) => u.id === id);
    if (user) {
        user.status = "Banned";
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
        <h1 class="mb-4 text-2xl font-bold">Reported Users</h1>
        <div class="overflow-x-auto">
            <table class="min-w-full rounded-lg bg-white shadow-md">
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
                    <tr
                        v-for="user in filteredUsers"
                        :key="user.id"
                        class="border-b"
                    >
                        <td class="p-3">{{ user.id }}</td>
                        <td class="p-3">{{ user.name }}</td>
                        <td class="p-3">{{ user.reason }}</td>
                        <td class="p-3">
                            <span :class="statusClass(user.status)">{{
                                user.status
                            }}</span>
                        </td>
                        <td class="flex gap-2 p-3">
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
    </div>
</template>
