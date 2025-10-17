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
let coverPhotoError = ref("");
let profilePhotoError = ref("");
let uploadSuccess = ref("");
let backendErrors = ref({});

// Profile Data
let workerSkills = ref(props.workerSkillsProp);
let workerProfile = ref(props.workerProfileProp);
let profilePreview = ref(props.userProp.profile_img_url);
let highestEducation = ref(workerProfile.value.highest_educational_attainment);
let coverPhotoPreview = ref(props.userProp.cover_photo_url);

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
// Image Validation Functions
function validateImageFile(file, maxSizeMB = 5) {
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    const maxSize = maxSizeMB * 1024 * 1024; // Convert to bytes

    // Check file type
    if (!allowedTypes.includes(file.type)) {
        return {
            valid: false,
            message: 'Please select a valid image file (JPEG, JPG, PNG, or WEBP). GIF and video files are not allowed.'
        };
    }

    // Check file size
    if (file.size > maxSize) {
        return {
            valid: false,
            message: `Image must be less than ${maxSizeMB}MB. Your file is ${(file.size / (1024 * 1024)).toFixed(2)}MB.`
        };
    }

    // Additional check for GIF by reading first few bytes
    if (file.type === 'image/gif' || file.name.toLowerCase().endsWith('.gif')) {
        return {
            valid: false,
            message: 'GIF images are not allowed. Please use JPEG, JPG, PNG, or WEBP format.'
        };
    }

    return { valid: true, message: '' };
}

function showUploadSuccess(message) {
    uploadSuccess.value = message;
    setTimeout(() => {
        uploadSuccess.value = "";
    }, 3000);
}

function clearErrors() {
    coverPhotoError.value = "";
    profilePhotoError.value = "";
}

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

