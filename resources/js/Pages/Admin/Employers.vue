<script setup>
import { ref, computed } from "vue";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";

defineOptions({
    layout: AdminLayout,
});

// Dummy employer data
const employers = ref([
    { id: 1, company: "Tech Solutions", owner: "Alice Brown", verified: false },
    { id: 2, company: "Secure Corp", owner: "Bob Smith", verified: true },
    { id: 3, company: "Future Web", owner: "Charlie Johnson", verified: false },
    { id: 4, company: "Global Tech", owner: "Diana Evans", verified: true },
]);

// Tabs for sub-navigation
const tabs = [
    { id: "all", label: "All" },
    { id: "verified", label: "Verified" },
    { id: "unverified", label: "Unverified" },
];

// Active tab state
const activeTab = ref("all");

// Filter employers based on active tab
const filteredEmployers = computed(() => {
    if (activeTab.value === "all") {
        return employers.value;
    } else if (activeTab.value === "verified") {
        return employers.value.filter((employer) => employer.verified);
    } else {
        return employers.value.filter((employer) => !employer.verified);
    }
});

// Verify employer function
const verifyEmployer = (id) => {
    const employer = employers.value.find((e) => e.id === id);
    if (employer) {
        employer.verified = true;
    }
};
</script>

<template>
    <div class="p-4">
        <!-- Sub-navigation -->
        <nav class="mb-6">
            <ul class="flex space-x-4 border-b overflow-x-auto whitespace-nowrap">
                <li
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="{
                        'border-b-2 border-blue-500': activeTab === tab.id,
                        'text-gray-500 hover:text-gray-700':
                            activeTab !== tab.id,
                    }"
                    class="cursor-pointer px-4 py-2 text-sm md:text-base"
                >
                    {{ tab.label }}
                </li>
            </ul>
        </nav>

        <h1 class="mb-4 text-xl md:text-2xl font-bold">Employer Verification</h1>

        <!-- Responsive Table Wrapper -->
        <div class="overflow-x-auto rounded-lg shadow-md">
            <table class="min-w-full bg-white">
                <thead>
                    <tr class="bg-gray-200 text-left text-xs md:text-sm">
                        <th class="p-3">ID</th>
                        <th class="p-3">Company</th>
                        <th class="p-3 hidden md:table-cell">Owner</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="employer in filteredEmployers"
                        :key="employer.id"
                        class="border-b text-xs md:text-sm"
                    >
                        <td class="p-3">{{ employer.id }}</td>
                        <td class="p-3">{{ employer.company }}</td>
                        <td class="p-3 hidden md:table-cell">{{ employer.owner }}</td>
                        <td class="p-3">
                            <span
                                :class="employer.verified ? 'text-green-600' : 'text-red-600'"
                            >
                                {{ employer.verified ? "Verified" : "Unverified" }}
                            </span>
                        </td>
                        <td class="p-3">
                            <button
                                v-if="!employer.verified"
                                @click="verifyEmployer(employer.id)"
                                class="rounded bg-blue-500 px-3 py-1 md:px-4 md:py-2 text-white hover:bg-blue-600 text-xs md:text-sm"
                            >
                                Verify
                            </button>
                            <span v-else class="text-gray-500">✔ Verified</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
