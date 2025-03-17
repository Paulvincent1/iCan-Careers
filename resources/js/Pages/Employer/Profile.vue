<script setup>
import { computed, getCurrentInstance, ref, watch, watchEffect } from "vue";
import dayjs from "dayjs";
import { Link, router } from "@inertiajs/vue3";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import ReusableModal from "../Components/Modal/ReusableModal.vue";

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
});

const memberSince = computed(() => {
    return dayjs(props.user.created_at).format("MMMM DD, YYYY");
});

let messageShow = ref(false);
let profilePreview = ref(props.user.profile_img);
let employerProfile = ref({ ...props.employerProfileProp });
let isEditFullNameActive = ref(false);
let isEditingBasicInfo = ref(false);
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

function submitReport(reason) {
    router.post(
        route("report.user", { userId: route().params.employerId }),
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
        <div class="relative h-32 bg-[#FAFAFA]">
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
                    <p class="text-lg">{{ user.name }}</p>
                    <Link
                        v-if="visitor"
                        :href="route('messages')"
                        :data="{ user: user.id }"
                        class="bi bi-chat-dots text-lg text-blue-500 hover:cursor-pointer"
                    ></Link>
                </div>
                <div class="mb-3">
                    <p
                        @click="isEditFullNameActive = true"
                        v-if="!isEditFullNameActive"
                        :class="[
                            'max-w-[600px] cursor-pointer break-words text-center text-[24px] font-bold hover:underline',
                            {
                                'pointer-events-none': visitor,
                            },
                        ]"
                    >
                        {{ employerProfile.full_name || "No Name Provided" }}
                    </p>
                    <form
                        @submit.prevent="
                            isEditFullNameActive = false;
                            updateFullName();
                        "
                        v-if="isEditFullNameActive"
                    >
                        <input
                            type="text"
                            v-model="employerProfile.full_name"
                            class="mr-2 rounded border p-1 outline-none ring-green-300 transition-all hover:ring-1 focus:ring-1"
                            required
                        />
                        <button
                            @click=""
                            class="rounded bg-green-500 p-1 text-white"
                        >
                            Save
                        </button>
                    </form>
                </div>
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
        <div
            class="xs container mx-auto px-[0.5rem] md:px-[3rem] lg:px-[9rem] xl:max-w-7xl"
        >
            <div
                class="mt-8 grid gap-6 md:grid-cols-[1fr,250px] lg:grid-cols-[1fr,300px]"
            >
                <div class="flex flex-col gap-4 text-[16px] text-gray-600">
                    <div class="rounded-lg bg-white p-8">
                        <div class="mb-3 flex items-center justify-between">
                            <p class="text-[20px] font-bold">Overview</p>
                            <button
                                v-if="visitor && !adminVisit"
                                @click="isShowReportModal = true"
                            >
                                <i
                                    class="bi bi-exclamation-diamond-fill text-red-600"
                                ></i>
                            </button>
                        </div>
                        <div class="mb-4 flex items-center gap-4">
                            <!-- <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-200"
                            >
                                <i class="bi bi-bag font-bold"></i>
                            </div> -->
                            <!-- <p
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
                            </p> -->
                            <!-- <form
                                v-if="isEditProfileActive"
                                @submit.prevent="
                                    updateWorkDetails();
                                    isEditProfileActive = false;
                                "
                            >
                                <WorkDetailsForm
                                    v-model:jobType="workerProfile.job_type"
                                    v-model:workHourPerDay="
                                        workerProfile.work_hour_per_day
                                    "
                                    v-model:hourPay="workerProfile.hour_pay"
                                    v-model:monthPay="workerProfile.month_pay"
                                /> 
                                <button
                                    class="rounded bg-green-500 p-1 text-white"
                                >
                                    Save
                                </button>
                            </form> -->
                        </div>
                        <div
                            class="mb-4 flex items-center gap-4"
                            v-if="businessProps"
                        >
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-200"
                            >
                                <i class="bi bi-building"></i>
                            </div>
                            <p>
                                {{
                                    businessProps?.industry ||
                                    "No business information available"
                                }}
                            </p>
                        </div>
                        <p v-else class="mb-1 text-gray-500">
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
                    <div
                        class="max-w-2xl rounded-lg bg-white p-8 text-gray-600 shadow"
                    >
                        <p class="mb-3 text-[20px] font-bold">Posted Jobs</p>

                        <!-- Business Info Section -->
                        <div class="mb-4 grid gap-4">
                            <div v-if="jobsPostedProps.length">
                                <div
                                    v-for="job in jobsPostedProps"
                                    :key="job.id"
                                    class="mb-4 transform overflow-hidden rounded-lg border bg-white text-[#fa8334] shadow-xl shadow-black/10 transition-all duration-300 hover:scale-105 hover:shadow-lg"
                                >
                                    <Link
                                        :href="
                                            visitor
                                                ? adminVisit
                                                    ? route(
                                                          'admin.show-job-post',
                                                          job.id,
                                                      )
                                                    : route(
                                                          'jobsearch.show',
                                                          job.id,
                                                      )
                                                : route(
                                                      'employer.jobpost.show',
                                                      job.id,
                                                  )
                                        "
                                        class="block h-full w-full p-4 transition-colors duration-300 hover:bg-gray-100"
                                    >
                                        <p
                                            class="text-green- x00 text-xl font-bold"
                                        >
                                            {{ job.job_title }}
                                        </p>
                                        <p class="text-gray-600">
                                            {{ job.job_type }} -
                                            {{ job.work_arrangement }}
                                        </p>
                                    </Link>
                                </div>
                            </div>
                            <p v-else>No jobs posted yet.</p>
                        </div>
                    </div>

                    <!-- <div
                        class="mb-8 rounded-lg bg-white p-8 text-gray-600 shadow"
                    >
                        <div class="mb-4 flex justify-between">
                            <p class="text-[20px] font-bold">Top Skills</p>
                            <button
                                v-if="!visitor"
                                @click="isOpenUpdateValue(true)"
                                class="rounded-xl bg-green-400 px-2 text-white"
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
                    </div> -->
                </div>
                <div
                    class="self-start rounded-lg bg-white p-8 text-gray-600 shadow"
                >
                    <p class="mb-3 text-[20px] font-bold">Basic Information</p>

                    <div
                        v-if="!isEditingBasicInfo"
                        @click="
                            visitor
                                ? (isEditingBasicInfo = false)
                                : (isEditingBasicInfo = true)
                        "
                        :class="[
                            'cursor-pointer',
                            {
                                'pointer-events-none': visitor,
                            },
                        ]"
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
                        <div class="mb-2">
                            <label class="text-sm">Website / Account:</label>
                            <!-- <p>{{ employerProfile.website }}</p> -->
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
                        <!-- <div class="mb-2">
                            <label class="text-sm">Employer Type:</label>
                            <input
                                type="text"
                                v-model="employerProfile.employer_type"
                                class="w-full border p-1"
                            />
                        </div> -->
                        <!-- <div class="mb-2">
                            <label class="text-sm">Website / Account:</label>
                            <input
                                type="url"
                                v-model="employerProfile.website"
                                class="w-full border p-1"
                            />
                        </div> -->
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
</style>