// Add the cover photo upload function
function uploadCoverPhoto(e) {
    clearErrors();

    if (!e.target.files[0]) return;

    const file = e.target.files[0];

    // Validate image file
    const validation = validateImageFile(file, 5);
    if (!validation.valid) {
        coverPhotoError.value = validation.message;
        e.target.value = ''; // Clear the file input
        return;
    }

    const formData = new FormData();
    formData.append('cover_photo', file);
    formData.append('_method', 'put');

    router.post(
        "/jobseekers/myprofile/updateprofile",
        formData,
        {
            onSuccess: () => {
                showUploadSuccess('Cover photo updated successfully!');
                coverPhotoPreview.value = URL.createObjectURL(file);
                coverPhotoError.value = "";
            },
            onError: (errors) => {
                coverPhotoError.value = errors.cover_photo || 'Failed to upload cover photo. Please try again.';
            },
            preserveScroll: true,
        },
    );
}


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
    clearErrors();

    if (!e.target.files[0]) return;

    const file = e.target.files[0];

    // Validate image file
    const validation = validateImageFile(file, 2); // 2MB max for profile photos
    if (!validation.valid) {
        profilePhotoError.value = validation.message;
        e.target.value = ''; // Clear the file input
        return;
    }

    // Create preview
    profilePreview.value = URL.createObjectURL(file);

    const formData = new FormData();
    formData.append('_method', 'put');
    formData.append('profile_img', file);

    router.post(
        "/jobseekers/myprofile/updateprofile",
        formData,
        {
            onSuccess: () => {
                showUploadSuccess('Profile photo updated successfully!');
                profilePhotoError.value = "";
            },
            onError: (errors) => {
                profilePhotoError.value = errors.profile_img || 'Failed to upload profile photo. Please try again.';
                // Revert preview on error
                profilePreview.value = props.userProp.profile_img_url;
            },
            preserveScroll: true,
        },
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
                backendErrors.value = {}; // Clear backend errors

                // Update local state
                workerProfile.value.birth_year = basicInfoForm.value.birthYear;
                workerProfile.value.gender = basicInfoForm.value.gender;

                // Update address and website display
                address.value = basicInfoForm.value.address || "N/A";
                websiteLink.value = basicInfoForm.value.websiteLink || "N/A";
            },
            onError: (errors) => {
                // Handle backend validation errors
                backendErrors.value = errors;

                // If there's a birth_year error from backend, show it
                if (errors.birth_year) {
                    // You can either show an alert or display it in the form
                    alert(errors.birth_year);
                    // Or set it to display below the input field
                }
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
        <!-- Updated Header Section with Cover Photo -->

                <div class="relative">
                <!-- Cover Photo Section - Facebook Proportions -->
                <div class="relative bg-gray-200 overflow-hidden">
                    <!-- Facebook cover photo ratio is approximately 2.7:1 (851x315) -->
                    <div class="w-full aspect-[2.7/1] min-h-[150px] max-h-[400px]">
                    <!-- Cover Photo Display -->
                    <div v-if="coverPhotoPreview" class="h-full w-full">
                        <img
                        :src="coverPhotoPreview"
                        alt="Cover Photo"
                        class="h-full w-full object-cover"
                        />
                    </div>
                    <div v-else class="flex h-full w-full items-center justify-center bg-gradient-to-r from-gray-300 to-gray-400">
                        <span class="text-gray-500 text-sm md:text-base">No cover photo</span>
                    </div>
                    </div>

                    <!-- Cover Photo Upload Button (only for owner) - Facebook Style -->
                    <label
                    v-if="!visitor"
                    for="coverPhoto"
                    class="absolute right-4 bottom-4 flex cursor-pointer items-center gap-2 rounded bg-black/60 px-3 py-2 text-sm font-medium text-white backdrop-blur-sm transition-all hover:bg-black/70"
                    >
                    <i class="bi bi-camera"></i>
                    <span class="hidden sm:inline">{{ coverPhotoPreview ? 'Update cover photo' : 'Add cover photo' }}</span>
                    <span class="sm:hidden">{{ coverPhotoPreview ? 'Update' : 'Add' }}</span>
                    </label>
                    <input
                    @change="uploadCoverPhoto"
                    id="coverPhoto"
                    type="file"
                    class="hidden"
                    accept=".jpg,.jpeg,.png,.webp"
                    />

                    <!-- Report Button (for visitors) - Facebook Style -->
                    <div v-if="visitor" class="absolute right-4 top-4">
                    <button
                        @click="isShowReportModal = true"
                        class="flex items-center gap-1 rounded bg-black/60 px-3 py-2 text-sm font-medium text-white backdrop-blur-sm transition-all hover:bg-black/70"
                    >
                        <i class="bi bi-flag"></i>
                        <span class="hidden sm:inline">Report</span>
                    </button>
                    </div>
                </div>

                <!-- Profile Image Section - Centered like Facebook -->
              <div class="relative">
                    <div class="container mx-auto px-4 max-w-6xl">
                        <div class="flex justify-center">
                            <div class="absolute -bottom-16 transform">
                                <label
                                    for="profileimg"
                                    :class="[
                                        'flex flex-col items-center cursor-pointer',
                                        { 'pointer-events-none': visitor }
                                    ]"
                                >
                                    <div class="relative">
                                        <!-- Facebook-style profile picture with white border -->
                                        <div class="rounded-full bg-white p-1 shadow-lg">
                                            <img
                                                draggable="false"
                                                :src="profilePreview || '/assets/profile_placeholder.jpg'"
                                                alt="Profile"
                                                class="h-32 w-32 rounded-full object-cover md:h-36 md:w-36 lg:h-40 lg:w-40"
                                            />
                                        </div>

                                        <!-- Camera icon overlay - Positioned at bottom-right edge -->
                                        <div
                                            v-if="!visitor"
                                            class="absolute bottom-2 right-1 flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white shadow-lg transition-all hover:bg-blue-700 border-2 border-white"
                                        >
                                            <i class="bi bi-camera text-sm"></i>
                                        </div>
                                    </div>
                                    <input
                                        @change="uploadProfileImage"
                                        id="profileimg"
                                        type="file"
                                        class="hidden"
                                        accept=".jpg,.jpeg,.png,.webp"
                                    />
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                <!-- Profile Info Section - Adjusted for Facebook layout -->
                <div class="bg-white pb-6 pt-20 md:pt-24 lg:pt-28">
                <div class="container mx-auto max-w-6xl px-4">
                    <div class="flex flex-col items-center text-center">
                    <!-- Name and Chat -->
                    <div class="mb-2 flex items-end gap-3">
                        <p class="text-2xl font-bold text-gray-900">{{ userProp.name }}</p>
                        <Link
                        v-if="visitor"
                        :href="route('messages')"
                        :data="{ user: userProp.id }"
                        class="bi bi-chat-dots-fill text-[28px] text-blue-500 hover:cursor-pointer hover:text-blue-600 transition-colors"
                        ></Link>
                    </div>

                    <!-- Job Title -->
                    <div class="mb-4 max-w-2xl">
                        <div v-if="!isEditJobTitleActive" class="flex items-center justify-center gap-2">
                        <p
                            @click="isEditJobTitleActive = !visitor"
                            :class="[
                            'text-lg text-gray-600 break-words text-center',
                            { 'cursor-pointer hover:text-gray-800': !visitor }
                            ]"
                        >
                            {{ workerProfile.job_title }}
                        </p>
                        <i
                            v-if="!visitor"
                            class="bi bi-pencil-square cursor-pointer text-gray-400 hover:text-gray-600 transition-colors"
                            @click="isEditJobTitleActive = true"
                        ></i>
                        </div>
                        <form
                        v-if="isEditJobTitleActive"
                        @submit.prevent="updateJobTitle(); isEditJobTitleActive = false;"
                        class="flex items-center justify-center gap-2"
                        >
                        <input
                            type="text"
                            v-model="workerProfile.job_title"
                            class="rounded-lg border border-gray-300 px-3 py-1 text-center outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            placeholder="Enter your job title"
                            required
                        />
                        <button class="rounded-lg bg-blue-600 px-3 py-1 text-white hover:bg-blue-700 transition-colors">
                            Save
                        </button>
                        </form>
                    </div>

                    <!-- Verification Status -->
                    <div v-if="!$page.props.auth.worker_verified && !visitor" class="mb-4 flex flex-col items-center">
                        <p class="mb-2 text-center text-amber-600 text-sm">
                        <i class="bi bi-exclamation-triangle-fill mr-1"></i>
                        Please verify your account to apply for jobs!
                        </p>
                        <Link
                        :href="route('account.verify')"
                        as="button"
                        class="rounded-lg bg-amber-500 px-4 py-2 font-semibold text-white hover:bg-amber-600 transition-colors"
                        >
                        Verify Now
                        </Link>
                    </div>

                    <!-- Verified Badge -->
                    <div v-if="$page.props.auth.user.authenticated.verified" class="mb-4 flex items-center gap-1 text-green-600">
                        <i class="bi bi-patch-check-fill text-lg"></i>
                        <p class="text-sm font-semibold">Verified</p>
                    </div>

                    <!-- Pending Status -->
                    <div v-if="isPending" class="mb-4">
                        <p class="text-amber-500 text-sm">{{ isPending }}</p>
                    </div>

                    <!-- Manage Contracts Button -->
                    <div v-if="currentlyEmployedByMeProp?.length > 0" class="my-4">
                        <button
                        @click="isShowContractModal = true"
                        class="rounded-lg bg-red-500 px-6 py-2 font-semibold text-white shadow hover:bg-red-600 transition-colors"
                        >
                        Manage Contracts
                        </button>
                    </div>
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
                                    class="object-contain h-16 w-16 flex-shrink-0 rounded border"
                                    :src="job.employer?.employer_profile?.business_information?.business_logo_url || '/assets/logo-placeholder-image.png'"
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
                :min="props.userProp.senior_citizen_registered ? 1900 : 1900"
                :max="props.userProp.senior_citizen_registered ? dayjs().format('YYYY') - 60 : 2007"
                :placeholder="props.userProp.senior_citizen_registered ? 'Must be 60+ years' : '1900-2007'"
                class="w-full rounded border px-3 py-2 outline-none focus:ring-2 focus:ring-orange-400"
                :class="{
                    'border-red-500': (!ageValidation.valid && basicInfoForm.birthYear) || backendErrors.birth_year
                }"
                required
            />

            <!-- Frontend Validation Message -->
            <p v-if="!ageValidation.valid && basicInfoForm.birthYear" class="mt-1 text-xs text-red-600">
                {{ ageValidation.message }}
            </p>

            <!-- Backend Validation Message -->
            <p v-if="backendErrors.birth_year" class="mt-1 text-xs text-red-600">
                {{ backendErrors.birth_year }}
            </p>

            <!-- Success/Info Message -->
            <p v-if="ageValidation.valid && !backendErrors.birth_year" class="mt-1 text-xs text-green-600">
                {{ ageValidation.message }}
            </p>

            <!-- Senior Citizen Badge -->
            <div v-if="props.userProp.senior_citizen_registered" class="mt-2 flex items-center gap-1 text-purple-600">
                <i class="bi bi-person-check-fill"></i>
                <p class="text-xs font-semibold">Registered Senior Citizen</p>
            </div>
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
                                :href="workerProfile.resume_url"
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
