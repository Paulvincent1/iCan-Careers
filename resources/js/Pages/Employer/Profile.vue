<script setup>
import { computed, getCurrentInstance, ref, watch, watchEffect } from "vue";
import dayjs from "dayjs";
import { Link, router } from "@inertiajs/vue3";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import ReusableModal from "../Components/Modal/ReusableModal.vue";
import ProfilePage from "../Components/Reviews/ProfilePage.vue";
import ProfileBusinessCard from "../Components/ProfileBusinessCard.vue";

let props = defineProps({
    user: Object,
    employerProfileProp: {
        type: Object,
        default: () => ({}), // Ensure an empty object if null
    },
    messageProp: {
        type: String,
        default: "", // Ensure an empty string if null
    },
      messageType: {
        type: String,
        default: "success",
    },
    jobsPostedProps: {
        type: Array,
        default: () => [], // Ensure an empty array if null
    },
    businessProps: {
        type: Object,
        default: () => null, // Explicitly handle null
    },
    subscriptionProps: {
        type: Object,
        required: false,
        default: () => null, // Explicitly handle null
    },
    visitor: {
        default: null,
    },
    adminVisit: {
        default: null,
    },
    averageStar: null,
    recentReview: null,
});

const memberSince = computed(() => {
    return dayjs(props.user.created_at).format("MMMM DD, YYYY");
});

let messageShow = ref(false);
let messageType = ref(props.messageType || 'success');
let profilePreview = ref(props.user.profile_img_url || '/assets/profile_placeholder.jpg');
let employerProfile = ref({ ...props.employerProfileProp });
let isEditFullNameActive = ref(false);
let isEditingBasicInfo = ref(false);
let coverPhotoPreview = ref(props.user.cover_photo_url);

watchEffect(() => {
    showSuccessMessage();
});

const age = computed(() => {
    if (!employerProfile.value.birth_year) return "Not provided";
    const birthYear = Number(employerProfile.value.birth_year);
    return dayjs().year() - birthYear;
});

watchEffect(() => {
    console.log("Employer Profile:", employerProfile.value);
});

// Computed property for dynamic styling
const subscriptionClass = computed(() => {
    if (!props.subscriptionProps) return "";
    switch (props.subscriptionProps.subscription_type) {
        case "Free":
            return "text-gray-500 bg-gray-100 px-2 py-1 rounded";
        case "Pro":
            return "text-blue-500 bg-blue-100 px-2 py-1 rounded";
        case "Premium":
            return "text-yellow-500 bg-yellow-100 px-2 py-1 rounded";
        default:
            return "";
    }
});
// Validate image file
function validateImageFile(file) {
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/bmp', 'image/webp']; // Removed gif
    const maxSize = 2 * 1024 * 1024; // 2MB

    if (!validTypes.includes(file.type)) {
        return {
            isValid: false,
            message: 'Please select a valid image file (JPEG, PNG, JPG, BMP, WEBP). GIF files are not allowed.'
        };
    }

    if (file.size > maxSize) {
        return {
            isValid: false,
            message: 'Image must be less than 2MB.'
        };
    }

    return { isValid: true };
}
function showMessage(message, type = 'success') {
    messageShow.value = true;
    messageType.value = type;

    // Update the message prop for display
    const messageElement = document.querySelector('.message-display');
    if (messageElement) {
        messageElement.textContent = message;
    }

    setTimeout(() => {
        messageShow.value = false;
    }, 4000);
}

// Add the cover photo upload function
function uploadCoverPhoto(e) {
    if (!e.target.files[0]) return;

    const file = e.target.files[0];

    // Validate file
    const validation = validateImageFile(file);
    if (!validation.isValid) {
        showMessage(validation.message, 'error');
        e.target.value = ''; // Clear the file input
        return;
    }

    const formData = new FormData();
    formData.append('cover_photo', file);
    formData.append('_method', 'put');

    router.post(
        "/employers/myprofile/updateprofile",
        formData,
        {
            onSuccess: (page) => {
                if (page.props.message) {
                    showMessage(page.props.message, page.props.messageType || 'success');
                }
                coverPhotoPreview.value = URL.createObjectURL(file);
            },
            onError: (errors) => {
                if (errors.cover_photo) {
                    showMessage(errors.cover_photo[0], 'error');
                } else {
                    showMessage('Failed to upload cover photo. Please try again.', 'error');
                }
            },
            preserveScroll: true,
        },
    );
}

