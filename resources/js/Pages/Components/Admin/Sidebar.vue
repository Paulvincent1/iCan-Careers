<script setup>
import { Link } from "@inertiajs/vue3";
import { ref, onMounted, onUnmounted,computed } from "vue";
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
    faChevronDown,
    faChevronUp,
    faUserSlash,
    faFileCircleExclamation,
    faSun,
    faMoon,
    faUserTie
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
    faChevronDown,
    faChevronUp,
    faUserSlash,
    faFileCircleExclamation,
    faSun,
    faMoon,
    faUserTie
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

// Payments Dropdown
const isPaymentOpen = ref(false);
const togglePaymentDropdown = () => {
    isPaymentOpen.value = !isPaymentOpen.value;
};

// Reports Dropdown
const isReportOpen = ref(false);
const toggleReportDropdown = () => {
    isReportOpen.value = !isReportOpen.value;
};


// Close sidebar on mobile after clicking a link or X button
const closeSidebarOnMobile = () => {
    if (isMobile.value) {
        emit("toggleSidebar");
    }
};

const darkMode = ref(localStorage.getItem("theme") === "dark");

// Apply theme on mount
onMounted(() => {
    darkMode.value = localStorage.getItem("theme") === "dark";
    document.documentElement.classList.toggle("dark", darkMode.value);
});

// Toggle dark mode
const toggleDarkMode = () => {
    darkMode.value = !darkMode.value;
    localStorage.setItem("theme", darkMode.value ? "dark" : "light");
    document.documentElement.classList.toggle("dark", darkMode.value);

    // Emit event for other components
    window.dispatchEvent(new Event("theme-changed"));
};
// Compute the correct image source based on dark mode
const logoSrc = computed(() =>
    darkMode.value ? "/assets/iCanLogoLight.png" : "/assets/iCanLogoDark.png"
);

const textSrc = computed(() =>
    darkMode.value ? 'text-white' : 'text-gray-900'
);
</script>

