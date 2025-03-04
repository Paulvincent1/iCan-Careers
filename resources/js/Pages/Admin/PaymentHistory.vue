<script setup>
import { ref, computed, getCurrentInstance } from "vue";
import { usePage } from "@inertiajs/vue3";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import DataTable from "vue3-easy-data-table";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faSearch, faCalendar, faSort } from "@fortawesome/free-solid-svg-icons";
import dayjs from "dayjs";

// Add icons to FontAwesome library
library.add(faSearch, faCalendar, faSort);

// Define Layout
defineOptions({ layout: AdminLayout });

// Get payments from Laravel
const payments = ref(usePage().props.subscriptionPaymentHistoryProps || []);

// Search input state
const searchQuery = ref("");

// Filtered Payments (Based on Search)
const filteredPayments = computed(() => {
    if (!searchQuery.value) return payments.value;

    const query = searchQuery.value.toLowerCase();
    return payments.value.filter(payment =>
        payment.employer.email.toLowerCase().includes(query) ||
        payment.subscription_type.toLowerCase().includes(query)
    );
});

// Table Headers
const headers = [
    { text: "ID", value: "id", sortable: true },
    { text: "Employer", value: "employer.email", sortable: true },
    { text: "Amount", value: "amount", sortable: true },
    { text: "Subscription Type", value: "subscription_type", sortable: true },
    { text: "Date", value: "created_at", sortable: true },
];

// Format currency function
let formatCurrency =
    getCurrentInstance().appContext.config.globalProperties.formatCurrency;
</script>

<template>
    <Head title="Payment History | iCan Careers" />
    <div class="p-4">
        <h1 class="mb-4 text-xl font-bold flex items-center gap-2">
            <font-awesome-icon :icon="['fas', 'calendar']" class="text-[#fa8334]" />
            Payment History
        </h1>

        <!-- Search Bar -->
        <div class="mb-4 flex items-center gap-2 bg-gray-100 p-3 rounded-md">
            <font-awesome-icon :icon="['fas', 'search']" class="text-gray-500" />
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Search by employer or subscription type..."
                class="w-full bg-transparent outline-none"
            />
        </div>

        <!-- DataTable for Larger Screens -->
        <div class="hidden sm:block">
            <DataTable
                :headers="headers"
                :items="filteredPayments"
                :rows-per-page="10"
                :sort-by="'id'"
                :sort-type="'asc'"
            >
                <template #item-amount="{ amount }">
                    {{ formatCurrency(amount) }}
                </template>
                <template #item-created_at="{ created_at }">
                    {{ dayjs(created_at).format("MMMM DD, YYYY") }}
                </template>
            </DataTable>
        </div>

        <!-- Card Layout for Mobile -->
        <div class="sm:hidden space-y-4">
            <div v-for="payment in filteredPayments" :key="payment.id"
                class="bg-white p-4 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold">{{ payment.employer.email }}</h2>
                <p class="text-gray-600"><strong>Amount:</strong> {{ formatCurrency(payment.amount) }}</p>
                <p class="text-gray-600"><strong>Subscription:</strong> {{ payment.subscription_type }}</p>
                <p class="text-gray-600"><strong>Date:</strong> {{ dayjs(payment.created_at).format("MMMM DD, YYYY") }}</p>
            </div>
        </div>
    </div>
</template>
