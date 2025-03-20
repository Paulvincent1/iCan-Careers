<script setup>
import { ref, defineProps, watch, computed } from "vue";
import VueApexCharts from "vue3-apexcharts";
import { use } from "echarts/core";
import * as echarts from "echarts";
import { CanvasRenderer } from "echarts/renderers";
import { BarChart } from "echarts/charts";
import { GridComponent, TooltipComponent } from "echarts/components";
import VChart from "vue-echarts";

use([CanvasRenderer, BarChart, GridComponent, TooltipComponent]);

const props = defineProps({
    chartData: Object,
    earningsData: Object,
    type: String,
});

const isEarningsChart = computed(() => props.type === "earnings");

const earningsChartOptions = ref({
    chart: { type: "line", height: 350 },
    stroke: { curve: "smooth" },
    colors: ["#34D399"],
    xaxis: {
        categories: props.earningsData?.days || [], // FIXED: Use `days`
        title: { text: "Days" },
    },
    title: { text: "Daily Earnings Over Time", align: "center" },
});

const earningsSeries = ref([
    { name: "Earnings", data: props.earningsData?.earnings || [] },
]);

const statsChartOptions = ref({
    tooltip: {
        trigger: "axis",
        backgroundColor: "rgba(0, 0, 0, 0.7)",
        textStyle: { color: "#fff" },
        borderRadius: 8,
        padding: 10,
    },
    grid: {
        left: "5%",
        right: "5%",
        bottom: "5%",
        containLabel: true,
    },
    xAxis: {
        type: "category",
        data: ["Users", "Jobs", "Applications"],
        axisLine: { lineStyle: { color: "#ddd" } },
        axisLabel: { fontSize: 14, color: "#333" },
    },
    yAxis: {
        type: "value",
        axisLine: { show: false },
        splitLine: { lineStyle: { color: "#eee" } },
    },
    series: [
        {
            name: "Total Count",
            data: [
                props.chartData?.users || 0,
                props.chartData?.jobs || 0,
                props.chartData?.applications || 0,
            ],
            type: "bar",
            barWidth: "50%",
            itemStyle: {
                color: (params) => {
                    const gradients = [
                        new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: "red" },
                            { offset: 1, color: "orange" },
                        ]),
                        new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: "#00308F" },
                            { offset: 1, color: "#007FFF" },
                        ]),
                        new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: "#10B981" },
                            { offset: 1, color: "teal" },
                        ]),
                    ];
                    return gradients[params.dataIndex];
                },
                borderRadius: [8, 8, 0, 0],
                shadowColor: "rgba(0,0,0,0.2)",
                shadowBlur: 5,
            },
            animationEasing: "elasticOut",
            animationDelay: (idx) => idx * 100,
        },
    ],
});

watch(
    () => props.earningsData,
    (newData) => {
        earningsSeries.value = [
            { name: "Earnings", data: newData?.earnings || [] },
        ];
        earningsChartOptions.value.xaxis.categories = newData?.days || []; // FIXED
    },
    { deep: true }
);

watch(
    () => props.chartData,
    (newData) => {
        statsChartOptions.value.series[0].data = [
            newData?.users || 0,
            newData?.jobs || 0,
            newData?.applications || 0,
        ];
    },
    { deep: true }
);
</script>

<template>
    <div class="w-full">
        <!-- Earnings Chart -->
        <div v-if="isEarningsChart" class="relative h-[350px] w-full">
            <VueApexCharts
                :options="earningsChartOptions"
                :series="earningsSeries"
                height="350"
            />
        </div>

        <!-- General Stats Chart (Improved) -->
        <div v-else class="relative h-[350px] w-full">
            <VChart class="h-64 w-full" :option="statsChartOptions" />
        </div>
    </div>
</template>
