<script setup>
import { useForm } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import InputFlashMessage from "../Components/InputFlashMessage.vue";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import EducationalAttainment from "../Components/EducationalAttainment.vue";
import { onMounted, ref, useTemplateRef, watch } from "vue";
import { nanoid } from "nanoid/non-secure";
import Maps from "../Components/Maps.vue";
import Breadcrumbs from "../Components/Breadcrumbs.vue";
import Layout from "../Layouts/Layout.vue";
import SetupProfileLayout from "../Layouts/SetupProfileLayout.vue";
import SuccessfulMessage from "../Components/Popup/SuccessfulMessage.vue";

defineOptions({});

let props = defineProps({
    locationProps: null,
    jobPostProp: null,
    isEdit: null,
    canPost: Boolean,
    remaining: Number,
    limit: Number,
});

let form = useForm({
    job_title: props.jobPostProp?.job_title ?? null,
    job_type: props.jobPostProp?.job_type ?? null,
    work_arrangement: props.jobPostProp?.work_arrangement ?? null,
    location: props.jobPostProp?.location ?? props.locationProps,
    experience: props.jobPostProp?.experience ?? null,
    hour_per_day: props.jobPostProp?.hour_per_day ?? null,
    hourly_rate: props.jobPostProp?.hourly_rate ?? null,
    salary: props.jobPostProp?.salary ?? null,
    description: props.jobPostProp?.description ?? null,
    preferred_educational_attainment:
        props.jobPostProp?.preferred_educational_attainment ?? null,
    skills: props.jobPostProp?.skills ?? null,
    preferred_worker_types: props.jobPostProp?.preferred_worker_types ?? null,
});

// ✅ Auto calculation when hour/day or hourly_rate changes
watch(
    () => [form.hour_per_day, form.hourly_rate],
    ([hours, rate]) => {
        if (hours && rate) {
            // Assuming 22 working days per month
            form.salary = (hours * rate * 22).toFixed(2);
        } else {
            form.salary = null;
        }
    },
);

onMounted(() => {
    if (!form.location) {
        form.location = props.locationProps
            ? [...props.locationProps]
            : [120.9842, 14.5995];
    } else {
        showMap();
    }
});

let isMapShow = ref(false);
function showMap() {
    if (
        form.work_arrangement === "Onsite" ||
        form.work_arrangement === "Hybrid"
    ) {
        isMapShow.value = true;
    } else {
        isMapShow.value = false;
    }
}

function setCoordinates(coordinates) {
    form.location = [...coordinates];
}

watch(() => form.work_arrangement, showMap);

let isCheck = ref(false);
function check() {
    isCheck.value = !isCheck.value;
}

let isOpenToAllDisability = ref(false);
function openToAll() {
    isOpenToAllDisability.value = !isOpenToAllDisability.value;
}

let isOtherCheck = ref(false);
function otherCheck() {
    isOtherCheck.value = !isOtherCheck.value;
}

let candidateType = ref([]);
function addCandidateType(e) {
    if (!e.target.checked) {
        candidateType.value = candidateType.value.filter(
            (candidate) => candidate !== e.target.value,
        );
        if (e.target.value === "PWD") {
            isOpenToAllDisability.value = false;
            candidateType.value = candidateType.value.filter(
                (candidate) => candidate !== "Open to All Disabalities",
            );
        }
        if (e.target.value === "Other") {
            otherInput.value.value = "";
            isOtherCheck.value = false;
            candidateType.value = candidateType.value.filter(
                (candidate) => candidate !== otherValue.value,
            );
        }
    } else {
        candidateType.value.push(e.target.value);
        if (e.target.value === "Open to All Disabalities") {
            candidateType.value = candidateType.value.filter((candidate) =>
                [
                    "PWD",
                    "Seniors Citizens",
                    "Open to All Disabalities",
                ].includes(candidate),
            );
        }
    }
}

let otherInput = useTemplateRef("otherInput");
let otherValue = ref("");

let skillInput = useTemplateRef("skillInput");
let skills = ref(form.skills ?? []);
if (props.jobPostProp?.skills) {
    skills.value = props.jobPostProp?.skills.map((skill) => ({
        id: nanoid(),
        name: skill,
    }));
}

function addSkill() {
    if (skillInput.value.value) {
        skills.value.push({ id: nanoid(), name: skillInput.value.value });
        skillInput.value.value = "";
    }
}

