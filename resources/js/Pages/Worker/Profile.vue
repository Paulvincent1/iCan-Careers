<script setup>
import { computed, getCurrentInstance, ref, watch, watchEffect, onMounted } from "vue";
import Skill from "../Components/Skill.vue";
import dayjs from "dayjs";
import { Link, router } from "@inertiajs/vue3";
import AddSkillModal from "../Components/Modal/AddSkillModal.vue";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import InputFlashMessage from "../Components/InputFlashMessage.vue";
import ReusableModal from "../Components/Modal/ReusableModal.vue";
import ProfilePage from "../Components/Reviews/ProfilePage.vue";
import EducationalAttainment from "../Components/EducationalAttainment.vue";
import GenderSelect from "../Components/GenderSelect.vue";

let props = defineProps({
    userProp: Object,
    workerSkillsProp: Object,
    workerProfileProp: Object,
    workerBasicInfoProp: null,
    messageProp: String,
    visitor: null,
    isPending: null,
    currentlyEmployedByMeProp: null,
    adminVisit: null,
    appliedJobsProps: {
        type: Array,
        default: () => [],
    },
    averageStar: null,
    recentReview: null,
});

let { appContext } = getCurrentInstance();
let formatCurrency = appContext.config.globalProperties.formatCurrency;

// State Management
let messageShow = ref(false);
let isEditProfileActive = ref(false);
let isEditJobTitleActive = ref(false);
let isEditDescription = ref(false);
let isEditEducationActive = ref(false);
let isEditBasicInfoActive = ref(false);
let isOpen = ref(false);
let isShowContractModal = ref(false);
let isShowReportModal = ref(false);
let showAllJobs = ref(false);
let disable = ref(false);
let selectedContract = ref("");
let inputResumeError = ref("");
let reasonSelected = ref("");
let descriptions = ref([]);

// Profile Data
let workerSkills = ref(props.workerSkillsProp);
let workerProfile = ref(props.workerProfileProp);
let profilePreview = ref(props.userProp.profile_img);
let highestEducation = ref(workerProfile.value.highest_educational_attainment);

// Basic Information - Use props directly for display
let websiteLink = ref(props.workerBasicInfoProp?.website_link ?? "N/A");
let address = ref(props.workerBasicInfoProp?.address ?? "N/A");

// Basic Information Form for editing
let basicInfoForm = ref({
    birthYear: props.workerProfileProp.birth_year,
    gender: props.workerProfileProp.gender,
    address: props.workerBasicInfoProp?.address ?? "",
    websiteLink: props.workerBasicInfoProp?.website_link ?? ""
});

// Computed Properties
const memberSince = computed(() => {
    return dayjs(props.userProp.created_at).format("MMMM DD, YYYY");
});

const age = computed(() => {
    return workerProfile.value.birth_year ? dayjs().format("YYYY") - workerProfile.value.birth_year : "N/A";
});

const displayedJobs = computed(() => {
    return showAllJobs.value ? props.appliedJobsProps : props.appliedJobsProps.slice(0, 3);
});

const hasMoreJobs = computed(() => {
    return props.appliedJobsProps.length > 3;
});

const ageValidation = computed(() => {
    const birthYear = basicInfoForm.value.birthYear;
    if (!birthYear) return { valid: false, message: "Please enter birth year" };
    if (birthYear < 1900) return { valid: false, message: "Birth year cannot be before 1900" };
    if (birthYear > 2007) return { valid: false, message: "Birth year cannot be after 2007" };

    const calculatedAge = dayjs().format("YYYY") - birthYear;
    if (calculatedAge < 18) return { valid: false, message: "Must be at least 18 years old" };

    return { valid: true, message: `Age: ${calculatedAge} years old` };
});

// Watch Effects
watchEffect(() => {
    workerSkills.value = props.workerSkillsProp;
    showSuccessMessage();
});

watch(
    () => [workerProfile.value.work_hour_per_day, workerProfile.value.hour_pay],
    ([hours, rate]) => {
        if (hours && rate) {
            workerProfile.value.month_pay = (hours * rate * 22).toFixed(2);
        } else {
            workerProfile.value.month_pay = null;
        }
    },
);

// Lifecycle
onMounted(() => {
    if (props.messageProp) {
        showSuccessMessage();
    }
});

// Utility Functions
function showSuccessMessage() {
    if (props.messageProp) {
        messageShow.value = true;
        setTimeout(() => {
            messageShow.value = false;
        }, 2000);
    }
}

function isOpenUpdateValue(e) {
    isOpen.value = e;
}

function validateWorkHours() {
    if (workerProfile.value.work_hour_per_day > 12) {
        workerProfile.value.work_hour_per_day = 12;
    }
    if (workerProfile.value.work_hour_per_day < 1) {
        workerProfile.value.work_hour_per_day = 1;
    }
}

function toggleShowMore() {
    showAllJobs.value = !showAllJobs.value;
}

// Profile Update Functions
function updateJobTitle() {
    router.put(
        "/jobseekers/myprofile/updateprofile",
        { job_title: workerProfile.value.job_title },
        { onSuccess: showSuccessMessage, preserveScroll: true },
    );
}

