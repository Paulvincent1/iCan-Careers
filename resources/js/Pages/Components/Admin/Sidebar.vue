<script setup>
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
    faChartBar,
    faUserCheck,
    faBuilding,
    faExclamationTriangle,
    faClipboardCheck,
    faMoneyBillWave, // Added payment icon
    faHistory,
    faUserTag
} from "@fortawesome/free-solid-svg-icons";

// Add icons to FontAwesome library
library.add(
    faChartBar,
    faUserCheck,
    faBuilding,
    faExclamationTriangle,
    faClipboardCheck,
    faMoneyBillWave,
    faHistory,
    faUserTag
);

defineProps({
    isSidebarOpen: Boolean, // Sidebar visibility
});

const emit = defineEmits(["toggleSidebar"]);

const closeSidebarOnMobile = () => {
    if (window.innerWidth < 768) {
        emit("toggleSidebar"); // Only close on small screens
    }
};

// State to handle payment dropdown visibility
const isPaymentOpen = ref(false);
const togglePaymentDropdown = () => {
    isPaymentOpen.value = !isPaymentOpen.value;
};
</script>

<template>
    <aside
        :class="[
            'fixed left-0 top-0 z-50 h-full w-64 bg-gradient-to-r from-blue-900 to-indigo-900 text-white transition-transform duration-300 ease-in-out',
            isSidebarOpen ? 'translate-x-0' : '-translate-x-full', // Controls visibility
        ]"
    >
        <div class="flex items-center justify-between p-4 text-xl font-bold">
            Admin Panel
            <!-- Close Button (Only visible on small screens) -->
            <button @click="closeSidebarOnMobile" class="text-white md:hidden">
                ✖
            </button>
        </div>

        <nav class="mt-5">
            <Link
                @click="closeSidebarOnMobile"
                href="/admin"
                class="block flex items-center p-3 hover:bg-gray-700"
            >
                <font-awesome-icon :icon="['fas', 'chart-bar']" class="mr-2" />
                Dashboard
            </Link>
            <Link
                @click="closeSidebarOnMobile"
                href="/admin/workers"
                class="block flex items-center p-3 hover:bg-gray-700"
            >
                <font-awesome-icon :icon="['fas', 'user-check']" class="mr-2" />
                Workers
            </Link>
            <Link
                @click="closeSidebarOnMobile"
                href="/admin/employers"
                class="block flex items-center p-3 hover:bg-gray-700"
            >
                <font-awesome-icon :icon="['fas', 'building']" class="mr-2" />
                Employers
            </Link>
            <Link
                @click="closeSidebarOnMobile"
                href="/admin/reported-users"
                class="flex items-center p-3 hover:bg-gray-700"
            >
                <font-awesome-icon
                    :icon="['fas', 'exclamation-triangle']"
                    class="mr-2"
                />
                Reported Users
            </Link>
            <Link
                @click="closeSidebarOnMobile"
                href="/admin/job-approvals"
                class="flex items-center p-3 hover:bg-gray-700"
            >
                <font-awesome-icon
                    :icon="['fas', 'clipboard-check']"
                    class="mr-2"
                />
                Job Approvals
            </Link>

            <!-- Payment Section with Dropdown -->
            <div>
                <button
                    @click="togglePaymentDropdown"
                    class="w-full flex items-center justify-between p-3 hover:bg-gray-700 focus:outline-none"
                >
                    <div class="flex items-center">
                        <font-awesome-icon :icon="['fas', 'money-bill-wave']" class="mr-2" />
                        Payment
                    </div>
                    <span>{{ isPaymentOpen ? "▼" : "▶" }}</span>
                </button>
                <div v-if="isPaymentOpen" class="ml-6">
                    <Link
                        @click="closeSidebarOnMobile"
                        href="/admin/payment-history"
                        class="block flex items-center p-3 hover:bg-gray-700"
                    >
                        <font-awesome-icon :icon="['fas', 'history']" class="mr-2" />
                        Payment History
                    </Link>
                    <Link
                        @click="closeSidebarOnMobile"
                        href="/admin/table-subscription"
                        class="block flex items-center p-3 hover:bg-gray-700"
                    >
                        <font-awesome-icon :icon="['fas', 'user-tag']" class="mr-2" />
                        Subscribed Users
                    </Link>
                </div>
            </div>
        </nav>
    </aside>
</template>

<style scoped>
/* Ensures smooth transitions */
aside {
    transition: transform 0.3s ease-in-out;
}
</style>
