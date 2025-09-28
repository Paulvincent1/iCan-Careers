<script setup>
import { ref, watch } from "vue";
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

let resumeName = ref("");

function addResume(e) {
    if (e.target.files[0]) {
        form.resume = e.target.files[0];
        resumeName.value = e.target.files[0].name;
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

// ✅ Auto-calculation - Fixed to work like Create Job form
watch(
    () => [form.work_hour_per_day, form.hour_pay],
    ([hours, rate]) => {
        if (hours && rate) {
            // Assuming 22 working days per month (like in Create Job)
            form.month_pay = (hours * rate * 22).toFixed(2);
        } else {
            form.month_pay = null;
        }
    },
);
</script>

<template>
    <Head title="Create Profile | iCan Careers" />

    <div class="mb-4 flex justify-center">
        <div class="mt-5 w-full rounded-lg bg-white p-8">
            <h2 class="text-[24px] text-gray-900">Create Your Profile</h2>
            <p class="mb-6 text-lg text-gray-700">
                Complete your details to get better job opportunities.
            </p>
            <hr class="my-4 border-gray-300" />
            <h3 class="text-[18px] font-bold text-gray-900">
                Finish your Profile
            </h3>
            <h3 class="mb-4 text-xs">
                This information will be displayed publicly so be careful what
                you share.
            </h3>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Job Title -->
                <div>
                    <label class="text-gray-500">Job Title</label>
                    <input
                        v-model="form.job_title"
                        type="text"
                        class="mt-2 w-full rounded-lg border px-4 py-2 text-lg outline-orange-200"
                        placeholder="e.g. Social Media Manager"
                    />
                    <InputFlashMessage
                        type="error"
                        :message="form.errors.job_title"
                    />
                </div>

                <!-- Profile Description (Non-Adjustable) -->
                <div>
                    <label class="text-gray-500">Profile Description</label>
                    <textarea
                        v-model="form.profile_description"
                        class="mt-2 h-[200px] w-full resize-none rounded-lg border px-4 py-2 text-lg outline-orange-200"
                        placeholder="Describe your skills and work experience..."
                    ></textarea>
                    <InputFlashMessage
                        type="error"
                        :message="form.errors.profile_description"
                    />
                </div>

                <!-- Educational Attainment (Now Required) -->
                <div>
                    <label class="text-gray-500"
                        >Highest Educational Attainment
                        <span class="text-red-500">*</span></label
                    >
                    <EducationalAttainment
                        v-model="form.highest_educational_attainment"
                        :error="form.errors.highest_educational_attainment"
                        :openToAll="true"
                    />
                </div>
                <hr class="my-4 border-gray-300" />
                <!-- Work Details (Improved Layout) -->
                <div class="rounded-lg">
                    <h3 class="text-[18px] font-bold text-gray-900">
                        Looking for
                    </h3>
                    <h3 class="mb-4 text-xs">
                        Define your ideal job type, set your preferred work
                        hours and salary, and discover opportunities that match
                        your skills and goals.
                    </h3>
                    <!-- Job Type -->
                    <div>
                        <label class="block text-gray-500">Job Type</label>
                        <select
                            v-model="form.job_type"
                            class="mt-2 w-[200px] rounded-lg border px-4 py-2 text-lg outline-blue-400"
                        >
                            <option disabled value="">Select job type</option>
                            <option>Full-time</option>
                            <option>Part-time</option>
                        </select>
                        <InputFlashMessage
                            type="error"
                            :message="form.errors.job_type"
                        />
                    </div>

                    <div
                        class="my-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3"
                    >
                        <!-- Work Hours Per Day -->
                        <div>
                            <label class="text-gray-500"
                                >Work Hours per Day</label
                            >
                            <input
                                v-model.number="form.work_hour_per_day"
                                type="number"
                                min="1"
                                max="12"
                                class="mt-2 w-full rounded-lg border px-4 py-2 text-lg outline-blue-400"
                                placeholder="e.g. 6 hours"
                                required
                            />

                            <InputFlashMessage
                                type="error"
                                :message="form.errors.work_hour_per_day"
                            />
                            <small class="mt-1 text-xs text-gray-500">
                                Maximum of 12 hours per day is allowed.
                            </small>
                        </div>

                        <!-- Hourly Pay -->
                        <div>
                            <label class="text-gray-500">Hourly Pay (₱)</label>
                            <input
                                v-model.number="form.hour_pay"
                                type="number"
                                min="1"
                                step="1"
                                class="mt-2 w-full rounded-lg border px-4 py-2 text-lg outline-blue-400"
                                placeholder="e.g. 150"
                                required
                            />
                            <InputFlashMessage
                                type="error"
                                :message="form.errors.hour_pay"
                            />
                            <small class="mt-1 text-xs text-gray-500">
                                Example: ₱100 per hour (whole numbers only).
                            </small>
                        </div>

                        <!-- Monthly Pay -->
                        <div>
                            <label class="text-gray-500">Monthly Pay (₱)</label>
                            <input
                                v-model="form.month_pay"
                                type="number"
                                min="0"
                                class="mt-2 w-full cursor-not-allowed rounded-lg border bg-gray-100 px-4 py-2 text-lg text-gray-700 outline-none"
                                placeholder="Automatically calculated"
                                readonly
                            />
                            <InputFlashMessage
                                type="error"
                                :message="form.errors.month_pay"
                            />
                            <small class="mt-1 text-xs text-gray-500">
                                Automatically calculated based on hours × rate ×
                                22 working days.
                            </small>
                        </div>
                    </div>
                </div>

                <div
                    class="grid grid-cols-1 gap-4 sm:grid-cols-1 lg:grid-cols-1"
                >
                    <!-- Birth Year -->
                    <div>
                        <label class="block text-gray-500">Birth Year</label>
                        <input
                            v-model="form.birth_year"
                            type="number"
                            min="1900"
                            :max="dayjs().format('YYYY') - 18"
                            class="mt-2 w-[100px] rounded-lg border px-4 py-2 text-lg outline-blue-400"
                            placeholder="YYYY"
                            required
                        />
                        <InputFlashMessage
                            type="error"
                            :message="form.errors.birth_year"
                        />
                        <small class="mt-1 block text-xs text-gray-500">
                            PWD must be at least 18 years old.
                        </small>
                        <small class="mt-1 block text-xs text-gray-500">
                            Seniors Citizens must be at least 60 years old.
                        </small>
                    </div>

                    <!-- Gender -->
                    <div>
                        <p class="text-gray-500">Gender</p>
                        <div class="mt-2 flex items-center gap-6">
                            <label
                                class="flex cursor-pointer items-center gap-2 text-lg"
                            >
                                <input
                                    v-model="form.gender"
                                    type="radio"
                                    value="Male"
                                    class="h-4 w-4"
                                />
                                <span class="text-sm text-gray-700">Male</span>
                            </label>
                            <label
                                class="flex cursor-pointer items-center gap-2 text-lg"
                            >
                                <input
                                    v-model="form.gender"
                                    type="radio"
                                    value="Female"
                                    class="h-4 w-4"
                                />
                                <span class="text-sm text-gray-700"
                                    >Female</span
                                >
                            </label>
                        </div>
                        <InputFlashMessage
                            type="error"
                            :message="form.errors.gender"
                        />
                    </div>
                </div>

                <!-- Resume Upload (Shows File Name) -->
                <div>
                    <label class="block text-gray-500"
                        >Upload Resume (Optional)</label
                    >
                    <div class="mt-2 flex items-center gap-4">
                        <label
                            for="resume-upload"
                            class="cursor-pointer rounded bg-[#fa8334] p-2 text-white"
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
                <hr class="my-4 border-gray-300" />
                <div class="mt-4 flex justify-end gap-3">
                    <!-- Submit Button -->
                    <div
                        type="button"
                        @click="$inertia.visit(route('worker.dashboard'))"
                        class="mt-4 flex justify-end"
                    >
                        <button class="cursor-pointer rounded p-2 text-black">
                            <u>Skip for now</u>
                        </button>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4 flex justify-end">
                        <button
                            :disabled="isDisabled"
                            class="cursor-pointer rounded bg-[#fa8334] p-2 text-white disabled:opacity-50"
                        >
                            {{ isDisabled ? "Saving..." : "Save Profile" }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
