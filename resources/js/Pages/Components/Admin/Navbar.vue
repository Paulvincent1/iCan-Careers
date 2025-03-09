<script setup>
import { ref, onMounted, onUnmounted, useTemplateRef, watch } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

import { nanoid } from "nanoid";

// Props
defineProps({
    isSidebarOpen: Boolean,
});

// State for dropdowns
const isDropdownOpen = ref(false);
const showNotificationDropDown = ref(false);
const profileDropdown = ref(false);

// Template refs
const dropdownButton = useTemplateRef("drop");
const notificationButton = useTemplateRef("dropNotification");

// Page data
let page = usePage();
let notifications = ref(page.props.auth?.user.unreadNotifications);

// Function to toggle profile dropdown
const toggleDropdown = (event) => {
    event.stopPropagation();
    isDropdownOpen.value = !isDropdownOpen.value;
};

// Function to close dropdown when clicking outside
const closeDropdown = (event) => {
    if (!dropdownButton.value.contains(event.target)) {
        profileDropdown.value = false;
    }
    if (!notificationButton.value.contains(event.target)) {
        showNotificationDropDown.value = false;
    }
};

// Add and remove event listeners
onMounted(() => {
    document.addEventListener("click", closeDropdown);

    window.Echo.channel("notification-" + page.props.auth.user.authenticated?.id)
        .listen(".notification.event", (notif) => {
            notifications.value.unshift({
                id: nanoid(),
                data: {
                    status: notif.status,
                    message: notif.message,
                },
            });
        });
});

onUnmounted(() => {
    document.removeEventListener("click", closeDropdown);
});
</script>

<template>
    <!-- Navbar Wrapper -->
    <div class="relative sticky fixed top-0 left-0 border-gray-300/20 bg-white">
        <nav class="mx-auto flex max-w-screen-xl items-center justify-between p-3">
            <!-- Sidebar Toggle Button (Mobile) -->
            <div class="flex flex-start lg:hidden md:hidden">
                <button @click="$emit('toggleSidebar')" class="text-cyan-300 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
            </div>

            <!-- Logo -->
            <div class="flex flex-1 justify-center md:justify-start">
                <Link href="/admin">
                    <img src="/assets/iCanCareersLogofinal.png" alt="Logo" class="h-10 md:hidden lg:hidden" />
                </Link>
            </div>

            <!-- Profile & Notifications -->
            <div class="ml-auto flex items-center gap-3">

                    <!-- Notification Bell -->
                    <div ref="dropNotification" class="relative">
                        <button @click="showNotificationDropDown = !showNotificationDropDown" class="relative focus:outline-none">
                            <i class="bi bi-bell text-lg"></i>

                            <!-- Notification Count Badge (Shows only when there are notifications) -->
                            <span
                                v-if="true"
                                class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center"
                            >
                                {{ notifications.length > 99 ? '99+' : notifications.length }}
                            </span>

                        </button>

                        <!-- Notification Dropdown -->
                        <div v-if="showNotificationDropDown" class="absolute right-0 mt-2 w-80 rounded-lg border bg-white shadow-lg p-3">
                            <p class="text-xl font-bold">Notifications</p>
                            <p class="font-bold underline underline-offset-8">All</p>
                            <div v-if="notifications.length">
                                <div v-for="notification in notifications" :key="notification.id" class="mb-3 flex items-start gap-3">
                                    <img class="h-8 w-8 rounded-full" src="/assets/images.png" alt="User" />
                                    <div>
                                        <p class="font-bold">{{ notification.data.status }}</p>
                                        <p class="text-xs">{{ notification.data.message }}</p>
                                    </div>
                                </div>
                            </div>
                            <p v-else class="text-center">No Notifications</p>
                        </div>
                    </div>


                <!-- Profile Dropdown -->
                <div ref="drop" class="relative">
                    <button @click="profileDropdown = !profileDropdown" class="flex items-center space-x-2 focus:outline-none">
                        <img :src="page.props.auth.user?.profile_img || '/assets/profile_placeholder.jpg'" alt="Profile" class="h-10 w-10 rounded-full border" />
                        <p class="text-sm">Me</p>
                        <i class="bi bi-chevron-down text-sm"></i>
                    </button>

                    <!-- Profile Dropdown Menu -->
                    <div v-if="profileDropdown" class="absolute right-0 mt-2 w-40 rounded-lg border bg-white shadow-lg">
                        <Link :href="route('admin.dashboard')" class="block px-4 py-2 text-black hover:bg-gray-100" @click="profileDropdown = false">
                            <i class="bi bi-person"></i> Dashboard
                        </Link>
                        <Link :href="route('logout')" method="post" as="button" class="block px-4 py-2 text-black hover:bg-gray-100" @click="profileDropdown = false">
                            <i class="bi bi-box-arrow-left"></i> Logout
                        </Link>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</template>
