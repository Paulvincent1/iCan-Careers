<script setup>
import { computed, ref } from "vue";
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
console.log(props.viewerRole);

const createdDate = computed(() =>
    dayjs(props.business.created_at).format("MMMM DD, YYYY"),
);

// state for expand/collapse
const showAllEmployers = ref(false);
</script>

<template>
    <Head title="Business Profile | iCan Careers" />

    <div class="min-h-[calc(100vh-4.625rem)] bg-[#f3f7fa]">
        <div class="relative h-64 bg-gray-200">
            <div
                class="absolute inset-0 bg-gradient-to-r from-orange-100 to-orange-100"
            ></div>

            <div class="container relative mx-auto h-full xl:max-w-7xl">
                <div class="absolute -bottom-16 left-8 flex items-end">
                    <div class="h-36 w-36 rounded-md bg-white p-1 shadow-lg">
                        <img
                            :src="
                                business.business_logo ||
                                '/assets/profile_placeholder.jpg'
                            "
                            alt="Business Logo"
                            class="h-full w-full rounded-md object-cover"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Container -->
        <div class="container mx-auto mt-20 xl:max-w-7xl">
            <!-- Business Header Info -->
            <div class="flex flex-col px-8 md:flex-row">
                <!-- Left Column - Business Info -->
                <div class="w-full md:w-8/12">
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h1 class="text-3xl font-bold text-gray-900">
                            {{ business.business_name }}
                        </h1>
                        <p class="mt-1 text-gray-600">
                            {{ business.industry }}
                        </p>

                        <div
                            class="mt-4 flex flex-wrap items-center text-sm text-gray-500"
                        >
                            <span class="mr-4">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                Created {{ createdDate }}
                            </span>
                            <span v-if="business.website" class="mr-4">
                                <i class="fas fa-globe mr-1"></i>
                                <a
                                    :href="business.website"
                                    target="_blank"
                                    class="text-blue-600 hover:underline"
                                    >Website</a
                                >
                            </span>
                            <span v-if="business.employees_count" class="mr-4">
                                <i class="fas fa-users mr-1"></i>
                                {{ business.employees_count }} employees
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 flex space-x-3">
                            <div v-if="viewerRole === 'worker'" class="mt-4">
                                <button
                                    class="rounded-full bg-blue-600 px-5 py-2.5 font-medium text-white transition-colors hover:bg-blue-700"
                                >
                                    Follow
                                </button>
                                <button
                                    class="ml-3 rounded-full border border-blue-600 px-5 py-2.5 font-medium text-blue-600 transition-colors hover:bg-blue-50"
                                >
                                    <i class="fas fa-briefcase mr-2"></i>View
                                    jobs
                                </button>
                            </div>

                            <div
                                v-else-if="viewerRole === 'employer'"
                                class="mt-4"
                            >
                                <button
                                    class="rounded-full bg-blue-600 px-5 py-2.5 font-medium text-white transition-colors hover:bg-blue-700"
                                >
                                    <i class="fas fa-pencil-alt mr-2"></i>Edit
                                    page
                                </button>
                                <button
                                    class="ml-3 rounded-full border border-gray-400 px-5 py-2.5 font-medium text-gray-700 transition-colors hover:bg-gray-100"
                                >
                                    <i class="fas fa-eye mr-2"></i>View as
                                    public
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- About Section -->
                    <div class="mt-4 rounded-lg bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-xl font-bold text-gray-900">
                            About
                        </h2>
                        <p class="leading-relaxed text-gray-600">
                            {{
                                business.business_description ||
                                "No description available."
                            }}
                        </p>
                    </div>

                    <!-- Location Section -->
                    <div class="mt-4 rounded-lg bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-xl font-bold text-gray-900">
                            Location
                        </h2>
                        <Maps
                            :center-props="business.business_location"
                            :marked-coordinates-props="
                                business.business_location
                            "
                            :disable-search="true"
                            :disable-set-maker="true"
                            style="
                                height: 400px;
                                border-radius: 12px;
                                overflow: hidden;
                            "
                        />
                    </div>
                </div>

                <!-- Right Column - People & Actions -->
                <div class="mt-4 w-full md:mt-0 md:w-4/12 md:pl-4">
                    <!-- Affiliated Employers Card -->
                    <div class="rounded-lg bg-white p-5 shadow-sm">
                        <div class="mb-4 flex items-center justify-between">
                            <h2 class="text-lg font-bold text-gray-900">
                                People
                            </h2>
                            <span class="text-sm font-semibold text-blue-600">{{
                                employers.length
                            }}</span>
                        </div>

                        <div
                            v-if="employers && employers.length > 0"
                            class="space-y-4"
                        >
                            <div
                                v-for="emp in showAllEmployers
                                    ? employers
                                    : employers.slice(0, 3)"
                                :key="emp.id"
                                class="flex items-center"
                            >
                                <div
                                    class="mr-3 h-12 w-12 overflow-hidden rounded-full"
                                >
                                    <img
                                        :src="
                                            emp.profile_img ||
                                            '/assets/profile_placeholder.jpg'
                                        "
                                        :alt="emp.name"
                                        class="h-full w-full object-cover"
                                    />
                                </div>
                                <div>
                                    <template
                                        v-if="
                                            viewerRole === 'Senior' ||
                                            viewerRole === 'PWD'
                                        "
                                    >
                                        <Link
                                            :href="
                                                route(
                                                    'visit.employer.profile',
                                                    emp.id,
                                                )
                                            "
                                            class="text-sm font-semibold text-gray-900 hover:text-blue-600 hover:underline"
                                        >
                                            {{ emp.name }}
                                        </Link>
                                    </template>
                                    <template v-else>
                                        <span
                                            class="text-sm font-semibold text-gray-700"
                                        >
                                            {{ emp.name }}
                                        </span>
                                    </template>
                                    <p class="text-xs text-gray-500">
                                        {{ emp.position || "Employer" }}
                                    </p>
                                </div>
                            </div>

                            <!-- Expand/Collapse button -->
                            <div v-if="employers.length > 3" class="pt-2">
                                <button
                                    @click="
                                        showAllEmployers = !showAllEmployers
                                    "
                                    class="text-sm font-semibold text-blue-600 hover:underline focus:outline-none"
                                >
                                    <span v-if="!showAllEmployers"
                                        >See all {{ employers.length }} people
                                        ›</span
                                    >
                                    <span v-else>Show less ▲</span>
                                </button>
                            </div>
                        </div>

                        <p v-else class="text-sm text-gray-400">
                            No affiliated employers yet.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
