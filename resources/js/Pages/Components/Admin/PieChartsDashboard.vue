<script setup>
import { ref, watch, onMounted } from "vue";
import VueApexCharts from "vue3-apexcharts";

const props = defineProps({
    title: String,
    data: Array, // Array of { name: "Category", value: number }
    icon: Array,
    link: String, // ✅ Added link prop
    linkText: String,
});

const chartOptions = ref({
    chart: {
        type: "pie",
        height: 200,
    },
    labels: [],
    legend: {
        position: "bottom",
        labels: { colors: "#374151" },
    },
    colors: ["#2563eb", "#f59e0b", "#10b981", "#ef4444", "#8b5cf6"], // Custom colors
    tooltip: {
        theme: "light",
    },
});

const chartSeries = ref([]);

const updateChartData = () => {
    chartOptions.value.labels = props.data.map((d) => d.name);
    chartSeries.value = props.data.map((d) => d.value);
};

onMounted(updateChartData);
watch(() => props.data, updateChartData, { deep: true });
</script>

<template>
    <div class="rounded-lg border-r bg-white p-6 ">
        <div class="flex items-center justify-between text-center">
            <div>
                <h2 class="text-md font-semibold text-gray-700">{{ title }}</h2>
            
            </div>
            <div class="flex items-center space-x-3">
                <font-awesome-icon
                    :icon="icon"
                    class="text-5xl text-gray-600"
                />
            </div>
        </div>
        <VueApexCharts
            type="pie"
            height="200"
            :options="chartOptions"
            :series="chartSeries"
            class="mt-4"
        />
        <!-- Link placed beside the title and count -->
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
