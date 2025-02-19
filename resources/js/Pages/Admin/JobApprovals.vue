<script setup>
import { ref, computed } from "vue";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";

defineOptions({
    layout: AdminLayout,
});

// Dummy job post data
const jobs = ref([
    {
        id: 1,
        title: "Data Analyst",
        employer: "Tech Solutions",
        approved: false,
    },
    { id: 2, title: "Security Guard", employer: "Secure Corp", approved: true },
    {
        id: 3,
        title: "Graphic Designer",
        employer: "Future Web",
        approved: false,
    },
    {
        id: 4,
        title: "Software Engineer",
        employer: "Global Tech",
        approved: true,
    },
]);

// Tabs for sub-navigation
const tabs = [
    { id: "all", label: "All" },
    { id: "approved", label: "Approved" },
    { id: "pending", label: "Pending" },
];

// Active tab state
const activeTab = ref("all");

// Filter jobs based on active tab
const filteredJobs = computed(() => {
    if (activeTab.value === "all") {
        return jobs.value;
    } else if (activeTab.value === "approved") {
        return jobs.value.filter((job) => job.approved);
    } else {
        return jobs.value.filter((job) => !job.approved);
    }
});

// Approve job function
const approveJob = (id) => {
    const job = jobs.value.find((j) => j.id === id);
    if (job) {
        job.approved = true;
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
        <h1 class="mb-4 text-2xl font-bold">Job Post Approval</h1>
        <table class="min-w-full rounded-lg bg-white shadow-md">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-3">ID</th>
                    <th class="p-3">Job Title</th>
                    <th class="p-3">Employer</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="job in filteredJobs" :key="job.id" class="border-b">
                    <td class="p-3">{{ job.id }}</td>
                    <td class="p-3">{{ job.title }}</td>
                    <td class="p-3">{{ job.employer }}</td>
                    <td class="p-3">
                        <span
                            :class="
                                job.approved ? 'text-green-600' : 'text-red-600'
                            "
                        >
                            {{ job.approved ? "Approved" : "Pending" }}
                        </span>
                    </td>
                    <td class="p-3">
                        <button
                            v-if="!job.approved"
                            @click="approveJob(job.id)"
                            class="rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600"
                        >
                            Approve
                        </button>
                        <span v-else class="text-gray-500">✔ Approved</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
