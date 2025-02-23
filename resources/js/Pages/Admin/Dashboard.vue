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
    <div>
        <h1 class="text-2xl font-bold">Dashboard</h1>
        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
            <DashboardChart />
            <div class="rounded-lg bg-white p-5 shadow">Total Users: 50</div>
            <div class="rounded-lg bg-white p-5 shadow">Total Jobs: 20</div>
            <div class="rounded-lg bg-white p-5 shadow">
                New Applications: 10
            </div>
        </div>
    </div>
</template>