<template>
            <div
            :class="[
            'fixed left-0 top-0 z-50 h-full w-64 transform  transition-all duration-500 ease-in-out',
            isSidebarOpen ? 'translate-x-0' : '-translate-x-full',
            darkMode ? 'bg-gray-800' : 'bg-white'
        ]"
        >
        <!-- Sidebar Header with Logo & Close Button -->
            <div :class="['flex items-center justify-center p-1', darkMode ? '' : '']">
                <!-- Logo (Hidden on Mobile, Centered on Larger Screens) -->
                 <div :class="['w-full flex justify-center p-1 m-1', darkMode ? '' : 'bg-white' ]">
                     <Link href="/admin">
                 <img :src="logoSrc" alt="Logo" class="h-14" />
                     </Link>
                 </div>
            </div>

            <!-- X Button (Only for Mobile, Positioned in Right Corner) -->
            <button
                v-if="isMobile"
                @click="closeSidebarOnMobile"
                class="absolute right-3 top-3 text-gray-500 hover:text-gray-700"
            >
                <font-awesome-icon :icon="['fas', 'times']" :class="'text-2xl',darkMode ?'text-white':'text-black' " />
            </button>


       <div>
            <nav class=" p-5 space-y-0">
            <Link @click="closeSidebarOnMobile" href="/admin" class="nav-link">
                <font-awesome-icon :icon="['fas', 'chart-bar']" class="nav-icon" />
                <p :class="textSrc">Dashboard</p>
            </Link>

            <Link @click="closeSidebarOnMobile" href="/admin/workers" class="nav-link">
                <font-awesome-icon :icon="['fas', 'user-check']" class="nav-icon" />
                <p :class="textSrc">Workers</p>
            </Link>
            <Link @click="closeSidebarOnMobile" href="/admin/employers" class="nav-link">
                <font-awesome-icon :icon="['fas', 'user-tie']" class="nav-icon" />
                <p :class="textSrc">Employers</p>
            </Link>

            <Link @click="closeSidebarOnMobile" href="/admin/job-approvals" class="nav-link">
                <font-awesome-icon :icon="['fas', 'clipboard-check']" class="nav-icon" />
                <p :class="textSrc">Job Approvals</p>
            </Link>

            <!-- Reported User-->
            <div>
                <button @click="toggleReportDropdown" class="nav-link flex justify-between w-full">
                     <div class="flex items-center">
                     <font-awesome-icon :icon="['fas', 'exclamation-triangle']" class="nav-icon" />
                      <p :class="textSrc">Reports</p>
                        </div>
                        <font-awesome-icon
                    :icon="isReportOpen ? ['fas', 'chevron-up'] : ['fas', 'chevron-down']"
                    :class="['transition-transform duration-300', darkMode ? 'text-white' : 'text-black']"
                    />
                </button>

                <div v-if="isReportOpen" class="border-l ml-6 transition-all duration-300 ease-in-out">
                    <Link @click="closeSidebarOnMobile" href="/admin/reported-users" class="nav-link pl-5">
                        <font-awesome-icon :icon="['fas', 'user-slash']" class="nav-icon" />
                        <p :class="textSrc">Reported Users</p>
                    </Link>
                    <Link @click="closeSidebarOnMobile" href="/admin/reported-posts" class="nav-link pl-5">
                        <font-awesome-icon :icon="['fas', 'file-circle-exclamation']" class="nav-icon" />
                        <p :class="textSrc">Reported Posts</p>
                    </Link>
                </div>
            </div>

            <!-- Payment Section with Dropdown -->
            <div>
                <button @click="togglePaymentDropdown" class="nav-link flex justify-between w-full">
                    <div class="flex items-center">
                        <font-awesome-icon :icon="['fas', 'money-bill-wave']" class="nav-icon" />
                        <p :class="textSrc">Payments</p>
                    </div>
                    <font-awesome-icon
                    :icon="isPaymentOpen ? ['fas', 'chevron-up'] : ['fas', 'chevron-down']"
                    :class="['transition-transform duration-300', darkMode ? 'text-white' : 'text-black']"
                    />
                </button>
                <div v-if="isPaymentOpen" class="border-l ml-6 transition-all duration-300 ease-in-out">
                    <Link @click="closeSidebarOnMobile" href="/admin/payment-history" class="nav-link pl-5">
                        <font-awesome-icon :icon="['fas', 'history']" class="nav-icon" />
                        <p :class="textSrc">Payment History</p>
                    </Link>
                    <Link @click="closeSidebarOnMobile" href="/admin/table-subscription" class="nav-link pl-5">
                        <font-awesome-icon :icon="['fas', 'user-tag']" class="nav-icon" />
                        <p :class="textSrc">Subscribed Users</p>
                    </Link>
                </div>




            </div>

            <!-- Dark Mode Toggle -->
            <div class="absolute bottom-5 right-5 left-10">
                <button
                    @click="toggleDarkMode"
                    :class="['flex items-center gap-2 text-white p-2 rounded-md', darkMode ? 'bg-white' : 'bg-black']"
                >
                    <font-awesome-icon :icon="darkMode ? ['fas', 'moon'] : ['fas', 'sun']" :class="[ darkMode ? 'text-black' : 'text-white']"/>
                    <span :class="darkMode ? 'text-black' : 'text-white'">{{ darkMode ? 'Dark Mode' : 'Light Mode' }}</span>

                    <!-- Toggle Switch -->
                    <div
                        class="relative w-10 h-5 flex items-center bg-blue rounded-full p-1 transition duration-300"
                        :class="darkMode ? 'bg-blue-500' : 'bg-gray-400'"
                    >
                        <div
                            class="w-4 h-4 bg-white rounded-full shadow-md transform transition duration-300"
                            :class="darkMode ? 'translate-x-5' : 'translate-x-0'"
                        ></div>
                    </div>
                </button>
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
    font-weight: 600;

    border-radius: 8px;
    position: relative;
    color: #333;
}

.dark .nav-link {
    color: white;
}

/* Hover Effect */
.nav-link:hover {
    background-color: rgba(0, 123, 255, 0.1);
    border-left: 3px solid #fa8334;
}

/* Icon Styling */
.nav-icon {
    margin-right: 20px;
    font-size: 1.2rem;
    color: #fa8334;
}

/* Dark mode styles */
.dark {
    background-color: #1a1a1a;
    color: white;
}

.dark .nav-link:hover {
    background-color: rgba(250, 131, 52, 0.2);
}
</style>