function removeSkill(skill) {
    skills.value = skills.value.filter((s) => s.id !== skill.id);
}

const submit = () => {
    if (candidateType.value.includes("Other")) {
        candidateType.value[candidateType.value.indexOf("Other")] =
            otherInput.value.value;
        otherValue.value = otherInput.value.value;
    }
    form.skills = skills.value;
    form.preferred_worker_types = candidateType.value;
    if (!route().params.jobid) {
        form.post(route("create.job.post"));
    } else {
        form.put(
            route("employer.jobpost.update", { jobid: route().params.jobid }),
        );
    }
};
</script>

<template>
    <Head title="Create Job | iCan Careers" />
    <div
        class="flex min-h-screen justify-center bg-[#eff2f6] py-8 text-[#171816]"
    >
        <div
            class="omd:flex-row flex w-full max-w-5xl flex-col rounded-lg bg-white p-8 shadow-lg"
        >
            <!-- 🚫 Show this if posting limit reached -->
            <div
                v-if="!canPost && !isEdit"
                class="mb-8 flex w-full flex-col items-center justify-center rounded-lg border-2 border-red-200 bg-gradient-to-br from-red-50 to-orange-50 p-8 shadow-lg"
            >
                <div class="flex flex-col items-center space-y-6 text-center">
                    <!-- Icon Section -->
                    <div class="flex flex-col items-center space-y-3">
                        <div class="rounded-full bg-red-100 p-4">
                            <i
                                class="bi bi-exclamation-triangle-fill text-4xl text-red-600"
                            ></i>
                        </div>
                        <h2 class="text-2xl font-bold text-red-700">
                            Job Post Limit Reached
                        </h2>
                    </div>

                    <!-- Message Section -->
                    <div class="space-y-2">
                        <p class="text-lg text-gray-800">
                            You've used all
                            <span class="font-bold text-red-600">{{ limit }}</span>
                            available job posts for this month.
                        </p>
                        <p class="text-gray-700">
                            You have
                            <span class="font-semibold">{{ remaining }}</span>
                            posts remaining of your
                            <span class="font-semibold">{{ limit }}</span>
                            monthly limit.
                        </p>
                    </div>

                    <!-- Solution Section -->
                    <div
                        class="max-w-md rounded-lg border border-gray-200 bg-white p-4"
                    >
                        <p class="mb-3 text-gray-700">
                            To post more jobs, consider:
                        </p>
                        <ul class="space-y-2 text-left text-gray-600">
                            <li class="flex items-center">
                                <i
                                    class="bi bi-arrow-up-circle-fill mr-2 text-green-500"
                                ></i>
                                <span>Upgrading your subscription plan</span>
                            </li>
                            <li class="flex items-center">
                                <i
                                    class="bi bi-calendar-check-fill mr-2 text-blue-500"
                                ></i>
                                <span>Waiting until next month's reset</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col gap-4 pt-2 sm:flex-row">
                        <Link
                            :href="route('pricing')"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-3 font-semibold text-white transition-all hover:from-blue-700 hover:to-blue-800 hover:shadow-md"
                        >
                            <i class="bi bi-graph-up-arrow"></i>
                            Upgrade Plan
                        </Link>

                        <button
                            @click="$inertia.visit(route('employer.dashboard'))"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-3 font-semibold text-gray-700 transition-all hover:bg-gray-50 hover:shadow-md"
                        >
                            <i class="bi bi-arrow-left"></i>
                            Return to Dashboard
                        </button>
                    </div>

                    <!-- Additional Info -->
                    <div class="mt-4 text-sm text-gray-500">
                        <p>
                            Your posting limit will reset on the first day of
                            next month.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Content - Only show when canPost is true -->
            <div v-else class="w-full">
                <!-- Header Section -->
                <div class="mb-8 text-center md:text-left">
                    <h2
                        class="bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-3xl font-bold text-transparent"
                    >
                        {{
                            isEdit ? "Edit Job Posting" : "Create New Job Post"
                        }}
                    </h2>
                    <p class="mt-2 text-lg text-gray-600">
                        {{
                            isEdit
                                ? "Update your job opportunity details below."
                                : "Fill out the form below to post a new job opportunity."
                        }}
                    </p>

                    <!-- Progress Indicator -->
                    <div v-if="!isEdit" class="mt-4 flex items-center text-sm text-gray-500">
                        <span
                            class="rounded-full bg-green-100 px-3 py-1 font-medium text-green-800"
                        >
                            {{ remaining }} of {{ limit }} posts remaining this
                            month
                        </span>
                    </div>
                </div>

                <hr class="my-6 border-gray-200" />

                <form @submit.prevent="submit" class="space-y-8">
                    <!-- Job Information Section -->
                    <div>
                        <h3 class="mb-3 text-xl font-bold text-gray-900">
                            Job Information
                        </h3>
                        <p class="mb-6 text-gray-600">
                            This information will be displayed publicly so be
                            careful what you share.
                        </p>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Job Title -->
                            <div class="flex flex-col">
                                <label class="mb-2 font-semibold text-gray-700"
                                    >Job Title</label
                                >
                                <input
                                    v-model="form.job_title"
                                    type="text"
                                    class="rounded-lg border border-gray-300 px-4 py-3 outline-none transition-all focus:border-transparent focus:ring-2 focus:ring-blue-500"
                                    placeholder="Enter job title"
                                    required
                                />
                                <InputFlashMessage
                                    type="error"
                                    :message="form.errors.job_title"
                                />
                            </div>

                            <!-- Job Type -->
                            <div class="flex flex-col">
                                <label class="mb-2 font-semibold text-gray-700"
                                    >Job Type</label
                                >
                                <select
                                    v-model="form.job_type"
                                    class="rounded-lg border border-gray-300 px-4 py-3 outline-none transition-all focus:border-transparent focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="Full time">Full time</option>
                                    <option value="Part time">Part time</option>
                                    <option value="Contract">Contract</option>
                                </select>
                                <InputFlashMessage
                                    type="error"
                                    :message="form.errors.job_type"
                                />
                            </div>
                        </div>
                    </div>

                    <hr class="my-6 border-gray-200" />

                    <!-- Work Details Section -->
                    <div>
                        <h3 class="mb-3 text-xl font-bold text-gray-900">
                            Work Details
                        </h3>

                        <!-- Work Arrangement -->
                        <div class="mb-6 flex w-full flex-col">
                            <label class="mb-2 font-semibold text-gray-700"
                                >Work Arrangement</label
                            >
                            <select
                                v-model="form.work_arrangement"
                                class="max-w-md rounded-lg border border-gray-300 px-4 py-3 outline-none transition-all focus:border-transparent focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="Onsite">Onsite</option>
                                <option value="Hybrid">Hybrid</option>
                                <option value="Remote">Remote</option>
                            </select>
                            <InputFlashMessage
                                type="error"
                                :message="form.errors.work_arrangement"
                            />
                            <Maps
                                v-if="isMapShow"
                                :markedCoordinatesProps="form.location"
                                :centerProps="form.location"
                                @update:coordinates="setCoordinates"
                                class="mt-4"
                            />
                        </div>

                        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Preferred Experience -->
                            <div class="flex flex-col">
                                <label class="mb-2 font-semibold text-gray-700"
                                    >Preferred Experience</label
                                >
                                <select
                                    v-model="form.experience"
                                    class="rounded-lg border border-gray-300 px-4 py-3 outline-none transition-all focus:border-transparent focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="Fresher">Fresher</option>
                                    <option value="0-2 years">0-2 years</option>
                                    <option value="2-4 years">2-4 years</option>
                                    <option value="5+ years">5+ years</option>
                                </select>
                                <InputFlashMessage
                                    type="error"
                                    :message="form.errors.experience"
                                />
                            </div>

                            <!-- Preferred Educational Attainment -->
                            <div class="flex flex-col">
                                <label class="mb-2 font-semibold text-gray-700"
                                    >Preferred Educational Attainment</label
                                >
                                <EducationalAttainment
                                    v-model="
                                        form.preferred_educational_attainment
                                    "
                                    :error="
                                        form.errors
                                            .preferred_educational_attainment
                                    "
                                    :openToAll="true"
                                    class="w-full"
                                />
                            </div>
                        </div>

                        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-3">
                            <!-- Hour Per Day -->
                            <div class="flex flex-col">
                                <label class="mb-2 font-semibold text-gray-700"
                                    >Hour Per Day</label
                                >
                                <input
                                    v-model.number="form.hour_per_day"
                                    type="number"
                                    min="1"
                                    max="12"
                                    class="rounded-lg border border-gray-300 px-4 py-3 outline-none transition-all focus:border-transparent focus:ring-2 focus:ring-blue-500"
                                    placeholder="Enter hours per day (max 12)"
                                    required
                                />
                                <small class="mt-2 text-xs text-gray-500">
                                    Maximum of 12 hours per day is allowed.
                                </small>
                            </div>

                            <!-- Hourly Rate -->
                            <div class="flex flex-col">
                                <label class="mb-2 font-semibold text-gray-700"
                                    >Hourly Rate ₱</label
                                >
                                <input
                                    v-model.number="form.hourly_rate"
                                    type="number"
                                    min="1"
                                    class="rounded-lg border border-gray-300 px-4 py-3 outline-none transition-all focus:border-transparent focus:ring-2 focus:ring-blue-500"
                                    placeholder="Enter hourly rate"
                                    required
                                />
                                <small class="mt-2 text-xs text-gray-500">
                                    Example: ₱100 per hour.
                                </small>
                            </div>

                            <!-- Salary Per Month -->
                            <div class="flex flex-col">
                                <label class="mb-2 font-semibold text-gray-700"
                                    >Salary Per Month ₱</label
                                >
                                <input
                                    v-model="form.salary"
                                    type="number"
                                    class="rounded-lg border border-gray-300 bg-gray-50 px-4 py-3 outline-none transition-all"
                                    placeholder="Auto calculated"
                                    readonly
                                />
                                <small class="mt-2 text-xs text-gray-500">
                                    Automatically calculated based on hours ×
                                    rate × 22 working days.
                                </small>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-6 flex flex-col">
                            <label class="mb-2 font-semibold text-gray-700"
                                >Description</label
                            >
                            <textarea
                                v-model="form.description"
                                class="h-48 resize-none rounded-lg border border-gray-300 px-4 py-3 outline-none transition-all focus:border-transparent focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter job description"
                                required
                            ></textarea>
                            <InputFlashMessage
                                type="error"
                                :message="form.errors.description"
                            />
                        </div>

                        <!-- Required Skills -->
                        <div class="mb-6 flex flex-col">
                            <label class="font-semibold text-gray-700"
                                >Required Skills</label
                            >
                            <label class="mb-3 text-sm text-gray-600"
                                >Add the skills required for this job.</label
                            >
                            <div class="mb-3 flex gap-3">
                                <input
                                    ref="skillInput"
                                    type="text"
                                    class="flex-1 rounded-lg border border-gray-300 px-4 py-2 outline-none transition-all focus:border-transparent focus:ring-2 focus:ring-blue-500"
                                    placeholder="Enter skill"
                                />
                                <button
                                    @click="addSkill"
                                    type="button"
                                    class="rounded-lg bg-[#fa8334] px-6 py-2 font-semibold text-white transition-all hover:bg-[#e97324] focus:ring-2 focus:ring-orange-500"
                                >
                                    Add
                                </button>
                            </div>
                            <div
                                class="flex min-h-20 flex-wrap items-start gap-2 rounded-lg border border-gray-300 p-4"
                            >
                                <div
                                    v-for="skill in skills"
                                    :key="skill.id"
                                    class="flex items-center gap-2 rounded-full bg-blue-100 px-3 py-1"
                                >
                                    <span class="text-sm text-blue-800">{{
                                        skill.name
                                    }}</span>
                                    <button
                                        @click="removeSkill(skill)"
                                        type="button"
                                        class="text-blue-600 transition-colors hover:text-blue-800"
                                    >
                                        <i class="bi bi-x text-sm"></i>
                                    </button>
                                </div>
                            </div>
                            <InputFlashMessage
                                type="error"
                                :message="form.errors.skills"
                            />
                        </div>

                        <!-- Candidate Type Options -->
                        <div class="mb-8 flex flex-col">
                            <label class="font-semibold text-gray-700"
                                >Candidate Type Options:</label
                            >
                            <label class="mb-3 text-sm text-gray-600"
                                >Select all that apply.</label
                            >

                            <div class="space-y-4 rounded-lg bg-gray-50 p-4">
                                <div class="space-y-3">
                                    <!-- Seniors Citizens -->
                                    <div class="flex items-center">
                                        <input
                                            @change="addCandidateType"
                                            value="Seniors Citizens"
                                            type="checkbox"
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        />
                                        <label class="ml-3 text-gray-700"
                                            >Seniors Citizens</label
                                        >
                                    </div>

                                    <!-- PWD -->
                                    <div class="flex items-center">
                                        <input
                                            @change="
                                                check();
                                                addCandidateType($event);
                                            "
                                            value="PWD"
                                            type="checkbox"
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        />
                                        <label class="ml-3 text-gray-700"
                                            >PWD (Persons with
                                            Disabilities)</label
                                        >
                                    </div>

                                    <!-- PWD Disability Types (shown when PWD is checked) -->
                                    <div
                                        v-if="isCheck"
                                        class="ml-6 space-y-2 border-l-2 border-gray-300 pl-4"
                                    >
                                        <div
                                            v-if="!isOpenToAllDisability"
                                            class="space-y-2"
                                        >
                                            <div class="flex items-center">
                                                <input
                                                    @change="addCandidateType"
                                                    type="checkbox"
                                                    value="Physical Disabilities"
                                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                />
                                                <label
                                                    class="ml-3 text-gray-700"
                                                    >Physical
                                                    Disabilities</label
                                                >
                                            </div>
                                            <div class="flex items-center">
                                                <input
                                                    @change="addCandidateType"
                                                    type="checkbox"
                                                    value="Visual Impairments"
                                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                />
                                                <label
                                                    class="ml-3 text-gray-700"
                                                    >Visual Impairments</label
                                                >
                                            </div>
                                            <div class="flex items-center">
                                                <input
                                                    @change="addCandidateType"
                                                    type="checkbox"
                                                    value="Hearing Impairments"
                                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                />
                                                <label
                                                    class="ml-3 text-gray-700"
                                                    >Hearing Impairments</label
                                                >
                                            </div>
                                            <div class="flex items-center">
                                                <input
                                                    @change="addCandidateType"
                                                    type="checkbox"
                                                    value="Cognitive/Developmental Disabilities"
                                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                />
                                                <label
                                                    class="ml-3 text-gray-700"
                                                    >Cognitive/Developmental
                                                    Disabilities</label
                                                >
                                            </div>
                                            <div class="flex items-center">
                                                <input
                                                    @change="addCandidateType"
                                                    type="checkbox"
                                                    value="Mental Health Disabilities"
                                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                />
                                                <label
                                                    class="ml-3 text-gray-700"
                                                    >Mental Health
                                                    Disabilities</label
                                                >
                                            </div>
                                            <div class="flex items-center">
                                                <input
                                                    @change="
                                                        otherCheck();
                                                        addCandidateType(
                                                            $event,
                                                        );
                                                    "
                                                    type="checkbox"
                                                    value="Other"
                                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                />
                                                <label
                                                    class="ml-3 text-gray-700"
                                                    >Other</label
                                                >
                                            </div>
                                            <div
                                                v-if="isOtherCheck"
                                                class="ml-6 mt-2"
                                            >
                                                <input
                                                    ref="otherInput"
                                                    type="text"
                                                    placeholder="Please specify disability type"
                                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 outline-none focus:border-transparent focus:ring-2 focus:ring-blue-500"
                                                    required
                                                />
                                            </div>
                                        </div>

                                        <!-- Open to All Disabilities -->
                                        <div
                                            class="mt-3 flex items-center border-t border-gray-300 pt-3"
                                        >
                                            <input
                                                @change="
                                                    openToAll();
                                                    addCandidateType($event);
                                                "
                                                type="checkbox"
                                                value="Open to All Disabilities"
                                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            />
                                            <label
                                                class="ml-3 font-medium text-gray-700"
                                                >Open to All Disabilities</label
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <InputFlashMessage
                                type="error"
                                :message="form.errors.preferred_worker_types"
                            />
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div
                        class="flex justify-end gap-4 border-t border-gray-200 pt-6"
                    >
                        <button
                            type="button"
                            @click="$inertia.visit(route('employer.dashboard'))"
                            class="rounded-lg border border-gray-300 px-8 py-3 font-semibold text-gray-700 transition-all hover:bg-gray-50 focus:ring-2 focus:ring-gray-500"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="rounded-lg bg-[#fa8334] px-8 py-3 font-semibold text-white transition-all hover:bg-[#e97324] focus:ring-2 focus:ring-orange-500"
                        >
                            {{ isEdit ? "Update Job" : "Post Job" }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <SuccessfulMessage
        :messageProp="$page.props.errors.message"
        :messageShow="$page.props.errors.message"
        type="Error"
    ></SuccessfulMessage>
</template>

<style>
/* Add any custom styles here */
</style>