<script setup>
import { ref, computed, getCurrentInstance, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import DataTable from "vue3-easy-data-table";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
    faSearch,
    faCalendar,
    faSort,
} from "@fortawesome/free-solid-svg-icons";
import dayjs from "dayjs";
import EarningsCard from "../Components/Admin/EarningsCard.vue";

const darkMode = ref(localStorage.getItem("theme") === "dark");
onMounted(() => {
    // Apply theme on mount
    document.documentElement.classList.toggle("dark", darkMode.value);
    console.log("Total Earnings:", salaryProps.value.total_earnings);
    console.log("Pie Chart Data:", pieChartData.value);
    // Listen for changes from other components
    window.addEventListener("theme-changed", () => {
        darkMode.value = localStorage.getItem("theme") === "dark";
        document.documentElement.classList.toggle("dark", darkMode.value);
    });
});
const textSrc = computed(() =>
    darkMode.value ? "text-white" : "text-gray-900",
);
// Add icons to FontAwesome library
library.add(faSearch, faCalendar, faSort);

// Define Layout
defineOptions({ layout: AdminLayout });

// Get payments from Laravel
const payments = ref(usePage().props.subscriptionPaymentHistoryProps || []);
const salaryProps = ref(usePage().props.salaryProps || {});
const subscribedUsersData = ref(usePage().props.subscribedUsersData || {});

// Search input state
const searchQuery = ref("");

// Tabs for filtering payments
const tabs = [
    { id: "all", label: "All" },
    { id: "pro", label: "PRO" },
    { id: "premium", label: "Premium" },
];

const activeTab = ref("all");

// Filtered Payments (Based on Tab and Search)
const filteredPayments = computed(() => {
    let filtered = payments.value;

    // Filter by subscription type
    if (activeTab.value === "pro") {
        filtered = filtered.filter((payment) =>
            payment.subscription_type.toLowerCase().includes("pro"),
        );
    } else if (activeTab.value === "premium") {
        filtered = filtered.filter((payment) =>
            payment.subscription_type.toLowerCase().includes("premium"),
        );
    }

    // Search Filtering
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (payment) =>
                payment.employer.email.toLowerCase().includes(query) ||
                payment.subscription_type.toLowerCase().includes(query),
        );
    }

    return filtered;
});
// Get earnings data from Laravel props
const earningsData = ref(usePage().props.salaryProps || {});

// Compute pie chart data for Total Earnings using daily data
const pieChartData = computed(() => {
    if (!salaryProps.value || !salaryProps.value.days) return [];
    return (salaryProps.value.days || []).map((day, index) => ({
        name: day,
        value: salaryProps.value.dailyEarnings?.[index] ?? 0, // Use optional chaining
    }));
});

// Compute Subscription Type Distribution from payments
const subscriptionTypeData = computed(() => {
    if (!payments.value.length) return []; // Ensure data exists

    const counts = {};
    payments.value.forEach((payment) => {
        if (payment.subscription_type) {
            // Prevent null values
            counts[payment.subscription_type] =
                (counts[payment.subscription_type] || 0) + 1;
        }
    });

    return Object.keys(counts).map((type) => ({
        name: type,
        value: counts[type],
    }));
});

// Compute number of unique subscribed employers
const subscribedUsersCount = computed(() => {
    const unique = new Set();
    payments.value.forEach((payment) => {
        if (payment.employer && payment.employer.email) {
            unique.add(payment.employer.email);
        }
    });
    return unique.size;
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
    <div :class="['h-full p-4', darkMode ? 'bg-gray-700' : 'bg-white']">
        <!-- Search Bar -->
        <div class="w-[500px] mb-4 flex items-center gap-2 rounded-md bg-gray-100 p-3">
            <font-awesome-icon
                :icon="['fas', 'search']"
                class="text-gray-500"
            />
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Search by employer or subscription type..."
                class="w-full bg-transparent outline-none"
            />
        </div>

        <h1 class="mb-4 flex items-center gap-2 text-xl font-bold">
            <font-awesome-icon
                :icon="['fas', 'calendar']"
                class="text-[#fa8334]"
            />
            <p :class="textSrc">Payment History</p>
        </h1>
        <hr class="my-4 border-t-2 border-gray-200 dark:border-gray-600">

        <!-- Top Stats Cards -->
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <!-- Total Earnings Card with Pie Chart -->
            <EarningsCard
                title="Total Earnings"
                :total="
                    salaryProps.total_earnings
                        ? formatCurrency(salaryProps.total_earnings)
                        : '₱0'
                "
                :data="
                    pieChartData.length
                        ? pieChartData
                        : [{ name: 'No Data', value: 1 }]
                "
                :showChart="!!pieChartData.length"
            />
            <!-- Subscription Type Distribution Card -->
            <EarningsCard
                title="Type of Payment"
                :data="subscriptionTypeData"
                :showChart="true"
            />
            <!-- Number of Subscribed Users Card (No chart) -->
            <EarningsCard
                title="Employer Subscription Status"
                :data="subscribedUsersData"
                :showChart="true"
            />
        </div>
        <hr class="my-4 border-t-2 border-gray-200 dark:border-gray-600">

        <!-- Tabs Navigation -->
        <nav class="mb-6">
            <ul class="flex space-x-4 overflow-x-auto border-b">
                <li
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="{
                        'border-b-2 border-yellow-300 font-semibold':
                            activeTab === tab.id,
                        'text-yellow-300': activeTab === tab.id && !darkMode,
                        'text-yellow-300': activeTab === tab.id && darkMode,
                        'text-gray-500': activeTab !== tab.id && !darkMode,
                        'text-white': activeTab !== tab.id && darkMode,
                    }"
                    class="cursor-pointer whitespace-nowrap px-4 py-2"
                >
                    {{ tab.label }}
                </li>
            </ul>
        </nav>

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
        <div class="space-y-4 sm:hidden">
            <div
                v-for="payment in filteredPayments"
                :key="payment.id"
                class="rounded-lg bg-white p-4 shadow-md"
            >
                <h2 class="text-lg font-semibold">
                    {{ payment.employer.email }}
                </h2>
                <p class="text-gray-600">
                    <strong>Amount:</strong>
                    {{ formatCurrency(payment.amount) }}
                </p>
                <p class="text-gray-600">
                    <strong>Subscription:</strong>
                    {{ payment.subscription_type }}
                </p>
                <p class="text-gray-600">
                    <strong>Date:</strong>
                    {{ dayjs(payment.created_at).format("MMMM DD, YYYY") }}
                </p>
            </div>
        </div>
    </div>
</template>
