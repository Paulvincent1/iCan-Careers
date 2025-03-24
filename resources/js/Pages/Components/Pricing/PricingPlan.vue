<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    title: String,
    subtitle: String,
    price: String,
    features: Array,
    borderColor: String,
    priceColor: String,
    tag: null,
});
</script>

<template>
    <div class="m-5 mx-auto  max-w-xs transform transition-transform">
        <div class="border bg-white shadow-lg" :class="borderColor">
            <div class="h-[700px] w-[300px] py-6 text-center">
                <div class="border-b-2">
                    <h4 class="text-xl font-semibold" :class="priceColor">
                        {{ title }}
                    </h4>
                    <p v-if="subtitle" class="mt-2 text-lg text-gray-600">
                        {{ subtitle }}
                    </p>
                    <p class="text-primary my-4 text-4xl font-bold">
                        {{ price }}
                    </p>
                </div>
                <div class="text-start card-body p-6">
                    <ul>
                        <li
                            v-for="(feature, index) in features"
                            :key="index"
                            class="pt-4"
                        >
                            <span v-if="feature.strike"
                                ><p class="line-through">
                                    {{ feature.text }}
                                </p></span
                            >
                            <span v-else>{{ feature.text }}</span>
                        </li>
                    </ul>
                </div>
                <div
                    class="absolute bottom-0 left-0 right-0 mb-5"
                    v-if="tag !== 'Free'"
                >
                    <button
                        @click="$emit('buyNow', tag)"
                        class="mt-4 inline-block rounded-lg border border-blue-500 bg-transparent px-4 py-2 font-bold text-blue-500 transition hover:bg-blue-500 hover:text-white"
                    >
                        <span
                            v-if="
                                $page.props.auth.user?.role?.name === 'Employer'
                            "
                        >
                            Buy Now
                        </span>
                        <Link v-else :href="route('register.create')">
                            Register
                        </Link>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
