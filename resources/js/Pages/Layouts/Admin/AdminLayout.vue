<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import Sidebar from "../../Components/Admin/Sidebar.vue";
import Navbar from "../../Components/Admin/Navbar.vue";
// import Footer from "../../Components/Admin/Footer.vue";

const isSidebarOpen = ref(window.innerWidth >= 768);
const isDesktop = ref(window.innerWidth >= 768);

const handleResize = () => {
    isDesktop.value = window.innerWidth >= 768;
    if (!isDesktop.value)
        isSidebarOpen.value = false; // Auto-close on small screens
    else isSidebarOpen.value = true; // Keep open on large screens
};

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

onMounted(() => window.addEventListener("resize", handleResize));
onUnmounted(() => window.removeEventListener("resize", handleResize));
</script>
<template>
    <div class="flex min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <Sidebar
            :isSidebarOpen="isSidebarOpen"
            @toggleSidebar="toggleSidebar"
        />

        <!-- Main Content -->
        <div
            class="flex flex-1 flex-col transition-all duration-300"
            :class="{
                'ml-64': isSidebarOpen && isDesktop,
                'ml-0': !isSidebarOpen || !isDesktop,
            }"
        >
            <!-- Navbar with Sidebar Toggle -->
            <Navbar
                @toggleSidebar="toggleSidebar"
                :isSidebarOpen="isSidebarOpen"
            />

            <!-- Content Area -->
            <main class="flex-grow p-6">
                <slot></slot>
            </main>

            <!-- Footer -->
            <Footer />
        </div>
    </div>
</template>
