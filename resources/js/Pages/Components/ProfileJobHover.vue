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
      const cardEl = document.querySelector(".hover-card-job");
      const cardHeight = cardEl ? cardEl.offsetHeight : 220;
      const openTop = spaceBelow < cardHeight && spaceAbove > spaceBelow;

      const left = Math.min(
        Math.max(rect.left + rect.width / 2 - 160, 8),
        window.innerWidth - 320 - 8
      );

      const top = openTop
        ? Math.max(8, rect.top - cardHeight - 8)
        : Math.min(rect.bottom + 8, window.innerHeight - cardHeight - 8);

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
      <div v-if="loading" class="flex items-center justify-center h-32 text-gray-500">
        <svg
          class="animate-spin h-6 w-6 text-gray-400 mr-2"
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
          <!-- <img
            v-if="jobData.business?.logo"
            :src="jobData.business.logo"
            alt="Business Logo"
            class="h-16 w-16 rounded-full object-cover border-2 border-gray-200 shadow-sm"
          /> -->

          <div class="flex-1">
            <h3 class="font-semibold text-gray-900 text-lg leading-tight">
              {{ jobData.title }}
            </h3>
            <p class="text-sm text-gray-500 mt-1">
              {{ jobData.job_type ?? "N/A" }}
            </p>
          </div>
        </div>

        <div class="border-t border-gray-100 my-4"></div>

        <div class="space-y-2 text-sm">
          <p class="flex items-center gap-2">
            👥 <span class="text-gray-700">Preferred: {{ jobData.preferred_worker_types ?? "N/A" }}</span>
          </p>
          <p class="flex items-center gap-2">
            💸 <span class="text-gray-700">Hourly Rate: ₱{{ jobData.hourly_rate ?? "N/A" }}</span>
          </p>
          <p class="flex items-center gap-2">
            📊 <span class="text-gray-700">Salary / Month: ₱{{ jobData.salary_per_month ?? "N/A" }}</span>
          </p>
          <p class="flex items-center gap-2">
            📩 <span class="text-gray-700">Applications: {{ jobData.applications_count }}</span>
          </p>
          <p class="flex items-center gap-2">
            🕒 <span class="text-gray-700">Posted: {{ jobData.created_at }}</span>
          </p>
        </div>
      </div>

      <!-- Empty -->
      <div v-else class="p-6 text-gray-500 text-center text-sm">
        No data available
      </div>
    </div>
  </Teleport>
</template>
