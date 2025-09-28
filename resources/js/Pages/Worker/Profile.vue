<script setup>
import { computed, getCurrentInstance, ref, watch, watchEffect, onMounted } from "vue";
import Skill from "../Components/Skill.vue";
import dayjs from "dayjs";
import WorkDetailsForm from "../Components/WorkDetailsForm.vue";
import { Link, router } from "@inertiajs/vue3";
import AddSkillModal from "../Components/Modal/AddSkillModal.vue";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import InputFlashMessage from "../Components/InputFlashMessage.vue";
import ReusableModal from "../Components/Modal/ReusableModal.vue";
import ProfilePage from "../Components/Reviews/ProfilePage.vue";
import EducationalAttainment from "../Components/EducationalAttainment.vue";
import GenderSelect from "../Components/GenderSelect.vue";
import SuccessfulMessage from "../Components/Popup/SuccessfulMessage.vue";

let props = defineProps({
    userProp: Object,
    workerSkillsProp: Object,
    workerProfileProp: Object,
    workerBasicInfoProp: null,
    messageProp: String,
    
    visitor: null,
    isPending: {
        type: null,
    },
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

let messageShow = ref(false);

let isEditProfileActive = ref(false);
let isEditJobTitleActive = ref(false);
let isEditDescription = ref(false);
let isEditAgeActive = ref(false);
let isEditGenderActive = ref(false);
let isEditEducationActive = ref(false);
let isOpen = ref(false);

onMounted(() => {
    if (props.successMessage) {
        messageShow.value = true;
        setTimeout(() => {
            messageShow.value = false;
        }, 3000);
    }
});

function isOpenUpdateValue(e) {
    isOpen.value = e;
}

let workerSkills = ref(props.workerSkillsProp);
let workerProfile = ref(props.workerProfileProp);

watchEffect(() => {
    workerSkills.value = props.workerSkillsProp;
    showSuccessMessage();
});

function showSuccessMessage() {
    if (props.messageProp) {
        messageShow.value = true;
        setTimeout(() => {
            messageShow.value = false;
        }, 2000);
    }
}

const memberSince = computed(() => {
    return dayjs(props.userProp.created_at).format("MMMM DD, YYYY");
});

const age = computed(() => {
    return birthYear.value ? dayjs().format("YYYY") - birthYear.value : "N/A";
});
let birthYear = ref(workerProfile.value.birth_year);
let gender = ref(workerProfile.value.gender);
let highestEducation = ref(workerProfile.value.highest_educational_attainment);
let hourPay = ref(formatCurrency(workerProfile.value.hour_pay));
let monthPay = ref(formatCurrency(workerProfile.value.month_pay));
let isShowContractModal = ref(false);
let selectedContract = ref("");
// Add computed property for validation
const ageValidation = computed(() => {
    if (!birthYear.value)
        return { valid: false, message: "Please enter birth year" };

    if (birthYear.value < 1900)
        return { valid: false, message: "Birth year cannot be before 1900" };
    if (birthYear.value > 2007)
        return { valid: false, message: "Birth year cannot be after 2007" };

    const calculatedAge = dayjs().format("YYYY") - birthYear.value;
    if (calculatedAge < 18)
        return { valid: false, message: "Must be at least 18 years old" };

    return { valid: true, message: `Age: ${calculatedAge} years old` };
});
// Update the function to use the validation
function updateAge() {
    if (!ageValidation.value.valid) {
        alert(ageValidation.value.message);
        return;
    }

    router.put(
        "/jobseekers/myprofile/updateprofile",
        {
            birth_year: birthYear.value,
        },
        {
            onSuccess: () => {
                showSuccessMessage();
                isEditAgeActive.value = false;
                workerProfile.value.birth_year = birthYear.value;
            },
            preserveScroll: true,
        },
    );
}

// Function to update gender
function updateGender() {
    if (gender.value) {
        router.put(
            "/jobseekers/myprofile/updateprofile",
            {
                gender: gender.value,
            },
            {
                onSuccess: () => {
                    showSuccessMessage();
                    isEditGenderActive.value = false;
                    workerProfile.value.gender = gender.value;
                },
                preserveScroll: true,
            },
        );
    }
}

// Function to update education
function updateEducation() {
    if (highestEducation.value) {
        router.put(
            "/jobseekers/myprofile/updateprofile",
            {
                highest_educational_attainment: highestEducation.value,
            },
            {
                onSuccess: () => {
                    showSuccessMessage();
                    isEditEducationActive.value = false;
                    workerProfile.value.highest_educational_attainment =
                        highestEducation.value;
                },
                preserveScroll: true,
            },
        );
    }
}

// Cancel editing functions
function cancelEditAge() {
    birthYear.value = workerProfile.value.birth_year;
    isEditAgeActive.value = false;
}

function cancelEditGender() {
    gender.value = workerProfile.value.gender;
    isEditGenderActive.value = false;
}

function cancelEditEducation() {
    highestEducation.value = workerProfile.value.highest_educational_attainment;
    isEditEducationActive.value = false;
}

// ✅ Auto-calculation for salary fields
watch(
    () => [workerProfile.value.work_hour_per_day, workerProfile.value.hour_pay],
    ([hours, rate]) => {
        if (hours && rate) {
            // Assuming 22 working days per month
            workerProfile.value.month_pay = (hours * rate * 22).toFixed(2);
            // Update the formatted display values
            hourPay.value = formatCurrency(rate);
            monthPay.value = formatCurrency(workerProfile.value.month_pay);
        } else {
            workerProfile.value.month_pay = null;
            monthPay.value = "0.00";
        }
    },
);

// Function to validate work hours per day (max 12 hours)
function validateWorkHours() {
    if (workerProfile.value.work_hour_per_day > 12) {
        workerProfile.value.work_hour_per_day = 12;
    }
    if (workerProfile.value.work_hour_per_day < 1) {
        workerProfile.value.work_hour_per_day = 1;
    }
}

function updateJobTitle() {
    router.put(
        "/jobseekers/myprofile/updateprofile",
        {
            job_title: workerProfile.value.job_title,
        },
        {
            onSuccess: () => {
                showSuccessMessage();
            },
            preserveScroll: true,
        },
    );
}

function updateWorkDetails() {
    // Validate work hours before submitting
    validateWorkHours();

    // Ensure monthly pay is calculated before submission
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
        {
            onSuccess: () => {
                showSuccessMessage();
                hourPay.value = formatCurrency(workerProfile.value.hour_pay);
                monthPay.value = formatCurrency(workerProfile.value.month_pay);
            },
            preserveScroll: true,
        },
    );
}

function updateDescription() {
    router.put(
        "/jobseekers/myprofile/updateprofile",
        {
            profile_description: workerProfile.value.profile_description,
        },
        {
            onSuccess: () => {
                showSuccessMessage();
            },
            preserveScroll: true,
        },
    );
}

let profilePreview = ref(props.userProp.profile_img);
function uploadProfileImage(e) {
    profilePreview = URL.createObjectURL(e.target.files[0]);

    console.log(e.target.files[0]);

    router.post(
        "/jobseekers/myprofile/updateprofile",
        {
            _method: "put",
            profile_img: e.target.files[0],
        },
        {
            onSuccess: () => {
                showSuccessMessage();
            },
            preserveScroll: true,
        },
    );
}

// for skills
let disable = ref(false);

function updateSkillName(skillName, skillId) {
    router.put(
        `/jobseekers/myprofile/updateskill/${skillId}`,
        {
            skill_name: skillName,
        },
        {
            onStart: (start) => {
                disable.value = true;
            },
            onSuccess: () => {
                showSuccessMessage();
                workerSkills.value.forEach((e) => {
                    if (e.id === skillId) {
                        e.skill_name = skillName;
                    }
                });
            },
            onFinish: (visit) => {
                disable.value = false;
            },
            preserveScroll: true,
        },
    );
}

function updateExperience(exp, skillId) {
    router.put(
        `/jobseekers/myprofile/updateskill/${skillId}`,
        {
            experience: exp,
        },
        {
            onStart: (start) => {
                disable.value = true;
            },
            onSuccess: () => {
                showSuccessMessage();
                workerSkills.value.forEach((e) => {
                    if (e.id === skillId) {
                        e.experience = exp;
                    }
                });
            },
            onFinish: (visit) => {
                disable.value = false;
            },
            preserveScroll: true,
        },
    );
}

function updateStar(star, skillId) {
    router.put(
        `/jobseekers/myprofile/updateskill/${skillId}`,
        {
            rating: star,
        },
        {
            onStart: (start) => {
                disable.value = true;
            },
            onSuccess: () => {
                showSuccessMessage();
                workerSkills.value.forEach((e) => {
                    if (e.id === skillId) {
                        e.rating = star;
                    }
                });
            },
            onFinish: (visit) => {
                disable.value = false;
            },
            preserveScroll: true,
        },
    );
}

function removeSkill(skillId) {
    router.delete(
        `/jobseekers/myprofile/deleteskill/${skillId}`,

        {
            onBefore: () =>
                confirm("Are you sure you want to delete this user?"),
            onStart: (start) => {
                disable.value = true;
            },
            onSuccess: () => {
                showSuccessMessage();
                workerSkills.value.forEach((e, i) => {
                    if (e.id === skillId) {
                        workerSkills.value.splice(i, 1);
                    }
                });
            },
            onFinish: (visit) => {
                disable.value = false;
            },
            preserveScroll: true,
        },
    );
}

// basic info

let websiteLink = ref(props.workerBasicInfoProp?.website_link ?? "N/A");
let address = ref(props.workerBasicInfoProp?.address ?? "N/A");

let showWebsiteLinkSaveButton = ref(false);
let showAddressSaveButton = ref(false);

function updateWebsiteLink() {
    if (websiteLink.value != "" && websiteLink.value != "N/A") {
        router.put(
            route("update.basicInfo.put"),
            {
                website_link: websiteLink.value,
            },
            {
                onSuccess: () => {
                    showSuccessMessage();
                    showWebsiteLinkSaveButton.value = false;
                },
                preserveState: true,
                preserveScroll: true,
            },
        );
    }
}

function updateAdress() {
    if (address.value != "" && address.value != "N/A") {
        router.put(
            route("update.basicInfo.put"),
            {
                address: address.value,
            },
            {
                onSuccess: () => {
                    showSuccessMessage();
                    showAddressSaveButton.value = false;
                },
                preserveState: true,
                preserveScroll: true,
            },
        );
    }
}

let inputResumeError = ref("");
function updateResume(e) {
    router.post(
        "/jobseekers/myprofile/updateprofile",
        {
            _method: "put",
            resume: e.target.files[0],
        },
        {
            onError: (e) => {
                inputResumeError.value = e.resume;
            },
            onSuccess: () => {
                workerProfile.value = props.workerProfileProp;
                showSuccessMessage();
            },
            preserveScroll: true,
            preserveState: true,
        },
    );
}

const reports = [
    {
        reason: "Work Performance Issues",
        descriptions: [
            "Work Not Delivered",
            "Missed Deadlines",
            "Low-Quality Work",
            "Refusal to Revise Work",
        ],
    },
    {
        reason: "Fraud & Dishonesty",
        descriptions: [
            "Fake Profile or Misrepresentation",
            "Plagiarized or Stolen Work",
            "Multiple Accounts",
            "Scamming the Employer",
        ],
    },
    {
        reason: "Unprofessional Behavior",
        descriptions: [
            "Unresponsive Worker",
            "Rude or Disrespectful Communication",
            "Harassment or Threats",
        ],
    },
    {
        reason: "Platform Violations",
        descriptions: [
            "Asking for Off-Platform Payments",
            "Spamming or Self-Promotion",
            "Discrimination",
        ],
    },
];

let isShowReportModal = ref(false);

let reasonSelected = ref("");
let descriptions = ref([]);

function selectReason(report) {
    console.log(report);

    reasonSelected.value = report.reason;
    descriptions.value = report.descriptions;
}

function submitReport(reason) {
    router.post(
        route("report.user", { userId: route().params.id }),
        {
            reason,
        },
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
            route("fireworker", {
                workerId: route().params.id,
                jobPostId: jobPostId,
            }),
            {},
            {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    showSuccessMessage();
                },
            },
        );
    }
}
</script>
<template>
    <Head title="Profile | iCan Careers" />
    <div class="min-h-[calc(100vh-4.625rem)] bg-[#f3f7fa]">
        <div class="relative h-32 bg-[#FAFAFA]">
            <!-- Report Button - Top Right -->
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
                    {
                        'pointer-events-none': visitor,
                    },
                ]"
            >
                <div class="relative mb-3 h-full w-full">
                    <!-- Profile Image -->
                    <img
                        draggable="false"
                        :src="
                            profilePreview
                                ? profilePreview
                                : '/assets/profile_placeholder.jpg'
                        "
                        alt="Profile"
                        class="h-full w-full rounded-full border object-cover"
                    />

                    <!-- Camera Icon (Positioned at Bottom-Right) -->
                    <div
                        v-if="!visitor"
                        class="absolute bottom-1 right-1 flex h-8 w-8 items-center justify-center rounded-full bg-gray-200 shadow-md"
                    >
                        <i class="bi bi-camera text-lg text-gray-600"></i>
                    </div>
                </div>

                <input
                    @change="uploadProfileImage"
                    id="profileimg"
                    type="file"
                    class="hidden"
                />
            </label>
        </div>

        <div class="bg-white pb-2">
            <div
                class="xs container mx-auto flex flex-col items-center justify-center pt-16 xl:max-w-7xl"
            >
                <div class="mb-2 flex items-end gap-3">
                    <p class="text-lg">{{ userProp.name }}</p>
                    <Link
                        v-if="visitor"
                        :href="route('messages')"
                        :data="{ user: userProp.id }"
                        class="bi bi-chat-dots text-[30px] text-blue-500 hover:cursor-pointer"
                    ></Link>
                </div>
                <div class="mb-3">
                    <p
                        @click="isEditJobTitleActive = true"
                        v-if="!isEditJobTitleActive"
                        :class="[
                            'max-w-[600px] cursor-pointer break-words text-center text-[24px] font-bold hover:underline',
                            {
                                'pointer-events-none': visitor,
                            },
                        ]"
                    >
                        {{ workerProfile.job_title }}
                    </p>
                    <form
                        @submit.prevent="
                            isEditJobTitleActive = false;
                            updateJobTitle();
                        "
                        v-if="isEditJobTitleActive"
                    >
                        <input
                            type="text"
                            v-model="workerProfile.job_title"
                            class="mr-2 rounded border p-1 outline-none ring-orange-300 transition-all hover:ring-1 focus:ring-1"
                            required
                        />
                        <button
                            @click=""
                            class="rounded bg-orange-500 p-1 text-white"
                        >
                            Save
                        </button>
                    </form>
                </div>
                <div
                    v-if="!$page.props.auth.worker_verified && !visitor"
                    class="flex flex-col items-center"
                >
                    <p class="mb-3 text-center text-orange-600">
                        Please verify your account to apply for jobs!
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </p>
                    <Link
                        :href="route('account.verify')"
                        as="button"
                        class="w-fit rounded-lg border bg-orange-500 px-3 py-2 font-bold text-white"
                    >
                        Verfiy Now!
                    </Link>
                </div>
                <div
                    v-if="$page.props.auth.user.authenticated.verified"
                    class="flex items-center gap-1"
                >
                    <p class="text-sm font-bold text-gray-600">Verified</p>
                    <i class="bi bi-patch-check-fill text-green-400"></i>
                </div>

                <div v-if="isPending">
                    <p class="text-yellow-400">{{ isPending }}</p>
                </div>
                <!-- Manage Contracts Button -->
                <div
                    v-if="
                        currentlyEmployedByMeProp &&
                        currentlyEmployedByMeProp.length > 0
                    "
                    class="my-4 flex justify-center"
                >
                    <button
                        @click="isShowContractModal = true"
                        class="rounded-md bg-red-500 px-4 py-2 font-bold text-white shadow hover:bg-red-600"
                    >
                        Manage Contracts
                    </button>
                </div>
            </div>
        </div>
        <div
            class="xs container mx-auto px-[0.5rem] md:px-[3rem] lg:px-[9rem] xl:max-w-7xl"
        >
            <div
                class="mt-8 grid gap-6 md:grid-cols-[1fr,250px] lg:grid-cols-[1fr,300px]"
            >
                <div class="flex flex-col gap-4 text-[16px] text-gray-600">
                    <div class="rounded-lg bg-white p-8">
                        <div class="mb-4 flex items-center gap-4">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-200"
                            >
                                <i class="bi bi-bag font-bold"></i>
                            </div>
                            <p
                                v-if="!isEditProfileActive"
                                @click="isEditProfileActive = true"
                                :class="[
                                    'max-w-[400px] cursor-pointer hover:border-b-2',
                                    {
                                        'pointer-events-none': visitor,
                                    },
                                ]"
                            >
                                Looking for {{ workerProfile.job_type }} work
                                ({{
                                    workerProfile.work_hour_per_day
                                }}
                                hours/day) at {{ hourPay }}/hour ({{
                                    monthPay
                                }}/month)
                            </p>
                            <form
                                v-if="isEditProfileActive"
                                @submit.prevent="
                                    updateWorkDetails();
                                    isEditProfileActive = false;
                                "
                                class="space-y-4"
                            >
                                <!-- Job Type -->
                                <div>
                                    <label
                                        class="mb-1 block text-sm font-medium text-gray-700"
                                        >Job Type</label
                                    >
                                    <select
                                        v-model="workerProfile.job_type"
                                        class="w-full rounded border px-3 py-2 outline-none focus:ring-2 focus:ring-orange-400"
                                    >
                                        <option value="Full-time">
                                            Full-time
                                        </option>
                                        <option value="Part-time">
                                            Part-time
                                        </option>
                                    </select>
                                </div>

                                <!-- Work Hours Per Day -->
                                <div>
                                    <label
                                        class="mb-1 block text-sm font-medium text-gray-700"
                                    >
                                        Work Hours per Day (Max: 12 hours)
                                    </label>
                                    <input
                                        v-model.number="
                                            workerProfile.work_hour_per_day
                                        "
                                        type="number"
                                        min="1"
                                        max="12"
                                        @input="validateWorkHours"
                                        class="w-full rounded border px-3 py-2 outline-none focus:ring-2 focus:ring-orange-400"
                                        placeholder="e.g. 8 hours"
                                        required
                                    />
                                    <small class="mt-1 text-xs text-gray-500">
                                        Maximum of 12 hours per day is allowed.
                                    </small>
                                </div>

                                <!-- Hourly Pay -->
                                <div>
                                    <label
                                        class="mb-1 block text-sm font-medium text-gray-700"
                                        >Hourly Pay (₱)</label
                                    >
                                    <input
                                        v-model.number="workerProfile.hour_pay"
                                        type="number"
                                        min="1"
                                        step="1"
                                        class="w-full rounded border px-3 py-2 outline-none focus:ring-2 focus:ring-orange-400"
                                        placeholder="e.g. 150"
                                        required
                                    />
                                    <small class="mt-1 text-xs text-gray-500">
                                        Example: ₱100 per hour (whole numbers
                                        only).
                                    </small>
                                </div>

                                <!-- Monthly Pay (Auto-calculated) -->
                                <div>
                                    <label
                                        class="mb-1 block text-sm font-medium text-gray-700"
                                        >Monthly Pay (₱)</label
                                    >
                                    <input
                                        :value="
                                            formatCurrency(
                                                workerProfile.month_pay,
                                            )
                                        "
                                        type="text"
                                        class="w-full cursor-not-allowed rounded border bg-gray-100 px-3 py-2 outline-none"
                                        placeholder="Automatically calculated"
                                        readonly
                                    />
                                    <small class="mt-1 text-xs text-gray-500">
                                        Automatically calculated based on hours
                                        × rate × 22 working days.
                                    </small>
                                </div>

                                <div class="flex gap-2">
                                    <button
                                        type="submit"
                                        class="rounded bg-orange-500 px-4 py-2 text-white hover:bg-orange-600"
                                    >
                                        Save
                                    </button>
                                    <button
                                        type="button"
                                        @click="isEditProfileActive = false"
                                        class="rounded bg-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-400"
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="mb-4 flex items-center gap-4">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-200"
                            >
                                <i class="bi bi-mortarboard"></i>
                            </div>
                            <div
                                v-if="!isEditEducationActive"
                                class="flex items-center gap-2"
                            >
                                <p
                                    @click="isEditEducationActive = true"
                                    :class="[
                                        'cursor-pointer hover:underline',
                                        { 'pointer-events-none': visitor },
                                    ]"
                                >
                                    {{ highestEducation || "N/A" }}
                                </p>
                                <i
                                    v-if="!visitor"
                                    class="bi bi-pencil-square cursor-pointer text-sm text-gray-500"
                                    @click="isEditEducationActive = true"
                                ></i>
                            </div>
                            <form
                                v-else
                                @submit.prevent="updateEducation()"
                                class="flex items-center gap-2"
                            >
                                <EducationalAttainment
                                    v-model="highestEducation"
                                />
                                <button
                                    type="submit"
                                    class="rounded bg-orange-500 px-2 py-1 text-sm text-white"
                                >
                                    Save
                                </button>
                                <button
                                    type="button"
                                    @click="cancelEditEducation"
                                    class="rounded bg-gray-300 px-2 py-1 text-sm text-gray-700"
                                >
                                    Cancel
                                </button>
                            </form>
                        </div>
                        <div class="mb-4 flex items-center gap-4">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-200"
                            >
                                <i class="bi bi-person"></i>
                            </div>
                            <div>
                                <p>Member Since</p>
                                <p>{{ memberSince }}</p>
                            </div>
                        </div>
                    </div>
                    <div
                        class="max-w-2xl rounded-lg bg-white p-8 text-gray-600 shadow"
                    >
                        <p class="mb-3 text-[20px] font-bold">
                            Profile Description
                        </p>
                        <p
                            @click="isEditDescription = true"
                            v-if="!isEditDescription"
                            :class="[
                                'cursor-pointer whitespace-pre-wrap break-words text-[14px] hover:underline',
                                {
                                    'pointer-events-none': visitor,
                                },
                            ]"
                        >
                            {{ workerProfile.profile_description }}
                        </p>

                        <form
                            v-if="isEditDescription"
                            @submit.prevent="
                                updateDescription();
                                isEditDescription = false;
                            "
                        >
                            <textarea
                                class="w-full resize-none border p-2 pb-28 outline-orange-500"
                                v-model="workerProfile.profile_description"
                                required
                            ></textarea>
                            <button
                                class="rounded bg-orange-500 p-1 text-white"
                            >
                                Save
                            </button>
                        </form>
                    </div>

                    <div class="col-span-1 rounded-lg bg-white p-4 shadow-sm">
                        <p class="mb-4 text-xl font-bold text-gray-800">
                            Job History
                        </p>

                        <div
                            v-if="props.appliedJobsProps.length === 0"
                            class="text-center text-gray-500"
                        >
                            No applied jobs found.
                        </div>

                        <ul v-else class="space-y-4">
                            <li
                                v-for="job in props.appliedJobsProps"
                                :key="job.id"
                                class="flex items-center gap-4 rounded-md border p-4 shadow transition hover:shadow-md"
                            >
                                <!-- Company Logo -->

                                <img
                                    class="object-obtain h-16 w-16 flex-shrink-0 rounded border"
                                    :src="
                                        job.employer?.employer_profile
                                            ?.business_information
                                            ?.business_logo ||
                                        '/assets/logo-placeholder-image.png'
                                    "
                                    alt="Company Logo"
                                />

                                <!-- Job Info -->
                                <div class="flex-1">
                                    <h3
                                        class="cursor-pointer text-lg font-semibold text-gray-900"
                                    >
                                        {{ job.job_title }}
                                    </h3>

                                    <p class="text-sm text-gray-700">
                                        Employer:
                                        {{ job.employer?.name || "N/A" }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Status:
                                        {{ job.pivot?.status || "Pending" }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Started:
                                        {{
                                            job.pivot?.created_at
                                                ? new Date(
                                                      job.pivot.created_at,
                                                  ).toLocaleDateString()
                                                : "N/A"
                                        }}
                                    </p>
                                </div>

                                <!-- View Button -->
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
                    </div>

                    <div
                        class="mb-8 rounded-lg bg-white p-8 text-gray-600 shadow"
                    >
                        <div class="mb-4 flex justify-between">
                            <p class="text-[20px] font-bold">Top Skills</p>
                            <button
                                v-if="!visitor"
                                @click="isOpenUpdateValue(true)"
                                class="rounded-xl bg-orange-400 px-2 text-white"
                            >
                                Add Skill
                            </button>
                        </div>
                        <Skill
                            class="mb-3"
                            v-for="(skill, index) in workerSkills"
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
                <div>
                    <div
                        class="mb-5 self-start rounded-lg bg-white p-8 text-gray-600 shadow"
                    >
                        <p class="mb-3 text-[20px] font-bold">
                            Basic Information
                        </p>

                        <!-- Age Field with validation feedback -->
                        <div class="mb-2">
                            <label class="text-sm" for="">Age</label>
                            <div
                                v-if="!isEditAgeActive"
                                class="flex items-center gap-2"
                            >
                                <p
                                    @click="isEditAgeActive = true"
                                    :class="[
                                        'cursor-pointer hover:underline',
                                        { 'pointer-events-none': visitor },
                                    ]"
                                >
                                    {{ age }} (Born in {{ birthYear || "N/A" }})
                                </p>
                                <i
                                    v-if="!visitor"
                                    class="bi bi-pencil-square cursor-pointer text-sm text-gray-500"
                                    @click="isEditAgeActive = true"
                                ></i>
                            </div>
                            <form
                                v-else
                                @submit.prevent="updateAge()"
                                class="flex flex-col gap-2"
                            >
                                <div>
                                    <label
                                        class="mb-1 block text-xs text-gray-500"
                                        >Birth Year:</label
                                    >
                                    <input
                                        v-model.number="birthYear"
                                        type="number"
                                        :min="1900"
                                        :max="2007"
                                        placeholder="1900-2007"
                                        class="w-32 border px-2 py-1 outline-orange-200"
                                        :class="{
                                            'border-red-500':
                                                !ageValidation.valid &&
                                                birthYear,
                                        }"
                                        required
                                    />
                                    <p
                                        class="mt-1 text-xs"
                                        :class="
                                            ageValidation.valid
                                                ? 'text-green-600'
                                                : 'text-red-600'
                                        "
                                    >
                                        {{ ageValidation.message }}
                                    </p>
                                    <InputFlashMessage
                                        type="error"
                                        :message="$page.props.errors?.birth_year"
                                    />
                                </div>
                                <div class="flex gap-2">
                                    <button
                                        type="submit"
                                        :disabled="!ageValidation.valid"
                                        class="rounded bg-orange-500 px-2 py-1 text-sm text-white disabled:cursor-not-allowed disabled:bg-gray-300"
                                    >
                                        Save
                                    </button>
                                    <button
                                        type="button"
                                        @click="cancelEditAge"
                                        class="rounded bg-gray-300 px-2 py-1 text-sm text-gray-700"
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Gender Field -->
                        <div class="mb-2">
                            <label class="text-sm" for="">Gender</label>

                            <!-- Display mode -->
                            <div
                                v-if="!isEditGenderActive"
                                class="flex items-center gap-2"
                            >
                                <p
                                    @click="isEditGenderActive = true"
                                    :class="[
                                        'cursor-pointer hover:underline',
                                        { 'pointer-events-none': visitor },
                                    ]"
                                >
                                    {{ gender || "N/A" }}
                                </p>
                                <i
                                    v-if="!visitor"
                                    class="bi bi-pencil-square cursor-pointer text-sm text-gray-500"
                                    @click="isEditGenderActive = true"
                                ></i>
                            </div>

                            <!-- Edit mode -->
                            <form
                                v-else
                                @submit.prevent="updateGender()"
                                class="flex flex-col gap-3"
                            >
                                <!-- Gender Select -->
                                <GenderSelect v-model="gender" />

                                <!-- Save/Cancel buttons at the bottom -->
                                <div class="flex items-center gap-2">
                                    <button
                                        type="submit"
                                        class="rounded bg-orange-500 px-3 py-1 text-sm text-white disabled:cursor-not-allowed disabled:bg-gray-300"
                                    >
                                        Save
                                    </button>
                                    <button
                                        type="button"
                                        @click="cancelEditGender"
                                        class="rounded bg-gray-300 px-3 py-1 text-sm text-gray-700"
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Rest of the existing fields (Address, Website, Resume) -->
                        <div
                            :class="[
                                'mb-2',
                                {
                                    'pointer-events-none':
                                        adminVisit || visitor,
                                },
                            ]"
                        >
                            <label class="text-sm" for="">Address</label>
                            <input
                                @focus="showAddressSaveButton = true"
                                type="text"
                                v-model="address"
                                class="outline-none hover:underline"
                            />

                            <button
                                v-show="showAddressSaveButton"
                                @click="updateAdress()"
                                class="rounded bg-orange-500 p-1 text-white"
                            >
                                Save
                            </button>
                        </div>
                        <div
                            :class="[
                                'mb-2',
                                {
                                    'pointer-events-none':
                                        adminVisit || visitor,
                                },
                            ]"
                        >
                            <label class="text-sm" for=""
                                >Website / Account</label
                            >
                            <input
                                @focus="showWebsiteLinkSaveButton = true"
                                type="text"
                                v-model="websiteLink"
                                class="outline-none hover:underline"
                            />
                            <button
                                v-show="showWebsiteLinkSaveButton"
                                @click="updateWebsiteLink()"
                                class="rounded bg-orange-500 p-1 text-white"
                            >
                                Save
                            </button>
                        </div>
                        <div class="mb-2">
                            <label class="text-sm" for="">Resume</label>
                            <label
                                for="resume"
                                :class="[
                                    'cursor-pointer',
                                    { 'pointer-events-none': adminVisit },
                                ]"
                            >
                                <p
                                    class="hover:underline"
                                    v-if="!workerProfile.resume"
                                >
                                    N/A
                                </p>
                                <div v-else class="flex items-center gap-2">
                                    <p class="hover:underline">
                                        {{ workerProfile.resume }}
                                    </p>
                                    <a
                                        target="_blank"
                                        :href="'/' + workerProfile.resume_path"
                                        as="button"
                                        class="pointer-events-auto rounded bg-orange-500 px-2 py-1 text-white"
                                    >
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                </div>
                                <input
                                    @change="updateResume"
                                    type="file"
                                    id="resume"
                                    class="hidden"
                                />
                                <InputFlashMessage
                                    class="mt-3 text-sm"
                                    :message="inputResumeError"
                                    type="error"
                                ></InputFlashMessage>
                            </label>
                        </div>
                    </div>
                    <ProfilePage
                        :recentReview="recentReview"
                        :visitor="visitor"
                        :averageStar="averageStar"
                    ></ProfilePage>
                </div>
            </div>
        </div>

        <Teleport defer to="body">
            <Transition>
                <div v-if="messageShow" class="">
                    <div
                        class="fixed left-[50%] top-20 flex translate-x-[-50%] items-center gap-2 rounded bg-orange-200 p-4 text-orange-600"
                    >
                        <i class="bi bi-check-circle-fill"></i>
                        <p class="text-center">{{ props.messageProp }}</p>
                    </div>
                </div>
            </Transition>
        </Teleport>
        <Teleport defer to="body">
            <Transition>
                <AddSkillModal
                    v-if="isOpen"
                    :isOpen="isOpen"
                    @updateIsOpen="isOpenUpdateValue"
                />
            </Transition>
        </Teleport>
    </div>

    <ReusableModal
        v-if="isShowReportModal"
        @closeModal="isShowReportModal = false"
    >
        <div
            class="w-[350px] max-w-[500px] rounded bg-white px-4 py-4 sm:w-[500px]"
        >
            <div class="mb-3 flex justify-between">
                <button
                    @click="reasonSelected = ''"
                    :class="{ invisible: !reasonSelected }"
                >
                    <i class="bi bi-arrow-left-circle-fill text-2xl"></i>
                </button>
                <h2 class="text-2xl">Report</h2>
                <button @click="isShowReportModal = false">
                    <i class="bi bi-x-circle text-2xl"></i>
                </button>
            </div>
            <div class="mb-3 flex flex-col">
                <label for="" class="text-sm text-gray-500">{{
                    reasonSelected
                }}</label>
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

    <ReusableModal
        v-if="isShowContractModal"
        @closeModal="isShowContractModal = false"
    >
        <div
            class="w-[350px] max-w-full rounded bg-white px-4 py-6 sm:w-[500px]"
        >
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold">End Contract</h2>
                <button @click="isShowContractModal = false">
                    <i class="bi bi-x-circle text-2xl"></i>
                </button>
            </div>

            <!-- Dropdown to select job -->
            <div class="mb-4">
                <label class="mb-1 block text-sm font-medium text-gray-600"
                    >Select Contract</label
                >
                <select
                    v-model="selectedContract"
                    class="w-full rounded border px-3 py-2 outline-none focus:ring-2 focus:ring-orange-400"
                >
                    <option disabled value="">
                        -- Choose a job contract --
                    </option>
                    <option
                        v-for="job in currentlyEmployedByMeProp"
                        :key="job.id"
                        :value="job.id"
                    >
                        {{ job.job_title }}
                    </option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3">
                <button
                    @click="isShowContractModal = false"
                    class="rounded bg-gray-300 px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-400"
                >
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
    <SuccessfulMessage
        :messageProp="$page.props.errors.message"
        :messageShow="$page.props.errors.message"
        type="Error"
    ></SuccessfulMessage>
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
