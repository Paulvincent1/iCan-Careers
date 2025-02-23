<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { Link } from "@inertiajs/vue3";

// Props
defineProps({
    isSidebarOpen: Boolean,
});

// State for dropdown visibility
const isDropdownOpen = ref(false);

// Function to toggle dropdown
const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};

// Function to close dropdown when clicking outside
const closeDropdown = (event) => {
    const dropdown = document.getElementById("profile-dropdown");
    if (dropdown && !dropdown.contains(event.target)) {
        isDropdownOpen.value = false;
    }
};

// Add and remove event listeners
onMounted(() => {
    document.addEventListener("click", closeDropdown);
});
onUnmounted(() => {
    document.removeEventListener("click", closeDropdown);
});
</script>

<template>
    <!-- Sticky Navbar -->
    <nav class="fixed left-0 top-0 z-50 flex w-full items-center bg-white p-4 shadow">
        <!-- Sidebar Toggle Button (Mobile) -->
        <button @click="$emit('toggleSidebar')" class="text-gray-500 focus:outline-none md:hidden">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>

        <!-- Logo -->
        <Link href="/admin" class="flex-1 flex w-full justify-center md:justify-start">
            <img src="/assets/iCanCareersLogofinal.png" alt="Logo" class="h-10" />
        </Link>

        <!-- Profile Dropdown (Top Right) -->
        <div class="relative ml-auto" id="profile-dropdown">
            <!-- Profile Icon Button -->
            <button @click="toggleDropdown" class="flex items-center space-x-2 focus:outline-none">
                <img src="/assets/profile_placeholder.jpg" alt="Profile" class="h-10 w-10 rounded-full border border-gray-300" />
            </button>

            <!-- Dropdown Menu -->
            <div v-if="isDropdownOpen" class="absolute right-0 z-50 mt-2 w-48 rounded-lg border bg-white shadow-lg">
                <a href="/admin" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
                <a href="/admin" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Content Padding to Prevent Overlapping -->
    <div class="pt-16"></div>
</template>
