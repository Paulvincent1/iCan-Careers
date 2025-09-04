<script setup>
import { ref, nextTick, watch } from "vue";
import axios from "axios";

const props = defineProps({
    businessId: {
        type: Number,
        required: true,
    },
});

const businessData = ref(null);
const showCard = ref(false);
const loading = ref(false);
const cardStyle = ref({});
const triggerRef = ref(null);
let timeoutId = null;

// 🔹 Watch for businessId changes (reset data if new business is hovered)
watch(
    () => props.businessId,
    () => {
        businessData.value = null;
        loading.value = false;
    },
);


async function fetchPreview() {
    if (businessData.value || loading.value) return;
    loading.value = true;
    try {
        const res = await axios.get(`/business/${props.businessId}/preview`);
        const data = res.data;

       
        businessData.value = data;
    } catch (err) {
        console.error("Error fetching business preview:", err);
    } finally {
        loading.value = false;
    }
}

function handleMouseEnter() {
    showCard.value = true;
    fetchPreview();

    nextTick(() => {
        if (!triggerRef.value) return;

        const rect = triggerRef.value.getBoundingClientRect();
        const spaceBelow = window.innerHeight - rect.bottom;
        const spaceAbove = rect.top;
        const cardEl = document.querySelector(".business-hover-card");
        const cardHeight = cardEl ? cardEl.offsetHeight : 110;
        const openTop = spaceBelow < cardHeight && spaceAbove > spaceBelow;

        const left = Math.min(
            Math.max(rect.left + rect.width / 2 - 160, 8),
            window.innerWidth - 320 - 8,
        );

        const top = openTop
            ? Math.max(8, rect.top - cardHeight - 8)
            : Math.min(rect.bottom + 8, window.innerHeight - cardHeight - 8);

        cardStyle.value = {
            position: "fixed",
            top: top + "px",
            left: left + "px",
        };
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
            class="business-hover-card z-[9999] w-96 rounded-2xl border border-gray-100 bg-white shadow-2xl transition-all duration-300"
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
                Loading business...
            </div>

            <!-- Data -->
            <div v-else-if="businessData" class="p-5">
                <div class="flex items-start gap-4">
                    <div
                        class="flex h-16 w-16 items-center justify-center rounded-full border border-gray-200 bg-gray-100 text-xl font-bold text-gray-600 shadow-sm"
                    >
                        <img
                            :src="
                                businessData.business_logo ??
                                '/assets/profile_placeholder.jpg'
                            "
                            class="h-16 w-16 rounded-full border-2 border-gray-200 object-cover shadow-sm"
                            alt="profile"
                        />
                    </div>

                    <div class="flex-1">
                        <h3
                            class="text-lg font-semibold leading-tight text-gray-900"
                        >
                            {{ businessData.business_name }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            🏭 {{ businessData.industry }} 
                        </p>
                    </div>
                </div>

                <div class="my-4 border-t border-gray-100"></div>

                <div class="space-y-2 text-sm">
                                       
                    <p v-if="businessData.contact_number">
                        📞 {{ businessData.contact_number }} 
                    </p>
                    
                    <p v-if="businessData.description">
                        📝 {{ businessData.description }} 
                    </p>
                    <p v-if="businessData.created_at">
                        📅 Joined {{ businessData.created_at }} 
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
