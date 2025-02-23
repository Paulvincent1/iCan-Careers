<script setup>
import { ref } from "vue";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import { usePage, router } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";

defineOptions({
    layout: AdminLayout,
});

// Store workers in a reactive ref() instead of directly using usePage().props
const workers = ref([...usePage().props.workers]);

const toggleVerification = (id) => {
    router.put(
        `/admin/workers/${id}/verify`,
        {},
        {
            onSuccess: () => {
                // Update the worker's verification status in the local state
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
    <div class="rounded-lg bg-white p-6 shadow">
        <h1 class="mb-4 text-2xl font-bold">Workers Management</h1>

        <!-- Desktop Table (Hidden on Small Screens) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300 min-w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border p-2">Name</th>
                        <th class="border p-2">Email</th>
                        <th class="border p-2">Profile</th>
                        <th class="border p-2">Verified</th>
                        <th class="border p-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="worker in workers"
                        :key="worker.id"
                        class="text-center"
                    >
                        <td class="border p-2">{{ worker.name }}</td>
                        <td class="border p-2">{{ worker.email }}</td>
                        <td class="border p-2">
                            <Link
                                :href="`/admin/workers/${worker.id}/verification`"
                                class="mt-2 block text-blue-500 hover:underline"
                            >
                                View Verification
                            </Link>
                        </td>
                        <td class="border p-2">
                            <span
                                :class="worker.verified ? 'text-green-500' : 'text-red-500'"
                            >
                                {{ worker.verified ? "Verified" : "Not Verified" }}
                            </span>
                        </td>
                        <td class="border p-2">
                            <button
                                @click="toggleVerification(worker.id)"
                                class="rounded px-4 py-1 text-white"
                                :class="worker.verified ? 'bg-red-500 hover:bg-red-700' : 'bg-blue-500 hover:bg-blue-700'"
                            >
                                {{ worker.verified ? "Unverify" : "Verify" }}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mobile View (Stacked Cards) -->
        <div class="md:hidden space-y-4 mt-4">
            <div v-for="worker in workers" :key="worker.id" class="bg-gray-50 p-4 rounded-md shadow">
                <p class="text-lg font-semibold">{{ worker.name }}</p>
                <p class="text-sm text-gray-600"><strong>Email:</strong> {{ worker.email }}</p>
                <p class="text-sm text-gray-600">
                    <strong>Profile:</strong>
                    <Link
                        :href="`/admin/workers/${worker.id}/verification`"
                        class="text-blue-500 hover:underline"
                    >
                        View Verification
                    </Link>
                </p>
                <p class="text-sm text-gray-600">
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