function updateWorkDetails() {
    validateWorkHours();

    if (workerProfile.value.work_hour_per_day && workerProfile.value.hour_pay) {
        workerProfile.value.month_pay = (
            workerProfile.value.work_hour_per_day *
            workerProfile.value.hour_pay *
            22
        ).toFixed(2);
    }

    router.put(
        "/jobseekers/myprofile/updateprofile",
        {
            job_type: workerProfile.value.job_type,
            work_hour_per_day: workerProfile.value.work_hour_per_day,
            hour_pay: workerProfile.value.hour_pay,
            month_pay: workerProfile.value.month_pay,
        },
        { onSuccess: showSuccessMessage, preserveScroll: true },
    );
}

function updateDescription() {
    router.put(
        "/jobseekers/myprofile/updateprofile",
        { profile_description: workerProfile.value.profile_description },
        { onSuccess: showSuccessMessage, preserveScroll: true },
    );
}

function uploadProfileImage(e) {
    profilePreview.value = URL.createObjectURL(e.target.files[0]);
    router.post(
        "/jobseekers/myprofile/updateprofile",
        {
            _method: "put",
            profile_img: e.target.files[0],
        },
        { onSuccess: showSuccessMessage, preserveScroll: true },
    );
}

// Education Update Function
function updateEducation() {
    if (highestEducation.value) {
        router.put(
            "/jobseekers/myprofile/updateprofile",
            { highest_educational_attainment: highestEducation.value },
            {
                onSuccess: () => {
                    showSuccessMessage();
                    isEditEducationActive.value = false;
                    workerProfile.value.highest_educational_attainment = highestEducation.value;
                },
                preserveScroll: true,
            },
        );
    }
}

// Basic Information Functions
function updateBasicInfo() {
    if (!ageValidation.value.valid) {
        alert(ageValidation.value.message);
        return;
    }

    // Prepare all data for profile update
    const updateData = {
        birth_year: basicInfoForm.value.birthYear,
        gender: basicInfoForm.value.gender,
        address: basicInfoForm.value.address || null,
        website_link: basicInfoForm.value.websiteLink || null
    };

    router.put(
        "/jobseekers/myprofile/updateprofile",
        updateData,
        {
            onSuccess: () => {
                showSuccessMessage();
                isEditBasicInfoActive.value = false;

                // Update local state
                workerProfile.value.birth_year = basicInfoForm.value.birthYear;
                workerProfile.value.gender = basicInfoForm.value.gender;

                // Update address and website display
                address.value = basicInfoForm.value.address || "N/A";
                websiteLink.value = basicInfoForm.value.websiteLink || "N/A";
            },
            preserveScroll: true,
        },
    );
}

function cancelEditBasicInfo() {
    // Reset form to current values
    basicInfoForm.value = {
        birthYear: workerProfile.value.birth_year,
        gender: workerProfile.value.gender,
        address: props.workerBasicInfoProp?.address ?? "",
        websiteLink: props.workerBasicInfoProp?.website_link ?? ""
    };
    isEditBasicInfoActive.value = false;
}

// Skills Functions
function updateSkillName(skillName, skillId) {
    router.put(
        `/jobseekers/myprofile/updateskill/${skillId}`,
        { skill_name: skillName },
        {
            onStart: () => disable.value = true,
            onSuccess: () => {
                showSuccessMessage();
                workerSkills.value.forEach(e => {
                    if (e.id === skillId) e.skill_name = skillName;
                });
            },
            onFinish: () => disable.value = false,
            preserveScroll: true,
        },
    );
}

function updateExperience(exp, skillId) {
    router.put(
        `/jobseekers/myprofile/updateskill/${skillId}`,
        { experience: exp },
        {
            onStart: () => disable.value = true,
            onSuccess: () => {
                showSuccessMessage();
                workerSkills.value.forEach(e => {
                    if (e.id === skillId) e.experience = exp;
                });
            },
            onFinish: () => disable.value = false,
            preserveScroll: true,
        },
    );
}

function updateStar(star, skillId) {
    router.put(
        `/jobseekers/myprofile/updateskill/${skillId}`,
        { rating: star },
        {
            onStart: () => disable.value = true,
            onSuccess: () => {
                showSuccessMessage();
                workerSkills.value.forEach(e => {
                    if (e.id === skillId) e.rating = star;
                });
            },
            onFinish: () => disable.value = false,
            preserveScroll: true,
        },
    );
}

function removeSkill(skillId) {
    if (!confirm("Are you sure you want to delete this skill?")) return;

    router.delete(
        `/jobseekers/myprofile/deleteskill/${skillId}`,
        {
            onStart: () => disable.value = true,
            onSuccess: () => {
                showSuccessMessage();
                workerSkills.value = workerSkills.value.filter(e => e.id !== skillId);
            },
            onFinish: () => disable.value = false,
            preserveScroll: true,
        },
    );
}

