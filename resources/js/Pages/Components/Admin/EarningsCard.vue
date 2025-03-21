<script setup>
import { defineProps, computed } from "vue";
import VChart from "vue-echarts";
import { use } from "echarts/core";
import { PieChart } from "echarts/charts";
import { CanvasRenderer } from "echarts/renderers";
import { LegendComponent, TooltipComponent } from "echarts/components";

// Register ECharts modules
use([CanvasRenderer, PieChart, LegendComponent, TooltipComponent]);

// Props for dynamic data
const props = defineProps({
    title: String,
    data: Array, // Example: [{ name: "Mar 19", value: 1048 }, { name: "Mar 20", value: 735 }]
});

// Computed property for chart options
const chartOptions = computed(() => ({
    tooltip: {
        trigger: "item",
    },
    legend: {
        top: "5%",
        left: "center",
    },
    series: [
        {
            name: "Employers",
            type: "pie",
            radius: "50%",
            data: props.data,
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: "rgba(0, 0, 0, 0.5)",
                },
            },
        },
    ],
}));
</script>

<template>
    <div class="rounded-lg bg-white border-r p-4">
        <h2 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white">
            {{ title }}
        </h2>
        <VChart :option="chartOptions" style="width: 100%; height: 300px" />
    </div>
</template>
