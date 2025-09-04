<script setup>
import { ref, nextTick } from "vue";
import axios from "axios";

const props = defineProps({
    jobId: {
        type: Number,
        required: true,
    },
});

const jobData = ref(null);
const showCard = ref(false);
const loading = ref(false);
const cardStyle = ref({});
const triggerRef = ref(null);
let timeoutId = null;

async function fetchPreview() {
    if (jobData.value || loading.value) return;
    loading.value = true;
    try {
        const res = await axios.get(`/job/${props.jobId}/preview`);
        jobData.value = res.data;
    } catch (err) {
        console.error("Error fetching job preview:", err);
    } finally {
        loading.value = false;
    }
}

function handleMouseEnter() {
    showCard.value = true;
    fetchPreview();

    nextTick(() => {
        if (triggerRef.value) {
            const rect = triggerRef.value.getBoundingClientRect();
            const spaceBelow = window.innerHeight - rect.bottom;
            const spaceAbove = rect.top;
            const cardHeight = 220; // approximate hover card height
            const openTop = spaceBelow < cardHeight && spaceAbove > spaceBelow;

            // clamp left so it doesn’t overflow screen
            const left = Math.min(
                Math.max(rect.left + rect.width / 2 - 160, 8), // center by default
                window.innerWidth - 320 - 8,
            );

            cardStyle.value = {
                position: "fixed",
                top: openTop
                    ? rect.top - cardHeight - 8 + "px"
                    : rect.bottom + 8 + "px",
                left: left + "px",
            };
        }
    });
}

function handleMouseLeave() {
    timeoutId = setTimeout(() => {
        showCard.value = false;
    }, 200);
}

function cancelClose() {
    if (timeoutId) {
        clearTimeout(timeoutId);
        timeoutId = null;
    }
}
</script>

<template>
    <div
        ref="triggerRef"
        class="inline-block"
        @mouseenter="handleMouseEnter"
        @mouseleave="handleMouseLeave"
    >
        <!-- Hover Trigger -->
        <slot></slot>
    </div>

    <!-- Hover Card rendered at body level -->
    <Teleport to="body">
        <div
            v-if="showCard"
            :style="cardStyle"
            class="z-[9999] w-80 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl transition-all duration-200"
            @mouseenter="cancelClose"
            @mouseleave="handleMouseLeave"
        >
            <!-- Loading State -->
            <div v-if="loading" class="flex items-center justify-center p-6">
                <span class="text-sm text-gray-500">Loading...</span>
            </div>

            <!-- Job Data -->
            <div v-else-if="jobData">
                <!-- Header -->
                <div
                    class="bg-gradient-to-r from-orange-500 to-red-600 p-4 text-white"
                >
                    <h2 class="truncate text-lg font-semibold">
                        {{ jobData.title }}
                    </h2>
                    <span
                        class="mt-1 inline-block rounded-full bg-white/20 px-2 py-0.5 text-xs"
                    >
                        {{ jobData.job_type ?? "N/A" }}
                    </span>
                </div>

                <!-- Content -->
                <div class="space-y-3 p-4 text-sm text-gray-700">
                    <div class="flex items-center gap-2">
                        <span class="text-indigo-500">👥</span>
                        <span class="font-medium">Preferred Workers:</span>
                        <span class="truncate">{{
                            jobData.preferred_worker_types ?? "N/A"
                        }}</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-green-500">💸</span>
                        <span class="font-medium">Hourly Rate:</span>
                        <span>₱{{ jobData.hourly_rate ?? "N/A" }}</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-green-600">📊</span>
                        <span class="font-medium">Salary / Month:</span>
                        <span>₱{{ jobData.salary_per_month ?? "N/A" }}</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-blue-500">📩</span>
                        <span class="font-medium">Applications:</span>
                        <span>{{ jobData.applications_count }}</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-gray-500">🕒</span>
                        <span class="font-medium">Posted:</span>
                        <span>{{ jobData.created_at }}</span>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="p-6 text-center text-sm text-gray-500">
                No data available
            </div>
        </div>
    </Teleport>
</template>