// Resume Function
function updateResume(e) {
    router.post(
        "/jobseekers/myprofile/updateprofile",
        {
            _method: "put",
            resume: e.target.files[0],
        },
        {
            onError: (e) => inputResumeError.value = e.resume,
            onSuccess: () => {
                workerProfile.value = props.workerProfileProp;
                showSuccessMessage();
            },
            preserveScroll: true,
            preserveState: true,
        },
    );
}

// Report Functions
const reports = [
    {
        reason: "Work Performance Issues",
        descriptions: [
            "Work Not Delivered", "Missed Deadlines", "Low-Quality Work", "Refusal to Revise Work",
        ],
    },
    {
        reason: "Fraud & Dishonesty",
        descriptions: [
            "Fake Profile or Misrepresentation", "Plagiarized or Stolen Work", "Multiple Accounts", "Scamming the Employer",
        ],
    },
    {
        reason: "Unprofessional Behavior",
        descriptions: [
            "Unresponsive Worker", "Rude or Disrespectful Communication", "Harassment or Threats",
        ],
    },
    {
        reason: "Platform Violations",
        descriptions: [
            "Asking for Off-Platform Payments", "Spamming or Self-Promotion", "Discrimination",
        ],
    },
];

function selectReason(report) {
    reasonSelected.value = report.reason;
    descriptions.value = report.descriptions;
}

function submitReport(reason) {
    router.post(
        route("report.user", { userId: route().params.id }),
        { reason },
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                isShowReportModal.value = false;
                showSuccessMessage();
            },
        },
    );
}

function fireWorker(jobPostId) {
    if (confirm("Are you sure you want to end the contract?")) {
        router.put(
            route("fireworker", { workerId: route().params.id, jobPostId }),
            {},
            {
                preserveScroll: true,
                preserveState: true,
                onSuccess: showSuccessMessage,
            },
        );
    }
}
function isValidUrl(string) {
    try {
        new URL(string);
        return true;
    } catch (_) {
        return false;
    }
}

function formatUrl(url) {
    if (!url) return '';
    // Add https:// if not present
    if (!url.startsWith('http://') && !url.startsWith('https://')) {
        return 'https://' + url;
    }
    return url;
}

function formatUrlDisplay(url) {
    if (!url) return '';
    // Remove protocol and www for display
    return url.replace(/^(https?:\/\/)?(www\.)?/, '');
}
</script>

