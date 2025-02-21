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
        <h2 class="text-xl font-bold mb-4">👤 Subscribed Users</h2>
        <div class="mb-4 flex items-center space-x-2">
            <font-awesome-icon :icon="['fas', 'search']" class="text-gray-500" />
            <input type="text" placeholder="Search users..." class="p-2 border rounded w-full">
        </div>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border rounded-lg">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="p-3">#</th>
                        <th class="p-3">Name <font-awesome-icon :icon="['fas', 'user']" /></th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Plan</th>
                        <th class="p-3">Expiry Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in subscribedUsers" :key="user.id" class="border-t">
                        <td class="p-3">{{ user.id }}</td>
                        <td class="p-3">{{ user.name }}</td>
                        <td class="p-3">{{ user.email }}</td>
                        <td class="p-3">{{ user.plan }}</td>
                        <td class="p-3">{{ user.expiry }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
