<script setup>
import { ref, watch, onMounted } from "vue";
import VueApexCharts from "vue3-apexcharts";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    title: String,
    data: Array, // Array of { name: "Day", value: number }
    icon: Array,
    link: String, // ✅ Added link prop
    linkText: String,
});

const chartOptions = ref({
    chart: {
        type: "line",
        height: 160,
        toolbar: { show: false },
    },
    xaxis: {
        categories: [],
        labels: { style: { colors: "#4B5563" } }, // Dark gray for readability
    },
    yaxis: {
        labels: { style: { colors: "#4B5563" } },
    },
    stroke: {
        curve: "smooth",
        width: 2,
    },
    markers: {
        size: 4,
        colors: ["#2563eb"], // Blue
        strokeWidth: 2,
    },
    colors: ["#2563eb"],
    grid: { borderColor: "#E5E7EB" }, // Light gray
});

const chartSeries = ref([
    {
        name: "Value",
        data: [],
    },
]);

const updateChartData = () => {
    chartOptions.value.xaxis.categories = props.data.map((d) => d.name);
    chartSeries.value[0].data = props.data.map((d) => d.value);
};

onMounted(updateChartData);
watch(() => props.data, updateChartData, { deep: true });
</script>

<template>
    <div class="rounded-lg border-r bg-white p-6">
        <div class="flex items-center justify-between text-center">
            <div>
                <h2 class="text-md font-semibold text-gray-700">{{ title }}</h2>

                <p class="text-3xl font-bold text-gray-900">
                    {{ data.length ? data[data.length - 1].value : 0 }}
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <!-- Link placed beside the title and count -->

                <font-awesome-icon
                    :icon="icon"
                    class="text-5xl text-gray-600"
                />
            </div>
        </div>
        <VueApexCharts
            type="line"
            height="160"
            :options="chartOptions"
            :series="chartSeries"
            class="mt-4"
        />
        <div class="flex justify-end">
            <Link
                v-if="link"
                :href="link"
                class="text-sm text-blue-600 hover:underline"
            >
                {{ linkText ?? "View More" }}
            </Link>
        </div>
    </div>
</template>
