<script setup>
import { ref } from "vue";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import { usePage, router } from "@inertiajs/vue3";

defineOptions({
    layout: AdminLayout,
});

// Store workers in a reactive ref() instead of directly using usePage().props
const workers = ref([...usePage().props.workers]);

const toggleVerification = (id) => {
    router.put(`/admin/workers/${id}/verify`, {}, {
        onSuccess: () => {
            // Update the worker's verification status in the local state
            const worker = workers.value.find(w => w.id === id);
            if (worker) {
                worker.verified = !worker.verified;
            }
        }
    });
};
</script>

<template>
    <div class="p-6 bg-white rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-4">Workers Management</h1>

        <table class="w-full border-collapse border border-gray-300">
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
                <tr v-for="worker in workers" :key="worker.id" class="text-center">
                    <td class="border p-2">{{ worker.name }}</td>
                    <td class="border p-2">{{ worker.email }}</td>
                    <td class="border p-2">
                        <img :src="worker.profile_img" alt="Profile" class="w-10 h-10 rounded-full mx-auto" v-if="worker.profile_img">
                    </td>
                    <td class="border p-2">
                        <span :class="worker.verified ? 'text-green-500' : 'text-red-500'">
                            {{ worker.verified ? "Verified" : "Not Verified" }}
                        </span>
                    </td>
                    <td class="border p-2">
                        <button 
                            @click="toggleVerification(worker.id)" 
                            class="px-4 py-1 rounded text-white"
                            :class="worker.verified ? 'bg-red-500 hover:bg-red-700' : 'bg-blue-500 hover:bg-blue-700'">
                            {{ worker.verified ? "Unverify" : "Verify" }}
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
