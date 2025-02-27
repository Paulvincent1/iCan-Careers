<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import Chart from "chart.js/auto";

const chartCanvas = ref(null);
let chartInstance = null;

const createChart = () => {
    if (chartInstance) chartInstance.destroy();

    chartInstance = new Chart(chartCanvas.value, {
        type: "bar",
        data: {
            labels: ["Users", "Jobs", "Applications"],
            datasets: [
                {
                    label: "Count",
                    data: [50, 20, 10], // Replace with dynamic data
                    backgroundColor: [
                        "rgba(59, 130, 246, 0.8)", // Blue
                        "rgba(239, 68, 68, 0.8)", // Red
                        "rgba(16, 185, 129, 0.8)", // Green
                    ],
                    borderColor: [
                        "rgba(59, 130, 246, 1)", // Blue
                        "rgba(239, 68, 68, 1)", // Red
                        "rgba(16, 185, 129, 1)", // Green
                    ],
                    borderWidth: 2,
                    hoverBackgroundColor: [
                        "rgba(59, 130, 246, 1)", // Blue
                        "rgba(239, 68, 68, 1)", // Red
                        "rgba(16, 185, 129, 1)", // Green
                    ],
                    hoverBorderColor: "#fff",
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
                duration: 1000, // Animation duration
                easing: "easeInOutQuart", // Smooth easing
            },
        },
    });
};

onMounted(() => {
    // Delay chart creation to ensure the container is fully visible
    setTimeout(createChart, 500); // Adjust delay as needed
});

onBeforeUnmount(() => chartInstance?.destroy());
</script>

<template>
    <div class="w-full">
        <div class="relative h-[300px] w-full">
            <canvas ref="chartCanvas"></canvas>
        </div>
    </div>
</template>

<style scoped>
/* Glow effect for the chart bars */
canvas {
    filter: drop-shadow(0 0 8px rgba(59, 130, 246, 0.5))
            drop-shadow(0 0 15px rgba(59, 130, 246, 0.3));
}
</style>