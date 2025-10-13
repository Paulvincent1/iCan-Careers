<script setup>
import { Link } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    title: String,
    subtitle: String,
    price: String,
    features: Array,
    borderColor: String,
    priceColor: String,
    tag: String,
    isPro: Boolean,
    isPremium: Boolean,
});

const emit = defineEmits(['buyNow']);

// Hover state for card animation
const isHovered = ref(false);

const billingSuffix = computed(() => {
  if (props.tag === 'Pro') return '/month';
  if (props.tag === 'Premium') return '/year';
  return ''; // Free or anything else
});
// Determine card styling based on tier
const cardGradient = computed(() => {
    if (props.tag === 'Free') {
        return 'from-emerald-50 to-green-50';
    } else if (props.tag === 'Pro') {
        return 'from-blue-50 to-indigo-50';
    } else if (props.tag === 'Premium') {
        return 'from-red-50 to-orange-50';
    }
    return 'from-gray-50 to-white';
});

const accentColor = computed(() => {
    if (props.tag === 'Free') return 'emerald';
    if (props.tag === 'Pro') return 'blue';
    if (props.tag === 'Premium') return 'purple';
    return 'gray';
});

const buttonClasses = computed(() => {
    if (props.tag === 'Pro') {
        return 'bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white shadow-blue-500/25';
    } else if (props.tag === 'Premium') {
        return 'bg-gradient-to-r from-yellow-500 to-orange-600 hover:from-yellow-600 hover:to-orange-700 text-white shadow-purple-500/25';
    }
    return 'bg-gray-600 hover:bg-gray-700 text-white';
});

const iconColor = computed(() => {
    const colors = {
        'Free': 'text-emerald-500',
        'Pro': 'text-blue-500',
        'Premium': 'text-purple-500'
    };
    return colors[props.tag] || 'text-gray-500';
});
</script>

<template>
    <div
        class="relative group"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
    >
        <!-- Popular badge for Premium -->
        <div
            v-if="tag === 'Premium'"
            class="absolute -top-4 left-1/2 transform -translate-x-1/2 z-10"
        >
            <span class="bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs font-bold px-4 py-1.5 rounded-full shadow-lg animate-pulse">
                MOST POPULAR
            </span>
        </div>

        <!-- Main Card -->
        <div
            class="relative h-full bg-white rounded-2xl overflow-hidden transition-all duration-500 ease-out"
            :class="[
                'shadow-xl hover:shadow-2xl',
                tag === 'Premium' ? 'ring-2 ring-purple-400 ring-opacity-50' : '',
                isHovered ? 'transform -translate-y-2 scale-[1.02]' : ''
            ]"
        >
            <!-- Gradient Header -->
            <div
                class="relative h-64 bg-gradient-to-br p-8 text-white overflow-hidden"
                :class="{
                    'from-emerald-400 to-green-500': tag === 'Free',
                    'from-blue-500 to-indigo-600': tag === 'Pro',
                    'from-yellow-500 via-orange-500 to-amber-500': tag === 'Premium'
                }"
            >
                <!-- Decorative circles -->
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-white opacity-10 rounded-full"></div>
                <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-white opacity-10 rounded-full"></div>

                <!-- Pricing info -->
                <div class="relative z-10">
                    <h3 class="text-2xl font-bold mb-1">{{ title }}</h3>
                    <p v-if="subtitle" class="text-sm opacity-90 mb-4">{{ subtitle }}</p>
                    <div class="flex items-baseline">
                        <span class="text-4xl font-bold">{{ price }}</span>
                        <span v-if="billingSuffix" class="ml-2 text-sm opacity-75">{{ billingSuffix }}</span>
                    </div>

                </div>

                <!-- Wave decoration -->
                <svg class="absolute bottom-0 left-0 w-full" viewBox="0 0 1440 100" preserveAspectRatio="none">
                    <path fill="white" d="M0,32L48,37.3C96,43,192,53,288,58.7C384,64,480,64,576,58.7C672,53,768,43,864,42.7C960,43,1056,53,1152,58.7C1248,64,1344,64,1392,64L1440,64L1440,100L1392,100C1344,100,1248,100,1152,100C1056,100,960,100,864,100C768,100,672,100,576,100C480,100,384,100,288,100C192,100,96,100,48,100L0,100Z"></path>
                </svg>
            </div>

            <!-- Features List -->
            <div class="p-8 pb-24 h-[420px]">
                <ul class="space-y-4">
                    <li
                        v-for="(feature, index) in features"
                        :key="index"
                        class="flex items-start group/item transition-all duration-300 hover:translate-x-1"
                    >
                        <!-- Icon -->
                        <div class="flex-shrink-0 mr-3 mt-0.5">
                            <svg
                                v-if="!feature.strike"
                                class="w-5 h-5 transition-transform duration-300 group-hover/item:scale-110"
                                :class="iconColor"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <svg
                                v-else
                                class="w-5 h-5 text-gray-300"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <!-- Feature text -->
                        <span
                            class="text-gray-700 text-sm leading-relaxed"
                            :class="feature.strike ? 'line-through opacity-40' : ''"
                        >
                            {{ feature.text }}
                        </span>
                    </li>
                </ul>
            </div>

            <!-- CTA Button -->
            <div class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-white via-white">
                <button
                    v-if="tag !== 'Free'"
                    @click="$emit('buyNow', tag)"
                    class="w-full py-3.5 px-6 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                    :class="buttonClasses"
                >
                    <span v-if="$page.props.auth.user?.role?.name === 'Employer'" class="flex items-center justify-center">
                        <span>Get Started</span>
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </span>
                    <Link v-else :href="route('register.create')" class="flex items-center justify-center">
                        <span>Register Now</span>
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </Link>
                </button>

                <div v-else class="text-center">
                    <Link
                        :href="route('register.create')"
                        class="inline-flex items-center justify-center w-full py-3.5 px-6 rounded-xl font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition-all duration-300"
                    >
                        <span>Start Free</span>
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Floating animation effect -->
        <div
            class="absolute inset-0 bg-gradient-to-r rounded-2xl opacity-0 transition-opacity duration-500 -z-10 blur-xl"
            :class="[
                tag === 'Free' ? 'from-emerald-200 to-green-200' : '',
                tag === 'Pro' ? 'from-blue-200 to-indigo-200' : '',
                tag === 'Premium' ? 'from-yellow-200 to-orange-200' : '',
                isHovered ? 'opacity-40' : 'opacity-0'
            ]"
        ></div>
    </div>
</template>

<style scoped>
/* Add subtle floating animation */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.group:hover {
    animation: float 3s ease-in-out infinite;
}

/* Smooth transitions for all interactive elements */
* {
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
