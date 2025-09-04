<script setup>
import { ref, nextTick } from "vue";
import {Link} from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
  userId: {
    type: Number,
    required: true,
  },
});

const userData = ref(null);
const showCard = ref(false);
const loading = ref(false);
const cardStyle = ref({});
const triggerRef = ref(null);
let timeoutId = null;

async function fetchPreview() {
  if (userData.value || loading.value) return;
  loading.value = true;
  try {
    const res = await axios.get(`/user/${props.userId}/preview`);
    userData.value = res.data;
  } catch (err) {
    console.error("Error fetching user preview:", err);
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
      const cardEl = document.querySelector(".hover-card");
      const cardHeight = cardEl ? cardEl.offsetHeight : 110;
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
      class="hover-card z-[9999] w-96 rounded-2xl border border-gray-100 bg-white shadow-2xl transition-all duration-300"
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
        Loading profile...
      </div>

      <!-- Data -->
      <div v-else-if="userData" class="p-5">
        <div class="flex items-start gap-4">
          <img
            :src="userData.profile_img ?? '/assets/profile_placeholder.jpg'"
            class="h-16 w-16 rounded-full object-cover border-2 border-gray-200 shadow-sm"
            alt="profile"
          />

          <div class="flex-1">
            <h3 class="font-semibold text-gray-900 text-lg leading-tight">
              {{ userData.name }}
            </h3>
            <p class="text-sm text-gray-500 mt-1">
              ⭐ {{ userData.average_star }} / 5
              <span class="text-gray-400">
                ({{ userData.received_reviews_count }} reviews)
              </span>
            </p>
          </div>
        </div>

        <div class="border-t border-gray-100 my-4"></div>

        <div class="space-y-2 text-sm">
          <!-- Common -->
          <p v-if="userData.email" class="flex items-center gap-2">
            📧 <span class="text-gray-700">{{ userData.email }}</span>
          </p>
          <p v-if="userData.created_at" class="flex items-center gap-2">
            🤵 <span class="text-gray-700">Member Since: {{ userData.created_at }}</span>
          </p>

          <!-- Worker -->
          <template v-if="userData.role !== 'Employer'">
            <p v-if="userData.highest_educational_attainment">🎓 {{ userData.highest_educational_attainment }}</p>
            <p v-if="userData.website_link">
              🔗 <a :href="userData.website_link" target="_blank" class="text-blue-600 hover:underline truncate">
                {{ userData.website_link }}
              </a>
            </p>
            <p v-if="userData.address">📍 {{ userData.address }}</p>
            <p v-if="userData.skills?.length">🛠 {{ userData.skills.join(', ') }}</p>
          </template>

          <!-- Employer -->
          <template v-else>
            <p v-if="userData.company_name">🏢 {{ userData.company_name }}</p>
             <p v-if="userData.industries?.length">💼 {{ userData.industries.join(', ') }}</p>
            <p v-if="userData.address">📍 {{ userData.address }}</p>
          </template>
        </div>
      </div>

      <!-- Empty -->
      <div v-else class="p-6 text-gray-500 text-center text-sm">
        No data available
      </div>
    </div>
  </Teleport>
</template>
