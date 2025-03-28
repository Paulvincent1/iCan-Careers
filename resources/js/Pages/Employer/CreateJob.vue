<script setup>
import { useForm } from "@inertiajs/vue3";
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
    <div class="flex justify-center bg-[#eff2f6] text-[#171816]">
        <div
            class="omd:flex-row mt-8 flex w-full max-w-5xl flex-col rounded-lg bg-white p-8"
        >
            <!-- Left Side: Job Post Form -->
            <div class="w-full pr-6">
                <h2
                    class="text-center text-3xl font-bold text-gray-900 md:text-left"
                >
                    {{ isEdit ? "Edit a Job" : "Post a Job" }}
                </h2>
                <p class="mb-6 text-center text-lg text-gray-700 md:text-left">
                    Fill out the form below to post a new job opportunity.
                </p>

                <hr class="my-4 border-gray-300" />
                <h3 class="text-xl font-bold text-gray-900">Job Information</h3>
                <h3>This information will be displayed publicly so be careful what you share.</h3>
                <form @submit.prevent="submit">

                    <div class="space-y-6 mt-4">

                        <!-- Job Title -->
                        <div class="grid grid-cols-2 gap-5">
                            <div class="flex flex-col">
                                <label class="mb-2 font-semibold"
                                    >Job Title</label
                                >
                                <input
                                    v-model="form.job_title"
                                    type="text"
                                    class="rounded border px-3 py-2 outline-blue-400"
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
                                <label class="mb-2 font-semibold"
                                    >Job Type</label
                                >
                                <select
                                    v-model="form.job_type"
                                    class="rounded border px-3 py-2 outline-blue-400"
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

                        <hr class="my-4 border-gray-300" />
                        <h3 class="text-xl font-bold text-gray-900">Work Details</h3>
                        <!-- Work Arrangement -->
                        <div class="flex w-full flex-col">
                            <label class="mb-2 font-semibold"
                                >Work Arrangement</label
                            >
                            <select
                                v-model="form.work_arrangement"
                                class="rounded border px-3 py-2 outline-blue-400"
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
                            />
                        </div>

                        <div class="flex flex-col gap-4">
                            <!-- Preferred Experience -->
                            <div class="flex flex-col">
                                <label class="mb-2 font-semibold"
                                    >Preferred Experience</label
                                >
                                <select
                                    v-model="form.experience"
                                    class="w-full max-w-[350px] rounded border px-3 py-2 outline-blue-400 md:w-[350px]"
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
                                <label class="mb-2 font-semibold"
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
                                    class="w-full max-w-[350px] md:w-[350px]"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- Hour Per Day -->
                            <div class="flex flex-col">
                                <label class="mb-2 font-semibold"
                                    >Hour Per Day</label
                                >
                                <input
                                    v-model="form.hour_per_day"
                                    type="number"
                                    min="1"
                                    class="rounded border px-3 py-2 outline-blue-400"
                                    placeholder="Enter hours per day"
                                    required
                                />
                                <InputFlashMessage
                                    type="error"
                                    :message="form.errors.hour_per_day"
                                />
                            </div>

                            <!-- Hourly Rate -->
                            <div class="flex flex-col">
                                <label class="mb-2 font-semibold"
                                    >Hourly Rate ₱</label
                                >
                                <input
                                    v-model="form.hourly_rate"
                                    type="number"
                                    min="1"
                                    class="rounded border px-3 py-2 outline-blue-400"
                                    placeholder="Enter hourly rate"
                                    required
                                />
                                <InputFlashMessage
                                    type="error"
                                    :message="form.errors.hourly_rate"
                                />
                            </div>

                            <!-- Salary Per Month -->
                            <div class="flex flex-col">
                                <label class="mb-2 font-semibold"
                                    >Salary Per Month ₱</label
                                >
                                <input
                                    v-model="form.salary"
                                    type="number"
                                    min="1"
                                    class="rounded border px-3 py-2 outline-blue-400"
                                    placeholder="Enter salary per month"
                                    required
                                />
                                <InputFlashMessage
                                    type="error"
                                    :message="form.errors.salary"
                                />
                            </div>
                        </div>


                        <!-- Description -->
                        <div class="flex flex-col">
                            <label class="mb-2 font-semibold"
                                >Description</label
                            >
                            <textarea
                                v-model="form.description"
                                class="resize-none h-[250px] rounded border px-3 py-2 outline-blue-400"
                                placeholder="Enter job description"
                                required
                            ></textarea>
                            <InputFlashMessage
                                type="error"
                                :message="form.errors.description"
                            />
                        </div>

                        <!-- Required Skills -->
                        <div class="flex flex-col">
                            <label class="font-semibold">Required Skills</label>
                            <label class="mb-2 text-xs"
                                >Add the skills required for this job.</label
                            >
                            <div class="mb-3">
                                <input
                                    ref="skillInput"
                                    type="text"
                                    class="mr-3 rounded border px-3 py-2 outline-blue-400"
                                    placeholder="Enter skill"
                                />
                                <input
                                    @click="addSkill"
                                    class="cursor-pointer rounded bg-[#fa8334] p-1 text-white"
                                    type="button"
                                    value="Add"
                                />
                            </div>
                            <div
                                class="flex min-h-20 flex-wrap items-start justify-start gap-1 rounded border p-2"
                            >
                                <div
                                    v-for="skill in skills"
                                    :key="skill.id"
                                    class="flex w-fit rounded bg-gray-300 p-1"
                                >
                                    <p>{{ skill.name }}</p>
                                    <i
                                        @click="removeSkill(skill)"
                                        class="bi bi-x ml-1 cursor-pointer"
                                    ></i>
                                </div>
                            </div>
                            <InputFlashMessage
                                type="error"
                                :message="form.errors.skills"
                            />
                        </div>

                        <hr class="my-4 border-gray-300" />
                        <!-- Candidate Type Options -->
                        <div class="flex flex-col">
                            <label class="font-semibold"
                                >Candidate Type Options:</label
                            >
                            <label class="mb-2 text-xs"
                                >Select all that apply.</label
                            >
                            <div class="space-y-2">
                                <div>
                                    <label>Seniors</label>
                                    <input
                                        @change="addCandidateType"
                                        value="Seniors Citizens"
                                        type="checkbox"
                                        class="ml-2"
                                    />
                                </div>
                                <div>
                                    <label>PWD</label>
                                    <input
                                        @change="
                                            check();
                                            addCandidateType($event);
                                        "
                                        value="PWD"
                                        type="checkbox"
                                        class="ml-2"
                                    />
                                </div>
                                <div v-if="isCheck">
                                    <div v-if="!isOpenToAllDisability">
                                        <div>
                                            <label>Physical Disabilities</label>
                                            <input
                                                @change="addCandidateType"
                                                type="checkbox"
                                                value="Physical Disabilities"
                                                class="ml-2"
                                            />
                                        </div>
                                        <div>
                                            <label>Visual Impairments</label>
                                            <input
                                                @change="addCandidateType"
                                                type="checkbox"
                                                value="Visual Impairments"
                                                class="ml-2"
                                            />
                                        </div>
                                        <div>
                                            <label>Hearing Impairments</label>
                                            <input
                                                @change="addCandidateType"
                                                type="checkbox"
                                                value="Hearing Impairments"
                                                class="ml-2"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                >Cognitive/Developmental
                                                Disabilities</label
                                            >
                                            <input
                                                @change="addCandidateType"
                                                type="checkbox"
                                                value="Cognitive/Developmental Disabilities"
                                                class="ml-2"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                >Mental Health
                                                Disabilities</label
                                            >
                                            <input
                                                @change="addCandidateType"
                                                type="checkbox"
                                                value="Mental Health Disabilities"
                                                class="ml-2"
                                            />
                                        </div>
                                        <div>
                                            <label>Other</label>
                                            <input
                                                @change="
                                                    otherCheck();
                                                    addCandidateType($event);
                                                "
                                                type="checkbox"
                                                value="Other"
                                                class="ml-2"
                                            />
                                            <br />
                                            <input
                                                v-if="isOtherCheck"
                                                ref="otherInput"
                                                type="text"
                                                placeholder="Please Specify"
                                                class="mt-2 border-b outline-none"
                                                required
                                            />
                                        </div>
                                    </div>
                                    <div>
                                        <label>Open to All Disabilities</label>
                                        <input
                                            @change="
                                                openToAll();
                                                addCandidateType($event);
                                            "
                                            type="checkbox"
                                            value="Open to All Disabilities"
                                            class="ml-2"
                                        />
                                    </div>
                                </div>
                            </div>
                            <InputFlashMessage
                                type="error"
                                :message="form.errors.preferred_worker_types"
                            />
                        </div>

                        <div class="mt-4 flex justify-end gap-3">
                            <!-- Submit Button -->
                            <div
                                type="button"
                                @click="
                                    $inertia.visit(route('employer.dashboard'))
                                "
                                class="mt-4 flex justify-end"
                            >
                                <button
                                    class="cursor-pointer rounded p-2 text-black"
                                >
                                    Cancel
                                </button>
                            </div>

                            <!-- Submit Button -->
                            <div class="mt-4 flex justify-end">
                                <button
                                    class="cursor-pointer rounded bg-[#fa8334] p-2 text-white"
                                >
                                    Post Job
                                </button>
                            </div>
                        </div>
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
