<script setup>
import { computed, getCurrentInstance, onMounted, ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import DashboardChart from "../Components/Admin/DashboardChart.vue";
import LineChartCard from "../Components/Admin/LineChartCard.vue";
import BarChartCard from "../Components/Admin/BarChartCard.vue";
import PieChartCard from "../Components/Admin/PieChartCard.vue";
import { library } from "@fortawesome/fontawesome-svg-core";

import {
    faUsers,
    faBriefcase,
    faFileAlt,
    faUserTie,
    faUserSlash,
    faTriangleExclamation,
} from "@fortawesome/free-solid-svg-icons";
library.add(
    faUsers,
    faBriefcase,
    faFileAlt,
    faUserTie,
    faUserSlash,
    faTriangleExclamation,
);

const darkMode = ref(localStorage.getItem("theme") === "dark");
const workers = ref(usePage().props.workers || []);
const employers = ref(usePage().props.employers || []);
const reportedUsers = ref(usePage().props.reportedUsers || 0);
const reportedPosts = ref(usePage().props.reportedPosts || 0);

onMounted(() => {
    // Apply theme on mount
    document.documentElement.classList.toggle("dark", darkMode.value);
    // Listen for changes from other components
    window.addEventListener("theme-changed", () => {
        darkMode.value = localStorage.getItem("theme") === "dark";
        document.documentElement.classList.toggle("dark", darkMode.value);
    });
});
const textSrc = computed(() =>
    darkMode.value ? "text-white" : "text-gray-900",
);

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

// Earnings Data
const earningsData = computed(() => ({
    days: props.salaryProps?.days ?? [], // Use days instead of months
    earnings: props.salaryProps?.dailyEarnings ?? [], // Fetch daily earnings
}));
const isEarningsChart = computed(() => props.type === "earnings");
// **Data for EarningsCards**
// Sample data transformation for line graphs
const jobData = computed(() => [
    { name: "Total Jobs", value: props.jobProps || 0 },
]);

const applicationData = computed(() => [
    { name: "Total Applications", value: props.applicationProps || 0 },
]);

const workerData = computed(() => [
    { name: "Total Workers", value: workers.value },
]);

const employerData = computed(() => [
    { name: "Total Employers", value: employers.value },
]);

const reportedUsersData = computed(() => [
    { name: "Reported Users", value: reportedUsers.value },
]);

const reportedPostsData = computed(() => [
    { name: "Reported Jobs", value: reportedPosts.value },
]);
</script>

<template>
    <Head title="Dashboard | iCan Careers" />

    <div :class="['p-4', darkMode ? 'bg-gray-700' : 'bg-white']">
        <h1 class="mb-4 flex items-center gap-2 text-xl font-bold">
            <font-awesome-icon
                :icon="['fas', 'chart-bar']"
                class="text-[#fa8334]"
            />
            <p :class="textSrc">Dashboard</p>
        </h1>
        <hr class="border-t-2 border-gray-200 dark:border-gray-600" />
        <!-- Grouped Main Chart & Key Statistics -->
        <div :class="['p-4', darkMode ? 'bg-gray-700' : 'bg-white']">
            <!-- Key Statistics Cards (Displayed in rows of 3) -->
            <div class="mt-8 grid grid-cols-1 gap-5 md:grid-cols-3">
                <PieChartCard
                    title="Total Jobs"
                    :data="jobData"
                    :icon="['fas', 'briefcase']"
                    :link="route('admin.job.approvals')"
                    linkText="View Jobs→"
                />
                <PieChartCard
                    title="Total Applications"
                    :data="applicationData"
                    :icon="['fas', 'file-alt']"
                />
                <LineChartCard
                    title="Total Workers"
                    :data="workerData"
                    :icon="['fas', 'users']"
                    :link="route('admin.job.approvals')"
                    linkText="View Workers→"
                />
            </div>
            <hr class="my-4 border-t-2 border-gray-200 dark:border-gray-600" />

            <div class="mt-8 grid grid-cols-1 gap-5 md:grid-cols-3">
                <LineChartCard
                    title="Total Employers"
                    :data="employerData"
                    :icon="['fas', 'user-tie']"
                    :link="route('admin.job.approvals')"
                    linkText="View Employers→"
                />
                <BarChartCard
                    title="Reported Users"
                    :data="reportedUsersData"
                    :icon="['fas', 'user-slash']"
                    :link="route('admin.reported.users')"
                    linkText="View Reports→"
                />
                <BarChartCard
                    title="Reported Jobs"
                    :data="reportedPostsData"
                    :icon="['fas', 'triangle-exclamation']"
                    :link="route('admin.reported.posts')"
                    linkText="View Reports→"
                />
            </div>
        </div>
        <hr class="my-4 border-t-2 border-gray-200 dark:border-gray-600" />
        <!-- Total Earnings Card (Standalone at the Bottom) -->
        <div class="mt-8 rounded-lg bg-white p-6">
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
    </div>
</template>
