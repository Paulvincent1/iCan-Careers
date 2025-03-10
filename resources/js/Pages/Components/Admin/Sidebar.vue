<script setup>
import { Link } from "@inertiajs/vue3";
import { ref, onMounted, onUnmounted } from "vue";
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

library.add(
    faChartBar,
    faUserCheck,
    faBuilding,
    faExclamationTriangle,
    faClipboardCheck,
    faMoneyBillWave,
    faHistory,
    faUserTag,
    faTimes,
);

const isMobile = ref(window.innerWidth < 768);

const updateScreenSize = () => {
    isMobile.value = window.innerWidth < 768;
};

onMounted(() => {
    window.addEventListener("resize", updateScreenSize);
});

onUnmounted(() => {
    window.removeEventListener("resize", updateScreenSize);
});

defineProps({
    isSidebarOpen: Boolean,
});

const emit = defineEmits(["toggleSidebar"]);

// Payment Dropdown
const isPaymentOpen = ref(false);
const togglePaymentDropdown = () => {
    isPaymentOpen.value = !isPaymentOpen.value;
};

// Close sidebar on mobile after clicking a link or X button
const closeSidebarOnMobile = () => {
    if (isMobile.value) {
        emit("toggleSidebar");
    }
};
</script>

<template>
    <div
        :class="[
            'fixed left-0 top-0 z-50 h-full w-64 transform border-r bg-white transition-all duration-500 ease-in-out',
            isSidebarOpen ? 'translate-x-0' : '-translate-x-full'
        ]"
    >
        <!-- Sidebar Header with Logo & Close Button -->
            <div v-if="!isMobile" class="flex items-center justify-center p-1 border-b">
                <!-- Logo (Hidden on Mobile, Centered on Larger Screens) -->
                 <div class="w-full flex justify-center">
                     <Link href="/admin">
                 <img src="/assets/iCanCareersincognito.png" alt="Logo" class="h-14" />
                     </Link>
                 </div>
</div>

<!-- X Button (Only for Mobile, Positioned in Right Corner) -->
<button
    v-if="isMobile"
    @click="closeSidebarOnMobile"
    class="absolute right-3 top-3 text-gray-500 hover:text-gray-700"
>
    <font-awesome-icon :icon="['fas', 'times']" class="text-5xl" />
</button>

       <div>
            <nav class=" p-5 space-y-0">
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
                    <span class="transition-transform duration-300" :class="{ 'rotate-90': isPaymentOpen }">▶</span>
                </button>
                <div v-if="isPaymentOpen" class="border-l ml-6 transition-all duration-300 ease-in-out">
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
       </div>
    </div>
</template>

<style scoped>
/* Sidebar Links */
.nav-link {
    display: flex;
    align-items: center;
    padding: 12px;
    color: #333;
    font-weight: 600;
    transition: all 0.3s ease-in-out;
    border-radius: 8px;
    position: relative;
}

/* Hover Effect */
.nav-link:hover {
    background-color: rgba(0, 123, 255, 0.1);
    color: #fa8334;
    border-left: 3px solid #fa8334;
    box-shadow: none;
    transform: scale(1.05);
}

/* Icon Styling */
.nav-icon {
    margin-right: 20px;
    font-size: 1.2rem;
    color: #fa8334;
}

/* Rotate dropdown arrow */
.rotate-90 {
    transform: rotate(90deg);
}


</style>
