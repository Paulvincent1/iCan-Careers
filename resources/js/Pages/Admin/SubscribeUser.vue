<script setup>
import { ref, computed } from "vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faSearch, faUser } from "@fortawesome/free-solid-svg-icons";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import EasyDataTable from "vue3-easy-data-table";
import { usePage } from "@inertiajs/vue3";

// Add icons
library.add(faSearch, faUser);

// Define Admin Layout
defineOptions({
    layout: AdminLayout,
});

// Get Data from Laravel
const page = usePage();
const subscribedUsers = ref(page.props.subscribedUsers || []);

// Search Query (Email Only)
const searchQuery = ref("");

// Define Table Headers
const headers = [
    { text: "#", value: "id" },
    { text: "Name", value: "employer.name" },
    { text: "Email", value: "employer.email" },
    { text: "Plan", value: "subscription_type" },
    { text: "Start Date", value: "start_date" },
    { text: "Expiry Date", value: "expiry_date" },
];

// Filtered Data (Search Only by Email)
const filteredUsers = computed(() => {
    return subscribedUsers.value.filter((user) =>
        user.employer.email.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});
</script>

<template>
    <Head title="Subscribed Users | iCan Careers" />
    <div class="bg-white p-5 rounded-lg shadow">
        <!-- Title -->
        <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
            <font-awesome-icon :icon="['fas', 'user']" class="text-blue-500" />
            Subscribed Users
        </h2>

        <!-- Search Box (Email Only) -->
        <div class="mb-4 flex items-center gap-2 bg-gray-100 p-3 rounded-md">
            <font-awesome-icon :icon="['fas', 'search']" class="text-gray-500" />
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Search by email..."
                class="p-2 w-full bg-transparent outline-none"
            />
        </div>

        <!-- Data Table for Desktop -->
        <div class="hidden md:block overflow-x-auto rounded-lg">
            <EasyDataTable 
                :headers="headers"
                :items="filteredUsers"
                table-class-name="custom-table"
                header-text-direction="left"
                body-text-direction="left"
                theme-color="#3498db"
            />
        </div>

        <!-- Mobile View: Cards -->
        <div class="md:hidden space-y-4 mt-4">
            <div v-for="user in filteredUsers" :key="user.id" class="bg-gray-50 p-4 rounded-md shadow">
                <p class="text-lg font-semibold">{{ user.employer.name }}</p>
                <p class="text-sm text-gray-600"><strong>Email:</strong> {{ user.employer.email }}</p>
                <p class="text-sm text-gray-600"><strong>Plan:</strong> {{ user.subscription_type }}</p>
                <p class="text-sm text-gray-600"><strong>Start Date:</strong> {{ user.start_date }}</p>
                <p class="text-sm text-gray-600"><strong>Expiry Date:</strong> {{ user.expiry_date }}</p>
            </div>
        </div>
    </div>
</template>

<style>
/* Custom Table Styles */
.custom-table {
    border-radius: 10px;
    overflow: hidden;
}
</style>
