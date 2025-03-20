<script setup>
import { defineProps, ref, watch, onMounted } from "vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import VChart from "vue-echarts";
import * as echarts from "echarts";
import { use } from "echarts/core";
import { CanvasRenderer } from "echarts/renderers";
import { BarChart } from "echarts/charts";
import { GridComponent, TooltipComponent } from "echarts/components";

// Register ECharts components
use([CanvasRenderer, BarChart, GridComponent, TooltipComponent]);

// Define props
const props = defineProps({
    title: String, // The title of the card (e.g., "Total Reports")
    value: [String, Number], // The number or value to display
    icon: Object, // FontAwesome icon object (optional)
    chartData: Array, // Array of numbers for the chart
    labels: Array, // Labels for the chart
});

// Chart options
const chartOptions = ref({
    tooltip: {
        trigger: "axis",
    },
    grid: {
        left: "5%",
        right: "5%",
        bottom: "5%",
        top: "5%",
        containLabel: true,
    },
    xAxis: {
        type: "category",
        data: props.labels || [],
        axisLabel: { show: false },
        axisTick: { show: false },
        axisLine: { show: false },
    },
    yAxis: {
        type: "value",
        axisLabel: { show: false },
        axisTick: { show: false },
        axisLine: { show: false },
        splitLine: { show: false },
    },
    series: [
        {
            type: "bar",
            data: props.chartData || [],
            barWidth: "50%",
            itemStyle: { color: "#fa8334", borderRadius: [5, 5, 0, 0] },
        },
    ],
});

// Update chart when data changes
watch(() => props.chartData, (newData) => {
    chartOptions.value.series[0].data = newData;
});
</script>

<template>
    <div class="rounded-lg bg-white p-5 border-l border-r dark:bg-gray-800">
        <!-- Header -->
        <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-yellow-300 text-white">
                <font-awesome-icon :icon="icon" class="text-2xl" />
            </div>
            <div>
                <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ value }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-300">{{ title }}</p>
            </div>
        </div>

        <!-- Chart -->
        <div class="mt-3 h-16">
            <VChart :option="chartOptions" class="h-full w-full" />
        </div>
    </div>
</template>
