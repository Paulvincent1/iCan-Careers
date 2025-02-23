<script setup>
import { ref } from "vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faSearch, faUser } from "@fortawesome/free-solid-svg-icons";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";

defineOptions({
    layout: AdminLayout,
});

// Add icons to FontAwesome library
library.add(faSearch, faUser);

// Dummy Data for Subscribed Users
const subscribedUsers = ref([
    { id: 1, name: "Alice Brown", email: "alice@example.com", plan: "Premium", expiry: "2025-06-20" },
    { id: 2, name: "Bob Green", email: "bob@example.com", plan: "Standard", expiry: "2025-05-15" },
    { id: 3, name: "Charlie White", email: "charlie@example.com", plan: "Basic", expiry: "2025-04-10" },
]);
</script>

<template>
    <div class="bg-white p-5 rounded-lg shadow">
        <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
            <font-awesome-icon :icon="['fas', 'user']" class="text-blue-500" /> Subscribed Users
        </h2>

        <!-- Search Box -->
        <div class="mb-4 flex items-center gap-2 bg-gray-100 p-3 rounded-md">
            <font-awesome-icon :icon="['fas', 'search']" class="text-gray-500" />
            <input
                type="text"
                placeholder="Search users..."
                class="p-2 w-full bg-transparent outline-none"
            />
        </div>

        <!-- Table for Desktop -->
        <div class="hidden md:block overflow-x-auto rounded-lg">
            <table class="w-full border-collapse border rounded-lg min-w-full">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="p-3">#</th>
                        <th class="p-3">Name</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Plan</th>
                        <th class="p-3">Expiry Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in subscribedUsers" :key="user.id" class="border-t hover:bg-gray-100 transition">
                        <td class="p-3">{{ user.id }}</td>
                        <td class="p-3">{{ user.name }}</td>
                        <td class="p-3">{{ user.email }}</td>
                        <td class="p-3">{{ user.plan }}</td>
                        <td class="p-3">{{ user.expiry }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mobile View (Stacked Cards) -->
        <div class="md:hidden space-y-4 mt-4">
            <div v-for="user in subscribedUsers" :key="user.id" class="bg-gray-50 p-4 rounded-md shadow">
                <p class="text-lg font-semibold">{{ user.name }}</p>
                <p class="text-sm text-gray-600"><strong>Email:</strong> {{ user.email }}</p>
                <p class="text-sm text-gray-600"><strong>Plan:</strong> {{ user.plan }}</p>
                <p class="text-sm text-gray-600"><strong>Expiry:</strong> {{ user.expiry }}</p>
            </div>
        </div>
    </div>
</template>