function updateBasicInfo() {
    router.put(
        "/employers/myprofile/updateprofile",
        {
            birth_year: employerProfile.value.birth_year || "",
            gender: employerProfile.value.gender,
            phone_number: employerProfile.value.phone_number || "",
        },
        {
            onSuccess: () => {
                showSuccessMessage();
                isEditingBasicInfo.value = false;
            },
            preserveScroll: true,
        },
    );
}

function updateFullName() {
    router.put(
        "/employers/myprofile/updateprofile",
        {
            full_name: employerProfile.value.full_name,
        },
        {
            onSuccess: () => {
                showSuccessMessage();
            },
            preserveScroll: true,
        },
    );
}
function uploadProfileImage(e) {
    profilePreview = URL.createObjectURL(e.target.files[0]);

    router.post(
        "/employers/myprofile/updateprofile",
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

// Function to show success message
function showSuccessMessage() {
    if (props.messageProp) {
        messageShow.value = true;
        setTimeout(() => {
            messageShow.value = false;
        }, 2000);
    }
}

const reports = [
    {
        reason: "Payment Issues",
        descriptions: [
            "Non-Payment",
            "Underpayment",
            "Delayed Payment",
            "Scamming the Worker",
        ],
    },
    {
        reason: "Job-Related Issues",
        descriptions: [
            "Fake Job Listing",
            "Misleading Job Description",
            "Unreasonable Job Requirements",
            "Job Cancellation After Work",
        ],
    },
    {
        reason: "Professionalism & Behavior Issues",
        descriptions: [
            "Harassment or Abuse",
            "Discrimination",
            "Unprofessional Communication",
            "Forcing Off-Platform Work",
        ],
    },
    {
        reason: "Fraud & Policy Violations",
        descriptions: [
            "Fake Employer Profile",
            "Asking for Free Work",
            "Spamming & Scams",
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
// Add these new reactive variables for expandable jobs
const showAllJobs = ref(false);
const initialJobsToShow = 3; // Number of jobs to show initially

// Computed property to determine which jobs to display
const displayedJobs = computed(() => {
    if (showAllJobs.value) {
        return props.jobsPostedProps;
    } else {
        return props.jobsPostedProps.slice(0, initialJobsToShow);
    }
});

// Computed property to check if there are more jobs to show
const hasMoreJobs = computed(() => {
    return props.jobsPostedProps.length > initialJobsToShow;
});

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
</script>

<template>
    <Head title="Profile | iCan Careers" />
    <div class="min-h-[calc(100vh-4.625rem)] bg-[#f3f7fa]">
        <!-- Updated Header Section with Cover Photo - Facebook Style -->
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
                    accept="image/jpeg,image/jpg,image/png,image/bmp,image/webp"
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

                                    <!-- Camera icon overlay - Fixed position to stick to profile picture -->
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
                                    accept="image/jpeg,image/jpg,image/png,image/bmp,image/webp"
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
                        <p class="text-2xl font-bold text-gray-900">{{ user.name }}</p>
                        <Link
                            v-if="visitor"
                            :href="route('messages')"
                            :data="{ user: user.id }"
                            class="bi bi-chat-dots-fill text-[28px] text-blue-500 hover:cursor-pointer hover:text-blue-600 transition-colors"
                        ></Link>
                    </div>

                    <!-- Full Name -->
                    <div class="mb-4 max-w-2xl">
                        <div v-if="!isEditFullNameActive" class="flex items-center justify-center gap-2">
                            <p
                                @click="isEditFullNameActive = !visitor"
                                :class="[
                                    'text-lg text-gray-600 break-words text-center',
                                    { 'cursor-pointer hover:text-gray-800': !visitor }
                                ]"
                            >
                                {{ employerProfile.full_name || "No Name Provided" }}
                            </p>
                            <i
                                v-if="!visitor"
                                class="bi bi-pencil-square cursor-pointer text-gray-400 hover:text-gray-600 transition-colors"
                                @click="isEditFullNameActive = true"
                            ></i>
                        </div>
                        <form
                            v-if="isEditFullNameActive"
                            @submit.prevent="
                                isEditFullNameActive = false;
                                updateFullName();
                            "
                            class="flex items-center justify-center gap-2"
                        >
                            <input
                                type="text"
                                v-model="employerProfile.full_name"
                                class="rounded-lg border border-gray-300 px-3 py-1 text-center outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500"
                                placeholder="Enter your full name"
                                required
                            />
                            <button class="rounded-lg bg-green-500 px-3 py-1 text-white hover:bg-green-600 transition-colors">
                                Save
                            </button>
                        </form>
                    </div>

                    <!-- Subscription Status -->
                    <div class="flex flex-col items-center">
                        <div v-if="subscriptionProps">
                            <p class="font-bold">
                                <span :class="subscriptionClass">{{
                                    subscriptionProps.subscription_type
                                }}</span>
                            </p>
                        </div>
                        <p v-else>No active subscription.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto max-w-6xl px-4 py-8">
            <div class="grid gap-6 md:grid-cols-[1fr,250px] lg:grid-cols-[1fr,300px]">
                <div class="flex flex-col gap-4 text-[16px] text-gray-600">
                    <div class="rounded-lg bg-white p-8">
                        <div class="mb-3 flex items-center justify-between">
                            <p class="text-[20px] font-bold">Overview</p>

                            <div v-if="!visitor" class="">
                                <Link
                                    :href="route('employer.profile.edit')"
                                    class="flex items-center gap-1 text-orange-500 hover:text-orange-600"
                                >
                                    Edit Business Information
                                    <i class="bi bi-arrow-right"></i>
                                </Link>
                            </div>
                        </div>

                        <div
                            v-if="businessProps"
                            class="mb-4 flex items-center gap-4"
                        >
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-200"
                            >
                                <i class="bi bi-building"></i>
                            </div>
                            <Link
                                :href="
                                    route('businessinfo.show', businessProps.id)
                                "
                                class="group relative mb-3 text-blue-600 hover:underline"
                            >
                                <ProfileBusinessCard
                                    :business-id="businessProps.id"
                                >{{ businessProps.industry }}</ProfileBusinessCard>
                                <span
                                class="absolute z-[9999] left-1/2 top-full mt-2 -translate-x-1/2 whitespace-nowrap rounded-md bg-black px-2 py-1 text-xs text-white opacity-0 transition group-hover:opacity-100"
                            >
                                View Business Information
                                <!-- Tooltip Arrow -->
                                <span
                                    class="absolute left-1/2 top-0 -translate-x-1/2 -translate-y-full border-4 border-transparent border-b-black"
                                ></span>
                            </span>
                            </Link>
                        </div>

                        <p v-else class="mb-4 flex items-center gap-4">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-200"
                            >
                                <i class="bi bi-building"></i>
                            </div>
                            No business information available
                        </p>
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

                    <!-- Posted Jobs Section -->
                    <div class="col-span-1 rounded-lg bg-white p-4 shadow-sm mb-6">
                        <p class="mb-4 text-xl font-bold text-gray-800">
                            Posted Jobs
                        </p>

                        <!-- No Jobs -->
                        <div v-if="!jobsPostedProps.length" class="text-center text-gray-500">
                            No jobs posted yet.
                        </div>

                        <!-- Jobs List -->
                        <ul v-else class="space-y-4">
                            <li
                                v-for="job in displayedJobs"
                                :key="job.id"
                                class="flex items-center gap-4 rounded-md border p-4 shadow transition hover:shadow-md"
                            >
                                <!-- Business Logo -->
                                <Link
                                    :href="visitor
                                        ? adminVisit
                                            ? route('admin.show-job-post', job.id)
                                            : route('jobsearch.show', job.id)
                                        : route('employer.jobpost.show', job.id)
                                    "
                                >
                                    <img
                                    class="h-16 w-16 flex-shrink-0 rounded border object-contain"
                                    :src="
                                        job.employer?.business_information?.business_logo_url
                                            ? job.employer.business_information.business_logo_url
                                            : '/assets/logo-placeholder-image.png'
                                    "
                                    alt="Company Logo"
                                    />

                                </Link>

                                <!-- Job Info -->
                                <div class="flex-1">
                                    <Link
                                        :href="visitor
                                            ? adminVisit
                                                ? route('admin.show-job-post', job.id)
                                                : route('jobsearch.show', job.id)
                                            : route('employer.jobpost.show', job.id)
                                        "
                                    >
                                        <h3
                                            class="cursor-pointer text-lg font-semibold text-gray-900 hover:underline"
                                        >
                                            {{ job.job_title }}
                                        </h3>
                                    </Link>
                                    <p class="text-sm text-gray-700">
                                        {{ job.job_type }} • {{ job.work_arrangement }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Posted on:
                                        {{
                                            job.created_at
                                                ? new Date(job.created_at).toLocaleDateString()
                                                : "N/A"
                                        }}
                                    </p>
                                </div>

                                <!-- View Button -->
                                <Link
                                    :href="visitor
                                        ? adminVisit
                                            ? route('admin.show-job-post', job.id)
                                            : route('jobsearch.show', job.id)
                                        : route('employer.jobpost.show', job.id)
                                    "
                                    as="button"
                                    class="rounded-full bg-blue-100 p-2 text-blue-700 hover:bg-blue-200"
                                    aria-label="View Job"
                                >
                                    <i class="bi bi-arrow-right text-xl"></i>
                                </Link>
                            </li>
                        </ul>

                        <!-- Show More/Less Button -->
                        <div v-if="hasMoreJobs" class="mt-4 text-center">
                            <button
                                @click="showAllJobs = !showAllJobs"
                                class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100"
                            >
                                <span>{{ showAllJobs ? 'Show Less' : `Show More (${jobsPostedProps.length - initialJobsToShow} more)` }}</span>
                                <i
                                    :class="[
                                        'bi transition-transform duration-200',
                                        showAllJobs ? 'bi-chevron-up' : 'bi-chevron-down'
                                    ]"
                                ></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div>
                    <!-- Basic Information -->
                    <div class="mb-5 self-start rounded-lg bg-white p-8 text-gray-600 shadow">
                        <div class="mb-3 flex items-center justify-between">
                            <p class="text-[20px] font-bold">Basic Information</p>
                            <button
                                v-if="!visitor && !isEditingBasicInfo"
                                @click="isEditingBasicInfo = true"
                                class="flex items-center gap-1 text-orange-500 hover:text-orange-600"
                                title="Edit basic information"
                            >
                                <i class="bi bi-pencil-square text-lg"></i>
                                <span class="text-sm">Edit</span>
                            </button>
                        </div>

                        <div
                            v-if="!isEditingBasicInfo"
                        >
                            <div class="mb-2">
                                <label class="text-sm">Birth Year:</label>
                                <p>{{ age }}</p>
                            </div>
                            <div class="mb-2">
                                <label class="text-sm">Gender:</label>
                                <p>{{ employerProfile.gender }}</p>
                            </div>
                            <div class="mb-2">
                                <label class="text-sm">Phone Number:</label>
                                <p>{{ employerProfile.phone_number }}</p>
                            </div>
                            <div class="mb-2">
                                <label class="text-sm">Employer Type:</label>
                                <p>{{ employerProfile.employer_type }}</p>
                            </div>
                        </div>

                        <form
                            v-if="isEditingBasicInfo"
                            @submit.prevent="updateBasicInfo"
                        >
                            <div class="mb-2">
                                <label class="text-sm">Birth Year:</label>
                                <input
                                    type="number"
                                    v-model="employerProfile.birth_year"
                                    class="w-full border p-1"
                                    min="1925"
                                    max="2007"
                                />
                            </div>
                            <div class="mb-2">
                                <label class="text-sm">Gender:</label>
                                <select
                                    v-model="employerProfile.gender"
                                    class="w-full border p-1"
                                >
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="text-sm">Phone #:</label>
                                <input
                                    type="text"
                                    v-model="employerProfile.phone_number"
                                    class="w-full border p-1"
                                />
                            </div>
                            <button
                                class="mt-2 rounded bg-green-500 p-2 text-white"
                            >
                                Save
                            </button>
                            <button
                                type="button"
                                @click="isEditingBasicInfo = false"
                                class="ml-2 rounded bg-gray-500 p-2 text-white"
                            >
                                Cancel
                            </button>
                        </form>
                    </div>
                    <ProfilePage
                        :recentReview="recentReview"
                        :averageStar="averageStar"
                        :visitor="visitor"
                    ></ProfilePage>
                </div>
            </div>
        </div>

        <Teleport defer to="body">
            <Transition name="message">
                <div v-if="messageShow" class="fixed left-1/2 top-20 z-50 -translate-x-1/2 transform">
                    <div
                        :class="[
                            'flex items-center gap-2 rounded p-4 shadow-lg',
                            messageType === 'success'
                                ? 'bg-green-100 text-green-800 border border-green-300'
                                : 'bg-red-100 text-red-800 border border-red-300'
                        ]"
                    >
                        <i
                            :class="[
                                'bi',
                                messageType === 'success'
                                    ? 'bi-check-circle-fill'
                                    : 'bi-exclamation-circle-fill'
                            ]"
                        ></i>
                        <p class="message-display text-center font-medium">
                            {{ messageType === 'success' ? props.messageProp : props.messageProp }}
                        </p>
                    </div>
                </div>
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

.message-enter-active,
.message-leave-active {
    transition: all 0.5s ease;
}

.message-enter-from,
.message-leave-to {
    opacity: 0;
    transform: translate(-50%, -20px);
}
</style>
