<script setup>
import { useForm } from "@inertiajs/vue3";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import DashboardChart from "../Components/Admin/DashboardChart.vue";

defineOptions({
    layout: AdminLayout,
});
</script>

<template>
    <Head title="Dashboard | iCan Careers" />
    <div class="p-6 bg-gray-900 min-h-screen">
        <h1 class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500 animate-fade-in">
            Dashboard
        </h1>

        <div class="mt-8 grid grid-cols-1 gap-8 md:grid-cols-3">
            <!-- Main Chart Section -->
            <div class="md:col-span-2 bg-gray-100 rounded-xl shadow-2xl p-6 border border-gray-700 hover:border-cyan-400 transition-all">
                <DashboardChart />
            </div>

            <!-- Stats Section -->
            <div class="grid grid-cols-1 gap-6">
                <!-- Total Users -->
                <div class="bg-gray-800 rounded-xl shadow-2xl p-6 border border-gray-700 hover:border-cyan-400 transition-all animate-fade-in delay-100">
                    <h2 class="text-lg font-medium text-gray-400">Total Users</h2>
                    <p class="text-3xl font-bold text-cyan-400 glow">50</p>
                </div>

                <!-- Total Jobs -->
                <div class="bg-gray-800 rounded-xl shadow-2xl p-6 border border-gray-700 hover:border-cyan-400 transition-all animate-fade-in delay-200">
                    <h2 class="text-lg font-medium text-gray-400">Total Jobs</h2>
                    <p class="text-3xl font-bold text-cyan-400 glow">20</p>
                </div>

                <!-- New Applications -->
                <div class="bg-gray-800 rounded-xl shadow-2xl p-6 border border-gray-700 hover:border-cyan-400 transition-all animate-fade-in delay-300">
                    <h2 class="text-lg font-medium text-gray-400">New Applications</h2>
                    <p class="text-3xl font-bold text-cyan-400 glow">10</p>
                </div>

                <!-- Total Earnings -->
                <div class="bg-gray-800 rounded-xl shadow-2xl p-6 border border-gray-700 hover:border-cyan-400 transition-all animate-fade-in delay-400">
                    <h2 class="text-lg font-medium text-gray-400">Total Earnings</h2>
                    <p class="text-3xl font-bold text-green-400 glow">₱100,000</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
/* Glow effect for futuristic numbers */
.glow {
    text-shadow: 0 0 8px rgba(59, 130, 246, 0.7), 0 0 15px rgba(59, 130, 246, 0.5);
}

/* Gradient border effect */
.hover\:border-cyan-400:hover {
    border-color: rgba(59, 130, 246, 0.5);
    box-shadow: 0 0 15px rgba(59, 130, 246, 0.3);
}

/* Background and text colors */
.bg-gray-900 {
    background-color: #111827;
}

.bg-gray-800 {
    background-color: #1f2937;
}

.text-gray-400 {
    color: #9ca3af;
}

.text-cyan-400 {
    color: #22d3ee;
}

.text-green-400 {
    color: #34d399;
}

/* Smooth transitions */
.transition-all {
    transition: all 0.3s ease-in-out;
}

/* Fade-in animation */
.animate-fade-in {
    animation: fadeIn 1s ease-in-out forwards;
    opacity: 0;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Delay classes for staggered animations */
.delay-100 {
    animation-delay: 0.1s;
}

.delay-200 {
    animation-delay: 0.2s;
}

.delay-300 {
    animation-delay: 0.3s;
}

.delay-400 {
    animation-delay: 0.4s;
}
</style>