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
    faMoneyBillWave,
    faHistory,
    faUserTag,
    faTimes,
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
    faUserTag,
    faTimes
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
            'fixed left-0 top-0 z-50 h-full w-64 bg-mocha-dark text-mocha-light transition-transform duration-500 ease-in-out shadow-xl border-r border-mocha-border',
            isSidebarOpen ? 'translate-x-0' : '-translate-x-full'
        ]"
    >
        <div class="flex items-center justify-between p-4 border-b border-mocha-border bg-mocha-dark">
            <h2 class="text-xl font-bold tracking-wider text-mocha-accent">Admin Panel</h2>
            <button @click="closeSidebarOnMobile" class="text-mocha-light hover:text-mocha-accent md:hidden">
                <font-awesome-icon :icon="['fas', 'times']" class="text-xl" />
            </button>
        </div>

        <nav class="mt-5">
            <Link @click="closeSidebarOnMobile" href="/admin" class="nav-link">
                <font-awesome-icon :icon="['fas', 'chart-bar']" class="nav-icon" />
                Dashboard
            </Link>
            <Link @click="closeSidebarOnMobile" href="/admin/workers" class="nav-link">
                <font-awesome-icon :icon="['fas', 'user-check']" class="nav-icon" />
                Workers
            </Link>
            <Link @click="closeSidebarOnMobile" href="/admin/employers" class="nav-link">
                <font-awesome-icon :icon="['fas', 'building']" class="nav-icon" />
                Employers
            </Link>
            <Link @click="closeSidebarOnMobile" href="/admin/reported-users" class="nav-link">
                <font-awesome-icon :icon="['fas', 'exclamation-triangle']" class="nav-icon" />
                Reported Users
            </Link>
            <Link @click="closeSidebarOnMobile" href="/admin/job-approvals" class="nav-link">
                <font-awesome-icon :icon="['fas', 'clipboard-check']" class="nav-icon" />
                Job Approvals
            </Link>

            <!-- Payment Section with Dropdown -->
            <div>
                <button @click="togglePaymentDropdown" class="nav-link flex justify-between">
                    <div class="flex items-center">
                        <font-awesome-icon :icon="['fas', 'money-bill-wave']" class="nav-icon" />
                        Payment
                    </div>
                    <span class="text-mocha-light">{{ isPaymentOpen ? "▼" : "▶" }}</span>
                </button>
                <div v-if="isPaymentOpen" class="ml-6 border-l border-mocha-border transition-opacity duration-300 ease-in-out opacity-100">
                    <Link @click="closeSidebarOnMobile" href="/admin/payment-history" class="nav-link pl-5">
                        <font-awesome-icon :icon="['fas', 'history']" class="nav-icon" />
                        Payment History
                    </Link>
                    <Link @click="closeSidebarOnMobile" href="/admin/table-subscription" class="nav-link pl-5">
                        <font-awesome-icon :icon="['fas', 'user-tag']" class="nav-icon" />
                        Subscribed Users
                    </Link>
                </div>
            </div>
        </nav>
    </aside>
</template>

<style scoped>
/* Mocha-Themed Colors */
:root {
    --mocha-dark: #3E2723;
    --mocha-light: #D7CCC8;
    --mocha-accent: #A1887F;
    --mocha-border: #5D4037;
}

/* Sidebar Styling */
aside {
    font-family: 'Orbitron', sans-serif;
}

/* Sidebar Links */
.nav-link {
    display: flex;
    align-items: center;
    padding: 12px;
    color: var(--mocha-light);
    font-weight: 600;
    transition: all 0.3s ease-in-out;
    position: relative;
}

.nav-link:hover {
    background-color: rgba(255, 255, 255, 0.05);
    color: var(--mocha-accent);
    box-shadow: inset 3px 0px 0px var(--mocha-accent);
}

/* Sidebar Icons */
.nav-icon {
    margin-right: 12px;
    font-size: 1.2rem;
    color: var(--mocha-accent);
}

/* Dropdown Animation */
.ml-6 {
    transition: opacity 0.3s ease-in-out;
}

/* Smooth Sidebar Slide */
.transition-transform {
    transition: transform 0.5s ease-in-out;
}
</style>
