<script setup>
import { computed, getCurrentInstance ,onMounted, ref} from "vue";
import { Link } from "@inertiajs/vue3";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import DashboardChart from "../Components/Admin/DashboardChart.vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faUsers, faBriefcase, faFileAlt } from "@fortawesome/free-solid-svg-icons";

library.add(faUsers, faBriefcase, faFileAlt);

const darkMode = ref(localStorage.getItem("theme") === "dark");

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
    darkMode.value ? 'text-white' : 'text-gray-900'
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

    <div :class="['p-4', darkMode ? 'bg-gray-700' : 'bg-white']">


        <!-- Grouped Main Chart & Key Statistics -->
        <div class="mt-8 grid grid-cols-1 gap-5 md:grid-cols-3">
            <!-- Main Chart (Users, Jobs, Applications) -->
            <div class="col-span-1 md:col-span-2 bg-white p-4 rounded-lg shadow-lg w-full">
                <h2 class="mb-4 text-lg md:text-xl font-bold text-center md:text-left">
                    User, Job & Application Statistics
                </h2>
                <div class="h-[250px] sm:h-[300px] md:h-[350px]">
                    <DashboardChart :chartData="chartData" />
                </div>
            </div>

            <!-- Key Statistics Cards (Users, Jobs, Applications) -->
            <div class="col-span-1 space-y-5">
                <!-- Total Users Card -->
                <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-gradient-to-b from-red-500 to-orange-500 p-10 shadow-lg">
                    <div>
                        <h2 class="text-md font-semibold text-black">Total Users</h2>
                        <p class="text-4xl font-bold text-gray-800">{{ userCountProps ?? 0 }}</p>
                    </div>
                    <font-awesome-icon :icon="['fas', 'users']" :class="['text-5xl mr-4', darkMode ? 'text-yellow-300' : 'text-black']" />
                </div>

                <!-- Total Jobs Card -->
                <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-gradient-to-b from-blue-400 to-blue-900 p-10 shadow-lg">
                    <div>
                        <h2 class="text-md font-semibold text-gray-100">Total Jobs</h2>
                        <p class="text-4xl font-bold text-gray-100">{{ jobProps?.id ?? 0 }}</p>
                    </div>
                    <font-awesome-icon :icon="['fas', 'briefcase']" class="text-white text-5xl mr-4" />
                </div>

                <!-- New Applications Card -->
                <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-gradient-to-r from-green-700 to-teal-500 p-10 shadow-lg">
                    <div>
                        <h2 class="text-md font-semibold text-white">New Applications</h2>
                        <p class="text-4xl font-bold text-white">{{ applicationProps ? applicationProps.length : 0 }}</p>
                    </div>
                    <font-awesome-icon :icon="['fas', 'file-alt']" class="text-white text-5xl mr-4" />
                </div>
            </div>
        </div>

        <!-- Total Earnings Card (Standalone at the Bottom) -->
        <div class="mt-8 rounded-lg border border-gray-200 bg-white p-6 shadow-lg">
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
