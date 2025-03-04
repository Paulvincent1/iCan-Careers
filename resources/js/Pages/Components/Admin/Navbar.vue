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
const toggleDropdown = (event) => {
    event.stopPropagation(); // Prevents closing dropdown when clicking inside
    isDropdownOpen.value = !isDropdownOpen.value;
};

// Function to close dropdown when clicking outside
const closeDropdown = (event) => {
    if (!event.target.closest("#profile-dropdown")) {
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
    <!-- Navbar Wrapper -->
    <div
        class="fixed top-0 z-50 w-full border-b border-gray-300/20 bg-white shadow-lg"
    >
        <nav
            class="mx-auto flex max-w-screen-xl items-center justify-between p-3"
        >
            <!-- Sidebar Toggle Button (Mobile) -->
            <button
                @click="$emit('toggleSidebar')"
                class="text-cyan-300 focus:outline-none md:hidden"
            >
                <svg
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 12h16m-7 6h7"
                    ></path>
                </svg>
            </button>

            <!-- Logo -->
            <div class="flex flex-1 justify-center md:justify-start">
                <Link href="/admin">
                    <img
                        src="/assets/iCanCareersLogofinal.png"
                        alt="Logo"
                        class="h-10 md:hidden lg:hidden"
                    />
                </Link>
            </div>

            <!-- Profile Dropdown (Fixed on Right & Prevents Overflow) -->
            <div class="relative ml-auto" id="profile-menu">
                <button
                    @click="toggleDropdown"
                    class="flex items-center space-x-2 focus:outline-none"
                >
                    <img
                        src="/assets/profile_placeholder.jpg"
                        alt="Profile"
                        class="h-10 w-10 rounded-full border"
                    />
                </button>

                <!-- Dropdown Menu (Fixed Position & Responsive) -->
                <div
                    v-if="isDropdownOpen"
                    id="profile-dropdown"
                    class="absolute right-0 mt-2 w-48 rounded-lg border border-gray-300/20 bg-white shadow-lg"
                >
                    <Link
                        :href="route('admin.dashboard')"
                        class="block px-4 py-2 text-black hover:bg-gray-100"
                        >Profile</Link
                    >
                    <Link
                        :href="route('logout')"
                        method="post"
                        class="block px-4 py-2 text-black hover:bg-gray-100"
                        >Logout</Link
                    >
                </div>
            </div>
        </nav>
    </div>

    <!-- Content Padding to Prevent Overlapping -->
    <div class="pt-16"></div>
</template>