<template>
    <Head title="Profile | iCan Careers" />
    <div class="min-h-[calc(100vh-4.625rem)] bg-[#f3f7fa]">
        <!-- Header Section -->
        <div class="relative h-32 bg-[#FAFAFA]">
            <div class="absolute right-9 top-4">
                <button
                    v-if="visitor"
                    @click="isShowReportModal = true"
                    class="flex items-center gap-1 rounded-md bg-white px-3 py-2 text-sm font-medium text-red-600 shadow-sm hover:bg-gray-50"
                >
                    <i class="bi bi-exclamation-diamond-fill"></i>
                    <span>Report</span>
                </button>
            </div>
            <label
                for="profileimg"
                :class="[
                    'absolute left-[50%] top-[40px] flex h-36 w-36 translate-x-[-50%] cursor-pointer flex-col items-center',
                    { 'pointer-events-none': visitor },
                ]"
            >
                <div class="relative mb-3 h-full w-full">
                    <img
                        draggable="false"
                        :src="profilePreview || '/assets/profile_placeholder.jpg'"
                        alt="Profile"
                        class="h-full w-full rounded-full border object-cover"
                    />
                    <div
                        v-if="!visitor"
                        class="absolute bottom-1 right-1 flex h-8 w-8 items-center justify-center rounded-full bg-gray-200 shadow-md"
                    >
                        <i class="bi bi-camera text-lg text-gray-600"></i>
                    </div>
                </div>
                <input @change="uploadProfileImage" id="profileimg" type="file" class="hidden" />
            </label>
        </div>

        <!-- Profile Info Section -->
        <div class="bg-white pb-2">
            <div class="xs container mx-auto flex flex-col items-center justify-center pt-16 xl:max-w-7xl">
                <div class="mb-2 flex items-end gap-3">
                    <p class="text-lg">{{ userProp.name }}</p>
                    <Link
                        v-if="visitor"
                        :href="route('messages')"
                        :data="{ user: userProp.id }"
                        class="bi bi-chat-dots text-[30px] text-blue-500 hover:cursor-pointer"
                    ></Link>
                </div>

                <!-- Job Title -->
                <div class="mb-3">
                    <div v-if="!isEditJobTitleActive" class="flex items-center gap-2">
                        <p
                            @click="isEditJobTitleActive = !visitor"
                            :class="[
                                'max-w-[600px] break-words text-center text-[24px] font-bold',
                                { 'cursor-pointer hover:underline': !visitor }
                            ]"
                        >
                            {{ workerProfile.job_title }}
                        </p>
                        <i
                            v-if="!visitor"
                            class="bi bi-pencil-square cursor-pointer text-sm text-gray-500"
                            @click="isEditJobTitleActive = true"
                        ></i>
                    </div>
                    <form v-if="isEditJobTitleActive" @submit.prevent="updateJobTitle(); isEditJobTitleActive = false;">
                        <input
                            type="text"
                            v-model="workerProfile.job_title"
                            class="mr-2 rounded border p-1 outline-none ring-orange-300 transition-all hover:ring-1 focus:ring-1"
                            required
                        />
                        <button class="rounded bg-orange-500 p-1 text-white">Save</button>
                    </form>
                </div>

                <!-- Verification Status -->
                <div v-if="!$page.props.auth.worker_verified && !visitor" class="flex flex-col items-center">
                    <p class="mb-3 text-center text-orange-600">
                        Please verify your account to apply for jobs!
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </p>
                    <Link
                        :href="route('account.verify')"
                        as="button"
                        class="w-fit rounded-lg border bg-orange-500 px-3 py-2 font-bold text-white"
                    >
                        Verify Now!
                    </Link>
                </div>

                <div v-if="$page.props.auth.user.authenticated.verified" class="flex items-center gap-1">
                    <p class="text-sm font-bold text-gray-600">Verified</p>
                    <i class="bi bi-patch-check-fill text-green-400"></i>
                </div>

                <div v-if="isPending">
                    <p class="text-yellow-400">{{ isPending }}</p>
                </div>

                <!-- Manage Contracts Button -->
                <div v-if="currentlyEmployedByMeProp?.length > 0" class="my-4 flex justify-center">
                    <button
                        @click="isShowContractModal = true"
                        class="rounded-md bg-red-500 px-4 py-2 font-bold text-white shadow hover:bg-red-600"
                    >
                        Manage Contracts
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="xs container mx-auto px-[0.5rem] md:px-[3rem] lg:px-[9rem] xl:max-w-7xl">
            <div class="mt-8 grid gap-6 md:grid-cols-[1fr,250px] lg:grid-cols-[1fr,300px]">
                <!-- Left Column -->
                <div class="flex flex-col gap-4 text-[16px] text-gray-600">
                    <!-- Work Details -->
                    <div class="rounded-lg bg-white p-8">
                        <div class="mb-4 flex items-center gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-200">
                                <i class="bi bi-bag font-bold"></i>
                            </div>
                            <div v-if="!isEditProfileActive" class="flex items-center gap-2">
                                <p
                                    @click="isEditProfileActive = !visitor"
                                    :class="[
                                        'max-w-[400px]',
                                        { 'cursor-pointer hover:border-b-2': !visitor }
                                    ]"
                                >
                                    Looking for {{ workerProfile.job_type }} work
                                    ({{ workerProfile.work_hour_per_day }} hours/day) at
                                    {{ formatCurrency(workerProfile.hour_pay) }}/hour
                                    ({{ formatCurrency(workerProfile.month_pay) }}/month)
                                </p>
                                <i
                                    v-if="!visitor"
                                    class="bi bi-pencil-square cursor-pointer text-sm text-gray-500"
                                    @click="isEditProfileActive = true"
                                ></i>
                            </div>
                            <form v-if="isEditProfileActive" @submit.prevent="updateWorkDetails(); isEditProfileActive = false;" class="space-y-4">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Job Type</label>
                                    <select v-model="workerProfile.job_type" class="w-full rounded border px-3 py-2 outline-none focus:ring-2 focus:ring-orange-400">
                                        <option value="Full-time">Full-time</option>
                                        <option value="Part-time">Part-time</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Work Hours per Day (Max: 12 hours)</label>
                                    <input
                                        v-model.number="workerProfile.work_hour_per_day"
                                        type="number" min="1" max="12" @input="validateWorkHours"
                                        class="w-full rounded border px-3 py-2 outline-none focus:ring-2 focus:ring-orange-400"
                                        placeholder="e.g. 8 hours" required
                                    />
                                    <small class="mt-1 text-xs text-gray-500">Maximum of 12 hours per day is allowed.</small>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Hourly Pay (₱)</label>
                                    <input
                                        v-model.number="workerProfile.hour_pay" type="number" min="1" step="1"
                                        class="w-full rounded border px-3 py-2 outline-none focus:ring-2 focus:ring-orange-400"
                                        placeholder="e.g. 150" required
                                    />
                                    <small class="mt-1 text-xs text-gray-500">Example: ₱100 per hour (whole numbers only).</small>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Monthly Pay (₱)</label>
                                    <input
                                        :value="formatCurrency(workerProfile.month_pay)" type="text"
                                        class="w-full cursor-not-allowed rounded border bg-gray-100 px-3 py-2 outline-none"
                                        placeholder="Automatically calculated" readonly
                                    />
                                    <small class="mt-1 text-xs text-gray-500">Automatically calculated based on hours × rate × 22 working days.</small>
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" class="rounded bg-orange-500 px-4 py-2 text-white hover:bg-orange-600">Save</button>
                                    <button type="button" @click="isEditProfileActive = false" class="rounded bg-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-400">Cancel</button>
                                </div>
                            </form>
                        </div>

                        <!-- Education - Displayed in Overview but Editable -->
                        <div class="mb-4 flex items-center gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-200">
                                <i class="bi bi-mortarboard"></i>
                            </div>
                            <div v-if="!isEditEducationActive" class="flex items-center gap-2">
                                <p
                                    @click="isEditEducationActive = !visitor"
                                    :class="[
                                        'cursor-pointer hover:underline',
                                        { 'pointer-events-none': visitor }
                                    ]"
                                >
                                    {{ workerProfile.highest_educational_attainment || "N/A" }}
                                </p>
                                <i
                                    v-if="!visitor"
                                    class="bi bi-pencil-square cursor-pointer text-sm text-gray-500"
                                    @click="isEditEducationActive = true"
                                ></i>
                            </div>
                            <form v-else @submit.prevent="updateEducation()" class="flex items-center gap-2">
                                <EducationalAttainment v-model="highestEducation" />
                                <button type="submit" class="rounded bg-orange-500 px-2 py-1 text-sm text-white">Save</button>
                                <button type="button" @click="isEditEducationActive = false" class="rounded bg-gray-300 px-2 py-1 text-sm text-gray-700">Cancel</button>
                            </form>
                        </div>

                        <!-- Member Since -->
                        <div class="mb-4 flex items-center gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-200">
                                <i class="bi bi-person"></i>
                            </div>
                            <div>
                                <p>Member Since</p>
                                <p>{{ memberSince }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Description -->
                    <div class="max-w-2xl rounded-lg bg-white p-8 text-gray-600 shadow">
                        <div class="mb-3 flex items-center gap-2">
                            <p class="text-[20px] font-bold">Profile Description</p>
                            <i
                                v-if="!visitor && !isEditDescription"
                                class="bi bi-pencil-square cursor-pointer text-sm text-gray-500"
                                @click="isEditDescription = true"
                            ></i>
                        </div>
                        <p
                            @click="isEditDescription = !visitor"
                            v-if="!isEditDescription"
                            :class="[
                                'whitespace-pre-wrap break-words text-[14px]',
                                { 'cursor-pointer hover:underline': !visitor }
                            ]"
                        >
                            {{ workerProfile.profile_description }}
                        </p>
                        <form v-if="isEditDescription" @submit.prevent="updateDescription(); isEditDescription = false;">
                            <textarea
                                class="w-full resize-none border p-2 pb-28 outline-orange-500"
                                v-model="workerProfile.profile_description"
                                required
                            ></textarea>
                            <div class="flex gap-2 mt-2">
                                <button type="submit" class="rounded bg-orange-500 px-3 py-1 text-white">Save</button>
                                <button type="button" @click="isEditDescription = false" class="rounded bg-gray-300 px-3 py-1 text-gray-700">Cancel</button>
                            </div>
                        </form>
                    </div>

                    <!-- Job History -->
                    <div class="col-span-1 rounded-lg bg-white p-4 shadow-sm">
                        <p class="mb-4 text-xl font-bold text-gray-800">Job History</p>
                        <div v-if="props.appliedJobsProps.length === 0" class="text-center text-gray-500">
                            No applied jobs found.
                        </div>
                        <ul v-else class="space-y-4">
                            <li v-for="job in displayedJobs" :key="job.id" class="flex items-center gap-4 rounded-md border p-4 shadow transition hover:shadow-md">
                                <img
                                    class="object-obtain h-16 w-16 flex-shrink-0 rounded border"
                                    :src="job.employer?.employer_profile?.business_information?.business_logo || '/assets/logo-placeholder-image.png'"
                                    alt="Company Logo"
                                />
                                <div class="flex-1">
                                    <h3 class="cursor-pointer text-lg font-semibold text-gray-900">{{ job.job_title }}</h3>
                                    <p class="text-sm text-gray-700">Employer: {{ job.employer?.name || "N/A" }}</p>
                                    <p class="text-sm text-gray-500">Status: {{ job.pivot?.status || "Pending" }}</p>
                                    <p class="text-sm text-gray-500">
                                        Started: {{ job.pivot?.created_at ? new Date(job.pivot.created_at).toLocaleDateString() : "N/A" }}
                                    </p>
                                </div>
                                <Link
                                    v-if="!props.visitor"
                                    :href="route('jobsearch.show', job.id)"
                                    as="button"
                                    class="rounded-full bg-blue-100 p-2 text-blue-700 hover:bg-blue-200"
                                    aria-label="View Job"
                                >
                                    <i class="bi bi-arrow-right text-xl"></i>
                                </Link>
                            </li>
                        </ul>
                        <div v-if="hasMoreJobs" class="mt-4 flex justify-center">
                            <button @click="toggleShowMore" class="flex items-center gap-2 rounded-lg bg-orange-500 px-4 py-2 text-white transition-colors hover:bg-orange-600">
                                <span>{{ showAllJobs ? 'Show Less' : 'Show More' }}</span>
                                <i :class="['bi transition-transform', showAllJobs ? 'bi-chevron-up' : 'bi-chevron-down']"></i>
                            </button>
                        </div>
                        <div v-if="!showAllJobs && hasMoreJobs" class="mt-2 text-center text-sm text-gray-500">
                            Showing 3 of {{ props.appliedJobsProps.length }} jobs
                        </div>
                    </div>

                    <!-- Skills -->
                    <div class="mb-8 rounded-lg bg-white p-8 text-gray-600 shadow">
                        <div class="mb-4 flex justify-between">
                            <p class="text-[20px] font-bold">Top Skills</p>
                            <button v-if="!visitor" @click="isOpenUpdateValue(true)" class="rounded-xl bg-orange-400 px-2 text-white">
                                Add Skill
                            </button>
                        </div>
                        <Skill
                            class="mb-3"
                            v-for="skill in workerSkills"
                            :key="skill.id"
                            :disabled="disable"
                            :modelValue="{
                                id: skill.id,
                                name: skill.skill_name,
                                experience: skill.experience,
                                star: Number(skill.rating),
                            }"
                            :owner="!visitor"
                            @updateSkillName="updateSkillName"
                            @updateExperience="updateExperience"
                            @removeSkill="removeSkill"
                            @addstar="updateStar"
                        />
                    </div>
                </div>

                <!-- Right Column -->
                <div>
    <!-- Basic Information -->
    <div class="mb-5 self-start rounded-lg bg-white p-8 text-gray-600 shadow">
        <div class="mb-3 flex justify-between items-center">
            <p class="text-[20px] font-bold">Basic Information</p>
            <button
                v-if="!visitor && !isEditBasicInfoActive"
                @click="isEditBasicInfoActive = true"
                class="flex items-center gap-1 text-orange-500 hover:text-orange-600"
            >
                <i class="bi bi-pencil-square"></i>
                <span>Edit</span>
            </button>
        </div>

        <!-- Display Mode -->
        <div v-if="!isEditBasicInfoActive" class="space-y-3">
            <div>
                <label class="text-sm font-medium text-gray-700">Age</label>
                <p>{{ age }} (Born in {{ workerProfile.birth_year || "N/A" }})</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Gender</label>
                <p>{{ workerProfile.gender || "N/A" }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Address</label>
                <p class="break-words whitespace-normal min-w-0">
                    {{ address || "N/A" }}
                </p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Website / Account</label>
                <div class="min-w-0">
                    <a
                        v-if="websiteLink && isValidUrl(websiteLink)"
                        :href="formatUrl(websiteLink)"
                        target="_blank"
                        class="text-blue-600 hover:text-blue-800 hover:underline break-all whitespace-normal"
                    >
                        {{ formatUrlDisplay(websiteLink) }}
                    </a>
                    <p v-else-if="websiteLink" class="break-words whitespace-normal">
                        {{ websiteLink }}
                    </p>
                    <p v-else class="text-gray-500">N/A</p>
                </div>
            </div>
        </div>

        <!-- Edit Mode -->
        <form v-else @submit.prevent="updateBasicInfo()" class="space-y-4">
            <div>
                <label class="text-sm font-medium text-gray-700">Birth Year</label>
                <input
                    v-model.number="basicInfoForm.birthYear"
                    type="number"
                    :min="1900"
                    :max="2007"
                    placeholder="1900-2007"
                    class="w-full rounded border px-3 py-2 outline-none focus:ring-2 focus:ring-orange-400"
                    :class="{ 'border-red-500': !ageValidation.valid && basicInfoForm.birthYear }"
                    required
                />
                <p class="mt-1 text-xs" :class="ageValidation.valid ? 'text-green-600' : 'text-red-600'">
                    {{ ageValidation.message }}
                </p>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Gender</label>
                <GenderSelect
                    v-model="basicInfoForm.gender"
                    class="w-full rounded border px-3 py-2 outline-none focus:ring-2 focus:ring-orange-400"
                />
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Address</label>
                <textarea
                    v-model="basicInfoForm.address"
                    rows="2"
                    class="w-full rounded border px-3 py-2 outline-none focus:ring-2 focus:ring-orange-400 resize-none"
                    placeholder="Enter your address"
                ></textarea>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Website / Account</label>
                <input
                    v-model="basicInfoForm.websiteLink"
                    type="text"
                    class="w-full rounded border px-3 py-2 outline-none focus:ring-2 focus:ring-orange-400"
                    placeholder="Enter website or social media link"
                />
                <p class="mt-1 text-xs text-gray-500">
                    Example: https://linkedin.com/in/yourprofile or your social media handle
                </p>
            </div>

            <div class="flex gap-2 pt-2">
                <button
                    type="submit"
                    :disabled="!ageValidation.valid"
                    class="rounded bg-orange-500 px-4 py-2 text-white hover:bg-orange-600 disabled:cursor-not-allowed disabled:bg-gray-300"
                >
                    Save
                </button>
                <button
                    type="button"
                    @click="cancelEditBasicInfo"
                    class="rounded bg-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-400"
                >
                    Cancel
                </button>
            </div>
        </form>

        <!-- Resume Field (Separate) -->
        <div class="mt-6 pt-6 border-t">
            <label class="text-sm font-medium text-gray-700">Resume</label>
            <div class="flex items-center gap-2 mt-1">
                <label for="resume" :class="['cursor-pointer', { 'pointer-events-none': adminVisit || visitor }]">
                    <div class="flex items-center gap-2 min-w-0">
                        <p class="hover:underline break-all" v-if="!workerProfile.resume">N/A</p>
                        <div v-else class="flex items-center gap-2 min-w-0">
                            <p class="hover:underline break-all">{{ workerProfile.resume }}</p>
                            <a
                                target="_blank"
                                :href="'/' + workerProfile.resume_path"
                                class="pointer-events-auto flex-shrink-0 rounded bg-orange-500 px-2 py-1 text-white"
                            >
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        </div>
                    </div>
                </label>
                <i
                    v-if="!adminVisit && !visitor"
                    class="bi bi-pencil-square cursor-pointer text-sm text-gray-500 flex-shrink-0"
                    @click="$refs.resumeInput?.click()"
                ></i>
            </div>
            <input ref="resumeInput" @change="updateResume" type="file" id="resume" class="hidden" />
            <InputFlashMessage class="mt-3 text-sm" :message="inputResumeError" type="error" />
        </div>
    </div>

    <ProfilePage :recentReview="recentReview" :visitor="visitor" :averageStar="averageStar" />
</div><div>
    <!-- Basic Information -->
    <div class="mb-5 self-start rounded-lg bg-white p-8 text-gray-600 shadow">
        <div class="mb-3 flex justify-between items-center">
            <p class="text-[20px] font-bold">Basic Information</p>
            <button
                v-if="!visitor && !isEditBasicInfoActive"
                @click="isEditBasicInfoActive = true"
                class="flex items-center gap-1 text-orange-500 hover:text-orange-600"
            >
                <i class="bi bi-pencil-square"></i>
                <span>Edit</span>
            </button>
        </div>

        <!-- Display Mode -->
        <div v-if="!isEditBasicInfoActive" class="space-y-3">
            <div>
                <label class="text-sm font-medium text-gray-700">Age</label>
                <p>{{ age }} (Born in {{ workerProfile.birth_year || "N/A" }})</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Gender</label>
                <p>{{ workerProfile.gender || "N/A" }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Address</label>
                <p class="break-words whitespace-normal min-w-0">
                    {{ address || "N/A" }}
                </p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Website / Account</label>
                <div class="min-w-0">
                    <a
                        v-if="websiteLink && isValidUrl(websiteLink)"
                        :href="formatUrl(websiteLink)"
                        target="_blank"
                        class="text-blue-600 hover:text-blue-800 hover:underline break-all whitespace-normal"
                    >
                        {{ formatUrlDisplay(websiteLink) }}
                    </a>
                    <p v-else-if="websiteLink" class="break-words whitespace-normal">
                        {{ websiteLink }}
                    </p>
                    <p v-else class="text-gray-500">N/A</p>
                </div>
            </div>
        </div>

        <!-- Edit Mode -->
        <form v-else @submit.prevent="updateBasicInfo()" class="space-y-4">
            <div>
                <label class="text-sm font-medium text-gray-700">Birth Year</label>
                <input
                    v-model.number="basicInfoForm.birthYear"
                    type="number"
                    :min="1900"
                    :max="2007"
                    placeholder="1900-2007"
                    class="w-full rounded border px-3 py-2 outline-none focus:ring-2 focus:ring-orange-400"
                    :class="{ 'border-red-500': !ageValidation.valid && basicInfoForm.birthYear }"
                    required
                />
                <p class="mt-1 text-xs" :class="ageValidation.valid ? 'text-green-600' : 'text-red-600'">
                    {{ ageValidation.message }}
                </p>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Gender</label>
                <GenderSelect
                    v-model="basicInfoForm.gender"
                    class="w-full rounded border px-3 py-2 outline-none focus:ring-2 focus:ring-orange-400"
                />
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Address</label>
                <textarea
                    v-model="basicInfoForm.address"
                    rows="2"
                    class="w-full rounded border px-3 py-2 outline-none focus:ring-2 focus:ring-orange-400 resize-none"
                    placeholder="Enter your address"
                ></textarea>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Website / Account</label>
                <input
                    v-model="basicInfoForm.websiteLink"
                    type="text"
                    class="w-full rounded border px-3 py-2 outline-none focus:ring-2 focus:ring-orange-400"
                    placeholder="Enter website or social media link"
                />
                <p class="mt-1 text-xs text-gray-500">
                    Example: https://linkedin.com/in/yourprofile or your social media handle
                </p>
            </div>

            <div class="flex gap-2 pt-2">
                <button
                    type="submit"
                    :disabled="!ageValidation.valid"
                    class="rounded bg-orange-500 px-4 py-2 text-white hover:bg-orange-600 disabled:cursor-not-allowed disabled:bg-gray-300"
                >
                    Save
                </button>
                <button
                    type="button"
                    @click="cancelEditBasicInfo"
                    class="rounded bg-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-400"
                >
                    Cancel
                </button>
            </div>
        </form>

        <!-- Resume Field (Separate) -->
        <div class="mt-6 pt-6 border-t">
            <label class="text-sm font-medium text-gray-700">Resume</label>
            <div class="flex items-center gap-2 mt-1">
                <label for="resume" :class="['cursor-pointer', { 'pointer-events-none': adminVisit || visitor }]">
                    <div class="flex items-center gap-2 min-w-0">
                        <p class="hover:underline break-all" v-if="!workerProfile.resume">N/A</p>
                        <div v-else class="flex items-center gap-2 min-w-0">
                            <p class="hover:underline break-all">{{ workerProfile.resume }}</p>
                            <a
                                target="_blank"
                                :href="'/' + workerProfile.resume_path"
                                class="pointer-events-auto flex-shrink-0 rounded bg-orange-500 px-2 py-1 text-white"
                            >
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        </div>
                    </div>
                </label>
                <i
                    v-if="!adminVisit && !visitor"
                    class="bi bi-pencil-square cursor-pointer text-sm text-gray-500 flex-shrink-0"
                    @click="$refs.resumeInput?.click()"
                ></i>
            </div>
            <input ref="resumeInput" @change="updateResume" type="file" id="resume" class="hidden" />
            <InputFlashMessage class="mt-3 text-sm" :message="inputResumeError" type="error" />
        </div>
    </div>

    <ProfilePage :recentReview="recentReview" :visitor="visitor" :averageStar="averageStar" />
</div>
            </div>
        </div>

        <!-- Modals and Messages -->
        <Teleport to="body">
            <Transition>
                <div v-if="messageShow" class="fixed left-[50%] top-20 flex translate-x-[-50%] items-center gap-2 rounded bg-orange-200 p-4 text-orange-600">
                    <i class="bi bi-check-circle-fill"></i>
                    <p class="text-center">{{ props.messageProp }}</p>
                </div>
            </Transition>
        </Teleport>

        <Teleport to="body">
            <Transition>
                <AddSkillModal v-if="isOpen" :isOpen="isOpen" @updateIsOpen="isOpenUpdateValue" />
            </Transition>
        </Teleport>

        <!-- Report Modal -->
        <ReusableModal v-if="isShowReportModal" @closeModal="isShowReportModal = false">
            <div class="w-[350px] max-w-[500px] rounded bg-white px-4 py-4 sm:w-[500px]">
                <div class="mb-3 flex justify-between">
                    <button @click="reasonSelected = ''" :class="{ invisible: !reasonSelected }">
                        <i class="bi bi-arrow-left-circle-fill text-2xl"></i>
                    </button>
                    <h2 class="text-2xl">Report</h2>
                    <button @click="isShowReportModal = false">
                        <i class="bi bi-x-circle text-2xl"></i>
                    </button>
                </div>
                <div class="mb-3 flex flex-col">
                    <label for="" class="text-sm text-gray-500">{{ reasonSelected }}</label>
                    <button
                        v-show="!reasonSelected"
                        v-for="(report, index) in reports"
                        :key="index"
                        @click="selectReason(report)"
                        class="flex items-center justify-between py-2 transition-all"
                    >
                        <p>{{ report.reason }}</p>
                        <i class="bi bi-caret-right font-bold"></i>
                    </button>
                    <button
                        v-show="reasonSelected"
                        v-for="(description, index) in descriptions"
                        :key="index"
                        @click="submitReport(description)"
                        class="flex items-center justify-between py-2 transition-all"
                    >
                        <p>{{ description }}</p>
                        <i class="bi bi-caret-right font-bold"></i>
                    </button>
                </div>
            </div>
        </ReusableModal>

        <!-- Contract Modal -->
        <ReusableModal v-if="isShowContractModal" @closeModal="isShowContractModal = false">
            <div class="w-[350px] max-w-full rounded bg-white px-4 py-6 sm:w-[500px]">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xl font-bold">End Contract</h2>
                    <button @click="isShowContractModal = false">
                        <i class="bi bi-x-circle text-2xl"></i>
                    </button>
                </div>
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-gray-600">Select Contract</label>
                    <select v-model="selectedContract" class="w-full rounded border px-3 py-2 outline-none focus:ring-2 focus:ring-orange-400">
                        <option disabled value="">-- Choose a job contract --</option>
                        <option v-for="job in currentlyEmployedByMeProp" :key="job.id" :value="job.id">
                            {{ job.job_title }}
                        </option>
                    </select>
                </div>
                <div class="flex justify-end gap-3">
                    <button @click="isShowContractModal = false" class="rounded bg-gray-300 px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-400">
                        Cancel
                    </button>
                    <button
                        :disabled="!selectedContract"
                        @click="fireWorker(selectedContract)"
                        class="rounded bg-red-500 px-4 py-2 text-sm font-bold text-white hover:bg-red-600 disabled:opacity-50"
                    >
                        End Contract
                    </button>
                </div>
            </div>
        </ReusableModal>
    </div>
</template>

<style scoped>
.v-enter-active,
.v-leave-active {
    transition: opacity 0.5s ease;
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
}
</style>
