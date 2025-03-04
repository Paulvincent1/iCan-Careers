<script setup>
import { computed, getCurrentInstance } from "vue";
import { Link } from "@inertiajs/vue3";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import DashboardChart from "../Components/Admin/DashboardChart.vue";

// Props
const props = defineProps({
    salaryProps: Object,
    userCountProps: Number,
    applicationProps: Array,
    jobProps: Object,
});

// Define Layout
defineOptions({ layout: AdminLayout });

// Get today's date
const todayDate = new Date().toLocaleDateString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
});

// Currency Formatter
const formatCurrency =
    getCurrentInstance().appContext.config.globalProperties.formatCurrency;

// Chart Data
const chartData = computed(() => ({
    users: props.userCountProps ?? 0,
    jobs: props.jobProps?.id ?? 0,
    applications: props.applicationProps ? props.applicationProps.length : 0,
}));

// Earnings Data
const earningsData = computed(() => ({
    months: props.salaryProps?.months ?? [],
    earnings: props.salaryProps?.monthlyEarnings ?? [],
}));
</script>

<template>
    <Head title="Dashboard | iCan Careers" />

    <div class="p-4">
        <!-- Title -->
        <h1
            class="text-center text-3xl font-bold text-gray-800 sm:text-4xl md:text-left"
        >
            Admin Dashboard
        </h1>

        <!-- Total Earnings Card (Standalone at the Top) -->
        <div
            class="mt-8 rounded-lg border border-gray-200 bg-white p-6 shadow-lg"
        >
            <h2 class="text-lg font-bold text-gray-700 md:text-xl">
                Total Earnings
            </h2>
            <p class="text-3xl font-bold text-green-500 md:text-4xl">
                {{ formatCurrency(salaryProps?.total_earnings ?? 0) }}
            </p>
            <p class="text-sm text-gray-500">Earnings as of {{ todayDate }}</p>
            <Link
                :href="`/admin/payment-history`"
                class="mt-2 inline-block text-sm font-semibold text-blue-500 hover:underline"
            >
                View Payment History →
            </Link>
            <!-- Earnings Chart: Hidden on small screens -->
            <div class="hidden md:block md:h-[350px]">
                <DashboardChart :earningsData="earningsData" type="earnings" />
            </div>
        </div>

        <!-- Grouped Main Chart & Key Statistics at the Bottom -->
        <div class="mt-8 grid grid-cols-1 gap-5 md:grid-cols-3">
           
             <!-- Main Chart (Users, Jobs, Applications) -->
            <div class="col-span-1 md:col-span-2 bg-white p-4 rounded-lg shadow w-full">
                <h2 class="mb-4 text-lg md:text-xl font-bold text-center md:text-left">
                    User, Job & Application Statistics
                </h2>
                <div class="h-[250px] sm:h-[300px] md:h-[350px]">
                    <DashboardChart :chartData="chartData" />
                </div>
            </div>

             <!-- Key Statistics Cards (Users, Jobs, Applications) -->
            <div class="grid space-y-5">
                <div
                    class="flex hidden flex-col items-center rounded-lg border border-gray-200 bg-white p-6 shadow-lg md:block"
                >
                    <h2 class="text-md font-semibold text-gray-600">
                        Total Users
                    </h2>
                    <p class="text-4xl font-bold text-blue-600">
                        {{ userCountProps ?? 0 }}
                    </p>
                </div>
                <div
                    class="flex hidden flex-col items-center rounded-lg border border-gray-200 bg-white p-6 shadow-lg md:block"
                >
                    <h2 class="text-md font-semibold text-gray-600">
                        Total Jobs
                    </h2>
                    <p class="text-4xl font-bold text-red-500">
                        {{ jobProps?.id ?? 0 }}
                    </p>
                </div>
                <div
                    class="flex hidden md:block flex-col items-center rounded-lg border border-gray-200 bg-white p-6 shadow-lg"
                >
                    <h2 class="text-md font-semibold text-gray-600">
                        New Applications
                    </h2>
                    <p class="text-4xl font-bold text-green-600">
                        {{ applicationProps ? applicationProps.length : 0 }}
                    </p>
                </div>
            </div>

        </div>
    </div>
</template>
