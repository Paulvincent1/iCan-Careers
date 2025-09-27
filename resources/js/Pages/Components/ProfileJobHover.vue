<script setup>
import { ref, onMounted, nextTick } from "vue";
import axios from "axios";

const props = defineProps({
    jobId: {
        type: Number,
        required: true,
    },
    businessId: {
        type: Number,
        required: true,
    },
});

const businessLogo = ref("/assets/logo-placeholder-image.png");
const jobData = ref(null);
const showCard = ref(false);
const loading = ref(false);
const cardStyle = ref({});
const triggerRef = ref(null);
let timeoutId = null;

async function fetchBusinessLogo() {
    try {
        const res = await axios.get(`/business/${props.businessId}/logo`);
        businessLogo.value = res.data.business_logo;
    } catch (err) {
        console.error("Error fetching business logo:", err);
    }
}
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
onMounted(fetchBusinessLogo);

function handleMouseEnter() {
    showCard.value = true;
    fetchPreview();

    nextTick(() => {
        if (triggerRef.value) {
            const rect = triggerRef.value.getBoundingClientRect();
            const spaceBelow = window.innerHeight - rect.bottom;
            const spaceAbove = rect.top;
            const cardEl = document.querySelector(".hover-card-job");
            const cardHeight = cardEl ? cardEl.offsetHeight : 220;
            const openTop = spaceBelow < cardHeight && spaceAbove > spaceBelow;

            const left = Math.min(
                Math.max(rect.left + rect.width / 2 - 160, 8),
                window.innerWidth - 320 - 8,
            );

            const top = openTop
                ? Math.max(8, rect.top - cardHeight - 8)
                : Math.min(
                      rect.bottom + 8,
                      window.innerHeight - cardHeight - 8,
                  );

            cardStyle.value = {
                position: "fixed",
                top: top + "px",
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
        <slot></slot>
    </div>

    <Teleport to="body">
        <div
            v-if="showCard"
            :style="cardStyle"
            class="hover-card-job z-[9999] w-96 rounded-2xl border border-gray-100 bg-white shadow-2xl transition-all duration-300"
            @mouseenter="cancelClose"
            @mouseleave="handleMouseLeave"
        >
            <!-- Loading -->
            <div
                v-if="loading"
                class="flex h-32 items-center justify-center text-gray-500"
            >
                <svg
                    class="mr-2 h-6 w-6 animate-spin text-gray-400"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    />
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8v8z"
                    />
                </svg>
                Loading job...
            </div>

            <!-- Data -->
            <div v-else-if="jobData" class="p-5">
                <div class="flex items-start gap-4">
                    <img
                        :src="businessLogo"
                        alt="Business Logo"
                        class="h-14 w-14 rounded-full border object-cover"
                    />

                    <div class="flex-1">
                        <h3
                            class="text-lg font-semibold leading-tight text-gray-900"
                        >
                            {{ jobData.title }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ jobData.job_type ?? "N/A" }}
                        </p>
                    </div>
                </div>

                <div class="my-4 border-t border-gray-100"></div>

                <div class="space-y-2 text-sm">
                    <p class="flex items-center gap-2">
                        👥
                        <span class="text-gray-700"
                            >Preferred:
                            {{ jobData.preferred_worker_types ?? "N/A" }}</span
                        >
                    </p>
                    <p class="flex items-center gap-2">
                        💸
                        <span class="text-gray-700"
                            >Hourly Rate: ₱{{
                                jobData.hourly_rate ?? "N/A"
                            }}</span
                        >
                    </p>
                    <p class="flex items-center gap-2">
                        📊
                        <span class="text-gray-700"
                            >Salary / Month: ₱{{
                                jobData.salary_per_month ?? "N/A"
                            }}</span
                        >
                    </p>
                    <p class="flex items-center gap-2">
                        📩
                        <span class="text-gray-700"
                            >Applications:
                            {{ jobData.applications_count }}</span
                        >
                    </p>
                    <p class="flex items-center gap-2">
                        🕒
                        <span class="text-gray-700"
                            >Posted: {{ jobData.created_at }}</span
                        >
                    </p>
                </div>
            </div>

            <!-- Empty -->
            <div v-else class="p-6 text-center text-sm text-gray-500">
                No data available
            </div>
        </div>
    </Teleport>
</template>
