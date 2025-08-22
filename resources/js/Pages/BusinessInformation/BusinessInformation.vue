<script setup>
import { computed } from "vue";
import dayjs from "dayjs";
import Maps from "../Components/Maps.vue";
import { Link } from "@inertiajs/vue3";

let props = defineProps({
    business: Object,
    employer: Object,
    employers: Array,
    viewerRole: String,
});

console.log(props.employers);

const createdDate = computed(() =>
    dayjs(props.business.created_at).format("MMMM DD, YYYY"),
);
</script>

<template>
    <Head title="Business Profile | iCan Careers" />

    <div class="min-h-[calc(100vh-4.625rem)] bg-[#f3f7fa]">
        <!-- Business Header -->
        <!-- Business Header -->
        <div class="relative h-32 bg-[#FAFAFA]">
            <div
                class="absolute left-1/2 top-[40px] flex h-36 w-36 -translate-x-1/2 items-center justify-center"
            >
                <img
                    :src="
                        business.business_logo ||
                        '/assets/profile_placeholder.jpg'
                    "
                    alt="Business Logo"
                    class="h-full w-full rounded-full border object-cover"
                />
            </div>
        </div>

        <!-- Business Details -->
        <div class="bg-white pb-2">
            <div
                class="container mx-auto flex flex-col items-center pt-16 xl:max-w-7xl"
            >
                <p class="text-2xl font-bold">{{ business.business_name }}</p>
                <p class="text-gray-600">{{ business.industry }}</p>
            </div>
        </div>

        <div
            class="container mx-auto mt-8 grid gap-6 md:grid-cols-[1fr,300px] xl:max-w-7xl"
        >
            <!-- Left Column -->
            <div class="rounded-lg bg-white p-8 text-gray-600">
                <h2 class="mb-3 text-xl font-bold">Business Overview</h2>
                <p>
                    {{
                        business.business_description ||
                        "No description available."
                    }}
                </p>

                <p class="mt-2 text-sm text-gray-500">
                    Created: {{ createdDate }}
                </p>

                <h2 class="mb-3 mt-6 text-xl font-bold">Business Location</h2>
                <Maps
                    :center-props="business.business_location"
                    :marked-coordinates-props="business.business_location"
                    :disable-search="true"
                    :disable-set-maker="true"
                    style="height: 400px; border-radius: 12px; overflow: hidden"
                />
            </div>

            <!-- Right Column -->
            <div class="rounded-lg bg-white p-8 text-gray-600">
                <!-- Business Owner -->
                <h2 class="mb-3 text-xl font-bold">Business Owner</h2>
                <div v-if="employer">
                    <Link
                        :href="route('visit.employer.profile', employer.id)"
                        class="hover:text-blue-600"
                    >
                        <p class="text-lg font-semibold">{{ employer.name }}</p>
                        <p class="text-sm text-gray-500">
                            {{ employer.email }}
                        </p>
                    </Link>
                </div>
                <p v-else class="text-sm text-gray-400">No owner assigned.</p>

                <!-- Affiliated Employers -->
                <h2 class="mb-3 mt-6 text-xl font-bold">
                    Affiliated Employers
                </h2>
                <ul v-if="employers && employers.length > 0">
                    <li v-for="emp in employers" :key="emp.id" class="mb-2">
                        <Link
                            :href="route('visit.employer.profile', emp.id)"
                            class="hover:text-blue-600"
                        >
                            <p class="font-semibold">{{ emp.name }}</p>
                            <p class="text-sm text-gray-500">{{ emp.email }}</p>
                        </Link>
                    </li>
                </ul>
                <p v-else class="text-sm text-gray-400">
                    No affiliated employers yet.
                </p>

                <!-- Conditional section based on viewer -->
                <div v-if="viewerRole === 'worker'" class="mt-4">
                    <button
                        class="rounded-lg bg-green-600 px-4 py-2 text-white hover:bg-green-700"
                    >
                        Apply for Job
                    </button>
                </div>

                <div v-else-if="viewerRole === 'employer'" class="mt-4">
                    <button
                        class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                    >
                        Edit Business Info
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
