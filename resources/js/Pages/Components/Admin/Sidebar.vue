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
            'fixed left-0 top-0 z-50 h-full w-64 bg-gray-900 text-white transition-transform duration-300 ease-in-out shadow-lg border-r border-gray-700',
            isSidebarOpen ? 'translate-x-0' : '-translate-x-full',
        ]"
    >
        <div class="flex items-center justify-between p-4 border-b border-gray-700 bg-white">
            <h2 class="text-xl font-bold tracking-wider text-neon">.....</h2>
            <button @click="closeSidebarOnMobile" class="text-gray-400 hover:text-neon md:hidden">
                <font-awesome-icon :icon="['fas', 'times']" class="text-xl" />
            </button>
        </div>

        <nav class="mt-5">
            <Link
                @click="closeSidebarOnMobile"
                href="/admin"
                class="nav-link"
            >
                <font-awesome-icon :icon="['fas', 'chart-bar']" class="nav-icon" />
                Dashboard
            </Link>
            <Link
                @click="closeSidebarOnMobile"
                href="/admin/workers"
                class="nav-link"
            >
                <font-awesome-icon :icon="['fas', 'user-check']" class="nav-icon" />
                Workers
            </Link>
            <Link
                @click="closeSidebarOnMobile"
                href="/admin/employers"
                class="nav-link"
            >
                <font-awesome-icon :icon="['fas', 'building']" class="nav-icon" />
                Employers
            </Link>
            <Link
                @click="closeSidebarOnMobile"
                href="/admin/reported-users"
                class="nav-link"
            >
                <font-awesome-icon :icon="['fas', 'exclamation-triangle']" class="nav-icon" />
                Reported Users
            </Link>
            <Link
                @click="closeSidebarOnMobile"
                href="/admin/job-approvals"
                class="nav-link"
            >
                <font-awesome-icon :icon="['fas', 'clipboard-check']" class="nav-icon" />
                Job Approvals
            </Link>

            <!-- Payment Section with Dropdown -->
            <div>
                <button
                    @click="togglePaymentDropdown"
                    class="nav-link flex justify-between"
                >
                    <div class="flex items-center">
                        <font-awesome-icon :icon="['fas', 'money-bill-wave']" class="nav-icon" />
                        Payment
                    </div>
                    <span class="text-gray-400">{{ isPaymentOpen ? "▼" : "▶" }}</span>
                </button>
                <div v-if="isPaymentOpen" class="ml-6 border-l border-gray-700">
                    <Link
                        @click="closeSidebarOnMobile"
                        href="/admin/payment-history"
                        class="nav-link pl-5"
                    >
                        <font-awesome-icon :icon="['fas', 'history']" class="nav-icon" />
                        Payment History
                    </Link>
                    <Link
                        @click="closeSidebarOnMobile"
                        href="/admin/table-subscription"
                        class="nav-link pl-5"
                    >
                        <font-awesome-icon :icon="['fas', 'user-tag']" class="nav-icon" />
                        Subscribed Users
                    </Link>
                </div>
            </div>
        </nav>
    </aside>
</template>

<style scoped>
/* Robotic Aesthetic */
aside {
    transition: transform 0.3s ease-in-out;
    font-family: 'Orbitron', sans-serif;
}

/* Sidebar Links */
.nav-link {
    display: flex;
    align-items: center;
    padding: 12px;
    color: #a0a0a0;
    font-weight: 600;
    transition: all 0.3s ease-in-out;
    position: relative;
}

.nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: #00ffcc;
    transform: translateX(5px);
}

/* Sidebar Icons */
.nav-icon {
    margin-right: 12px;
    font-size: 1.2rem;
    color: #00ffcc;
}

/* Neon Effect */
.text-neon {
    color: #00ffcc;
    text-shadow: 0px 0px 10px #00ffcc;
}
</style>
