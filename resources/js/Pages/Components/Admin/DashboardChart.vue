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
                    backgroundColor: ["#3B82F6", "#EF4444", "#10B981"],
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } },
        },
    });
};

onMounted(createChart);
onBeforeUnmount(() => chartInstance?.destroy());
</script>

<template>
    <div class="mx-auto w-full max-w-4xl rounded-lg bg-white p-6 shadow">
        <h2 class="mb-4 text-center text-xl font-bold md:text-left">
            User & Job Statistics
        </h2>
        <div class="relative h-[300px] w-full">
            <canvas ref="chartCanvas"></canvas>
        </div>
    </div>
</template>
