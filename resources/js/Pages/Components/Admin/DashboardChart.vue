<script setup>
import { ref, onMounted, watch, defineProps } from "vue";
import Chart from "chart.js/auto";

const props = defineProps({
    chartData: Object,  
    earningsData: Object,
    type: String,       
});

const chartCanvas = ref(null);
let chartInstance = null;

const createChart = () => {
    if (!chartCanvas.value) return;

    if (chartInstance) chartInstance.destroy();

    const isEarningsChart = props.type === "earnings";

    chartInstance = new Chart(chartCanvas.value, {
        type: isEarningsChart ? "line" : "bar",
        data: {
            labels: isEarningsChart
                ? props.earningsData.months 
                : ["Users", "Jobs", "Applications"],
            datasets: [
                {
                    label: isEarningsChart ? "Earnings Over Time" : "Count",
                    data: isEarningsChart
                        ? props.earningsData.earnings 
                        : [props.chartData.users, props.chartData.jobs, props.chartData.applications],
                    backgroundColor: isEarningsChart
                        ? "rgba(34, 197, 94, 0.2)"
                        : ["rgba(59, 130, 246, 0.8)", "rgba(239, 68, 68, 0.8)", "rgba(16, 185, 129, 0.8)"],
                    borderColor: isEarningsChart
                        ? "rgba(34, 197, 94, 1)"
                        : ["rgba(59, 130, 246, 1)", "rgba(239, 68, 68, 1)", "rgba(16, 185, 129, 1)"],
                    borderWidth: 2,
                    hoverBackgroundColor: isEarningsChart
                        ? "rgba(34, 197, 94, 0.8)"
                        : ["rgba(59, 130, 246, 1)", "rgba(239, 68, 68, 1)", "rgba(16, 185, 129, 1)"],
                    fill: isEarningsChart,
                    tension: 0.4,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: "rgba(255, 255, 255, 0.1)" },
                    ticks: { color: "#9ca3af" },
                },
                x: {
                    grid: { color: "rgba(255, 255, 255, 0.1)" },
                    ticks: { color: "#9ca3af" },
                },
            },
            plugins: {
                legend: {
                    labels: { color: "#9ca3af" },
                },
            },
            animation: {
                duration: 1000,
                easing: "easeInOutQuart",
            },
        },
    });
};

watch(() => props.chartData, createChart, { deep: true });
watch(() => props.earningsData, createChart, { deep: true });

onMounted(() => {
    setTimeout(createChart, 500);
});
</script>

<template>
    <div class="w-full">
        <div class="relative h-[100px] sm:h-[100px] md:h-[350px] w-full">
            <canvas ref="chartCanvas"></canvas>
        </div>
    </div>
</template>
