<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import dayjs from "dayjs";
import Layout from "../Layouts/Layout.vue";
import SetupProfileLayout from "../Layouts/SetupProfileLayout.vue";
import WorkDetailsForm from "../Components/WorkDetailsForm.vue";
import InputFlashMessage from "../Components/InputFlashMessage.vue";
import EducationalAttainment from "../Components/EducationalAttainment.vue";

defineOptions({
    layout: [Layout, SetupProfileLayout],
});

let form = useForm({
    job_title: null,
    profile_description: null,
    highest_educational_attainment: null,
    job_type: null,
    work_hour_per_day: null,
    hour_pay: null,
    month_pay: null,
    birth_year: null,
    gender: null,
    resume: null,
});

let resumeName = ref(""); // Store uploaded file name

function addResume(e) {
    if (e.target.files[0]) {
        form.resume = e.target.files[0];
        resumeName.value = e.target.files[0].name; // Display file name
    }
}

let isDisabled = ref(false);
const submit = () => {
    form.post(route("create.profile.post"), {
        onStart: () => {
            isDisabled.value = true;
        },
        onFinish: () => {
            isDisabled.value = false;
        },
    });
};
</script>

<template>
    <Head title="Create Profile | iCan Careers" />

    <div class="flex justify-center">
        <div
            class="mt-8 w-full max-w-3xl rounded-lg border border-gray-300 bg-white p-8 shadow-md"
        >
            <h2 class="text-center text-3xl font-bold text-gray-900">
                Create Your Profile
            </h2>
            <p class="mb-6 text-center text-lg text-gray-700">
                Complete your details to get better job recommendations.
            </p>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Job Title -->
                <div>
                    <label class="block text-lg font-semibold text-gray-800"
                        >Job Title</label
                    >
                    <input
                        v-model="form.job_title"
                        type="text"
                        class="mt-2 w-full rounded-lg border border-gray-400 bg-gray-100 px-4 py-3 text-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. Social Media Manager"
                    />
                    <InputFlashMessage
                        type="error"
                        :message="form.errors.job_title"
                    />
                </div>

                <!-- Profile Description (Non-Adjustable) -->
                <div>
                    <label class="block text-lg font-semibold text-gray-800"
                        >Profile Description</label
                    >
                    <textarea
                        v-model="form.profile_description"
                        class="mt-2 h-60 w-full resize-none rounded-lg border border-gray-400 bg-gray-100 px-4 py-3 text-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Describe your skills and work experience..."
                    ></textarea>
                    <InputFlashMessage
                        type="error"
                        :message="form.errors.profile_description"
                    />
                </div>

                <!-- Educational Attainment (Now Required) -->
                <div>
                    <label class="block text-lg font-semibold text-gray-800"
                        >Highest Educational Attainment
                        <span class="text-red-500">*</span></label
                    >
                    <EducationalAttainment
                        v-model="form.highest_educational_attainment"
                        :error="form.errors.highest_educational_attainment"
                        :openToAll="true"
                    />
                </div>

                <!-- Work Details (Improved Layout) -->
                <div class="rounded-lg bg-gray-100 p-6 shadow-md">
                    <h3 class="mb-4 text-xl font-bold text-gray-900">
                        Looking for
                    </h3>
                    <div class="grid gap-4 md:grid-cols-2">
                        <!-- Job Type -->
                        <div>
                            <label
                                class="block text-lg font-semibold text-gray-800"
                                >Job Type</label
                            >
                            <select
                                v-model="form.job_type"
                                class="w-full rounded-lg border border-gray-400 bg-white px-4 py-3 text-lg focus:ring-2 focus:ring-blue-500"
                            >
                                <option disabled value="">
                                    Select job type
                                </option>
                                <option>Full-time</option>
                                <option>Part-time</option>
                            </select>
                        </div>

                        <!-- Work Hours Per Day -->
                        <div>
                            <label
                                class="block text-lg font-semibold text-gray-800"
                                >Work Hours Per Day</label
                            >
                            <input
                                v-model="form.work_hour_per_day"
                                type="number"
                                min="1"
                                max="12"
                                class="w-full rounded-lg border border-gray-400 bg-white px-4 py-3 text-lg focus:ring-2 focus:ring-blue-500"
                                placeholder="e.g. 6 hours"
                            />
                        </div>

                        <!-- Hourly Pay -->
                        <div>
                            <label
                                class="block text-lg font-semibold text-gray-800"
                                >Hourly Pay (₱)</label
                            >
                            <input
                                v-model="form.hour_pay"
                                type="number"
                                min="0"
                                class="w-full rounded-lg border border-gray-400 bg-white px-4 py-3 text-lg focus:ring-2 focus:ring-blue-500"
                                placeholder="e.g. 150"
                            />
                        </div>

                        <!-- Monthly Pay -->
                        <div>
                            <label
                                class="block text-lg font-semibold text-gray-800"
                                >Monthly Pay (₱)</label
                            >
                            <input
                                v-model="form.month_pay"
                                type="number"
                                min="0"
                                class="w-full rounded-lg border border-gray-400 bg-white px-4 py-3 text-lg focus:ring-2 focus:ring-blue-500"
                                placeholder="e.g. 20,000"
                            />
                        </div>
                    </div>
                </div>

                <!-- Birth Year -->
                <div>
                    <label class="block text-lg font-semibold text-gray-800"
                        >Birth Year</label
                    >
                    <input
                        v-model="form.birth_year"
                        type="number"
                        min="1900"
                        :max="dayjs().format('YYYY') - 18"
                        class="mt-2 w-32 rounded-lg border border-gray-400 bg-gray-100 px-4 py-3 text-center text-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="YYYY"
                        required
                    />
                </div>

                <!-- Gender -->
                <div>
                    <p class="text-lg font-semibold text-gray-800">Gender</p>
                    <div class="mt-2 flex items-center gap-6">
                        <label
                            class="flex cursor-pointer items-center gap-2 text-lg"
                        >
                            <input
                                v-model="form.gender"
                                type="radio"
                                value="Male"
                                class="h-6 w-6"
                            />
                            <span class="text-gray-700">Male</span>
                        </label>
                        <label
                            class="flex cursor-pointer items-center gap-2 text-lg"
                        >
                            <input
                                v-model="form.gender"
                                type="radio"
                                value="Female"
                                class="h-6 w-6"
                            />
                            <span class="text-gray-700">Female</span>
                        </label>
                    </div>
                    <InputFlashMessage
                        type="error"
                        :message="form.errors.gender"
                    />
                </div>

                <!-- Resume Upload (Shows File Name) -->
                <div>
                    <label class="block text-lg font-semibold text-gray-800"
                        >Upload Resume (Optional)</label
                    >
                    <div class="mt-2 flex items-center gap-4">
                        <label
                            for="resume-upload"
                            class="cursor-pointer rounded-lg bg-[#fa8334] px-6 py-3 text-lg text-white shadow-md transition hover:bg-[#fa8334]"
                        >
                            Choose File
                        </label>
                        <span v-if="resumeName" class="text-lg text-gray-700">{{
                            resumeName
                        }}</span>
                    </div>
                    <input
                        id="resume-upload"
                        @change="addResume"
                        type="file"
                        class="hidden"
                    />
                    <InputFlashMessage
                        type="error"
                        :message="form.errors.resume"
                    />
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center">
                    <button
                        class="w-full rounded-lg bg-[#fa8334] px-6 py-4 text-xl font-bold text-white shadow-md transition hover:bg-[#fa8334] disabled:opacity-50"
                        :disabled="isDisabled"
                    >
                        Save Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
