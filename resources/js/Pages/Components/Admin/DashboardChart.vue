<script setup>
import { ref, defineProps, watch, computed } from "vue";
import VueApexCharts from "vue3-apexcharts";

const props = defineProps({
    earningsData: Object,
});

const earningsChartOptions = ref({
    chart: { type: "line", height: 350, toolbar: { show: false } },
    stroke: { curve: "smooth", width: 3 },
    colors: ["#34D399"],
    dataLabels: { enabled: true, style: { fontSize: "12px", colors: ["#333"] } },
    grid: {
        borderColor: "#e5e7eb",
        strokeDashArray: 5,
        xaxis: { lines: { show: true } },
        yaxis: { lines: { show: true } },
    },
    xaxis: {
        categories: props.earningsData?.days || [],
        title: { text: "Days", style: { fontSize: "14px", fontWeight: "bold" } },
        labels: { style: { colors: "#666", fontSize: "12px" } },
    },
    yaxis: {
        title: { text: "Earnings ($)", style: { fontSize: "14px", fontWeight: "bold" } },
        labels: { style: { colors: "#666", fontSize: "12px" } },
    },
    tooltip: {
        theme: "dark",
        x: { format: "dd/MM" },
    },
    title: { text: "Daily Earnings Over Time", align: "center", style: { fontSize: "16px" } },
});

const earningsSeries = ref([
    { name: "Earnings", data: props.earningsData?.earnings || [] },
]);

watch(
    () => props.earningsData,
    (newData) => {
        earningsSeries.value = [{ name: "Earnings", data: newData?.earnings || [] }];
        earningsChartOptions.value.xaxis.categories = newData?.days || [];
    },
    { deep: true }
);
</script>

<template>
    <div class="relative h-[350px] w-full">
        <VueApexCharts :options="earningsChartOptions" :series="earningsSeries" height="350" />
    </div>
</template>
