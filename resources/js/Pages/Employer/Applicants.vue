<script setup>
import { Link, router, useForm, usePage } from "@inertiajs/vue3";
import {
    onMounted,
    onUnmounted,
    onUpdated,
    ref,
    TransitionGroup,
    useTemplateRef,
    watch,
} from "vue";
import SuccessfulMessage from "../Components/Popup/SuccessfulMessage.vue";
import { debounce } from "lodash";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import ReusableModal from "../Components/Modal/ReusableModal.vue";
import dayjs from "dayjs";
import InputFlashMessage from "../Components/InputFlashMessage.vue";
import Maps from "../Components/Maps.vue";
import utc from "dayjs/plugin/utc";
import timezone from "dayjs/plugin/timezone";

let props = defineProps({
    jobProps: null,
    applicantsProps: null,
    applicantsCount: null,
    statusCountProps: null,
    messageProp: null,
});

console.log(props.jobProps);

let page = usePage();

let showMessage = ref(false);

function showMessageProp() {
    showMessage.value = true;
    setTimeout(() => {
        showMessage.value = false;
    }, 2000);
}

let applicants = ref(props.applicantsProps);

let pendingCount = ref(props.statusCountProps.Pending ?? 0);
let underReviewCount = ref(props.statusCountProps["Under Review"] ?? 0);
let interviewCount = ref(props.statusCountProps["Interview Scheduled"] ?? 0);
let acceptedCount = ref(props.statusCountProps.Accepted ?? 0);
let rejectedCount = ref(props.statusCountProps.Rejected ?? 0);

let jobId = ref(null);
onMounted(() => {
    jobId.value = route().params.jobid;
    search.value = route().params.q;
});

let lastTagValueClicked = ref(null);

function showSpecificStatus(status, event) {
    if (!lastTagValueClicked.value) {
        if (status) {
            applicants.value = props.applicantsProps.filter((e, index) => {
                return e.pivot.status === status;
            });
        } else {
            applicants.value = props.applicantsProps;
        }

        lastTagValueClicked.value = status ?? lastTagValueClicked.value;
    } else {
        if (lastTagValueClicked.value != status) {
            applicants.value = props.applicantsProps.filter((e, index) => {
                return e.pivot.status === (status ?? lastTagValueClicked.value);
            });
            lastTagValueClicked.value = status ?? lastTagValueClicked.value;

            return;
        }

        applicants.value = props.applicantsProps;

        lastTagValueClicked.value = null;
    }
}

function updateCount(indexOfApplicant, statusAddCount) {
    if (applicants.value[indexOfApplicant].pivot.status === "Pending") {
        pendingCount.value--;
    }
    if (applicants.value[indexOfApplicant].pivot.status === "Under Review") {
        underReviewCount.value--;
    }
    if (
        applicants.value[indexOfApplicant].pivot.status ===
        "Interview Scheduled"
    ) {
        interviewCount.value--;
    }
    if (applicants.value[indexOfApplicant].pivot.status === "Accepted") {
        acceptedCount.value--;
    }
    if (applicants.value[indexOfApplicant].pivot.status === "Rejected") {
        rejectedCount.value--;
    }

    if (statusAddCount === "Pending") {
        pendingCount.value++;
    }

    if (statusAddCount === "Under Review") {
        underReviewCount.value++;
    }
    if (statusAddCount === "Interview Scheduled") {
        interviewCount.value++;
    }
    if (statusAddCount === "Accepted") {
        acceptedCount.value++;
    }
    if (statusAddCount === "Rejected") {
        rejectedCount.value++;
    }
}
let applicationPivotId = ref(null);
let setInterview = ref(false);
function updateStatus(applicationId, e) {
    if (e.target.value === "Interview Scheduled") {
        applicationPivotId.value = applicationId;
        setInterview.value = true;
        return;
    }

    if (e.target.value != "") {
        if (confirm("Are you sure you want to update the status?")) {
            router.post(
                route("job.applicants.update.status", {
                    pivotId: applicationId,
                }),
                {
                    _method: "put",
                    status: e.target.value,
                },
                {
                    onSuccess: () => {
                        showMessageProp();
                        if (e.target.value) {
                        }
                        let indexOfApplicant = applicants.value.findIndex(
                            (applicant) => {
                                return applicant.pivot.id === applicationId;
                            },
                        );

                        updateCount(indexOfApplicant, e.target.value);

                        applicants.value[indexOfApplicant].pivot.status =
                            e.target.value;

                        showSpecificStatus();
                        closeModal();
                    },
                    onError: (e) => {
                        showMessageProp();
                        console.log(e);
                    },
                    preserveState: true,
                    preserveScroll: true,
                },
            );
        } else {
            console.log("232");
            e.target.value = "";
            closeModal();
        }
    }
}

let search = ref(null);
function submit() {
    router.get(
        `/employers/applicants/${jobId.value}`,
        {
            q: search.value,
        },
        {
            preserveState: true,
            onSuccess: () => {
                showSpecificStatus();
            },
        },
    );
}

watch(search, debounce(submit, 500));

let now = ref(dayjs().format("YYYY-MM-DD"));
let intervalId;
onMounted(() => {
    intervalId = setInterval(() => {
        now.value = updateTime();
    }, 2000);
});

onUnmounted(() => {
    clearInterval(intervalId);
});

function updateTime() {
    return dayjs().format("H:mm");
}

let coordinates = ref(null);

function setCoordinates(coord) {
    coordinates.value = coord;
}

let date = ref(null);
let time = ref(null);
let interviewMode = ref("remote");

let errorMessage = ref(null);
function schedInterview(e) {
    if (!date.value || !time.value) {
        errorMessage.value = "Please fill all the field.";
        return;
    }

    if (interviewMode.value === "onsite" && !coordinates.value) {
        coordinates.value = [120.9842, 14.5995];
    }

    router.put(
        route("job.applicants.addinterview", {
            pivotId: applicationPivotId.value,
        }),
        {
            date: date.value,
            time: time.value,
            interview_mode: interviewMode.value,
            coordinates: coordinates.value,
            timezone: userTz,
        },
        {
            onSuccess: () => {
                showMessageProp();
                let indexOfApplicant = applicants.value.findIndex(
                    (applicant) => {
                        return applicationPivotId.value === applicant.pivot.id;
                    },
                );

                updateCount(indexOfApplicant, "Interview Scheduled");

                applicants.value[indexOfApplicant].pivot.status =
                    "Interview Scheduled";

                showSpecificStatus();
                applicationPivotId.value = null;
                closeModal();
                setInterview.value = false;
            },
            onError: () => {
                applicationPivotId.value = null;
                showMessageProp();
                event.target.value = "";
                closeModal();
                setInterview.value = false;
            },
            preserveState: true,
            preserveScroll: true,
        },
    );
}

let showModal = ref(false);

function closeModal(e) {
    if (e instanceof Event) {
        e.target.value = "";
    }
    if (event instanceof Event) {
        event.target.value = "";
    }
    showModal.value = false;
    setInterview.value = false;
}
let event;
function openModal(e) {
    event = e;
    showModal.value = true;
}

let applicantData = ref(null);

dayjs.extend(utc);
dayjs.extend(timezone);

// get the timezone of the user
const userTz = Intl.DateTimeFormat().resolvedOptions().timeZone;
console.log(userTz);
</script>

<template>
    <Head title="Applicants| iCan Careers" />
    <div class="min-h-[calc(100vh-4.625rem)] bg-gray-50 py-6">
        <div class="container mx-auto max-w-7xl px-4">
            <!-- Header Section -->
            <div class="mb-6">
                <div
                    class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            {{ jobProps.job_title }}
                        </h1>
                        <div class="mt-2 flex items-center gap-4">
                            <p class="text-gray-600">
                                <span class="font-medium">{{
                                    applicantsCount
                                }}</span>
                                applicants
                            </p>
                            <p
                                v-if="
                                    page.props.auth.user.employer.subscription
                                        .subscription_type === 'Free'
                                "
                                class="flex items-center gap-1 text-sm font-medium text-amber-600"
                            >
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                You cannot hire applicants in free tier.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search Section -->
            <div class="mb-6 space-y-4">
                <!-- Status Filters -->
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="status in [
                            {
                                label: 'Pending',
                                count: pendingCount,
                                value: 'Pending',
                            },
                            {
                                label: 'Under Review',
                                count: underReviewCount,
                                value: 'Under Review',
                            },
                            {
                                label: 'Interview',
                                count: interviewCount,
                                value: 'Interview Scheduled',
                            },
                            {
                                label: 'Accepted',
                                count: acceptedCount,
                                value: 'Accepted',
                            },
                            {
                                label: 'Rejected',
                                count: rejectedCount,
                                value: 'Rejected',
                            },
                        ]"
                        :key="status.value"
                        @click="showSpecificStatus(status.value, $event)"
                        :class="[
                            'rounded-full border px-4 py-2 text-sm font-medium transition-all duration-200',
                            lastTagValueClicked === status.value
                                ? 'border-gray-900 bg-gray-900 text-white shadow-sm'
                                : 'border-gray-300 bg-white text-gray-700 hover:border-gray-400 hover:shadow-sm',
                        ]"
                    >
                        {{ status.label }} ({{ status.count }})
                    </button>
                </div>

                <!-- Search -->
                <div class="flex justify-end">
                    <div class="relative w-full sm:w-64">
                        <i
                            class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 transform text-gray-400"
                        ></i>
                        <input
                            v-model="search"
                            type="text"
                            class="w-full rounded-lg border border-gray-300 py-2 pl-10 pr-4 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                            placeholder="Search applicant..."
                        />
                    </div>
                </div>
            </div>

            <!-- Applicants Table -->
            <div
                class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
            >
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-gray-200 bg-gray-50">
                            <tr
                                class="text-left text-sm font-medium text-gray-700"
                            >
                                <th class="px-6 py-4 font-semibold">
                                    Applicant
                                </th>
                                <th class="px-6 py-4 font-semibold">Resume</th>
                                <th class="px-6 py-4 font-semibold">Profile</th>
                                <th class="px-6 py-4 font-semibold">Status</th>
                                <th class="px-6 py-4 font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <TransitionGroup name="applicant">
                                <tr
                                    v-for="applicant in applicants"
                                    :key="applicant.id"
                                    class="transition-colors hover:bg-gray-50"
                                >
                                    <!-- Applicant Info -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-10 w-10 flex-shrink-0"
                                            >
                                                <img
                                                    class="h-10 w-10 rounded-full border border-gray-200 object-cover"
                                                    :src="
                                                        applicant.profile_img_url ||
                                                        '/assets/profile_placeholder.jpg'
                                                    "
                                                    @error="
                                                        (event) =>
                                                            (event.target.src =
                                                                '/assets/profile_placeholder.jpg')
                                                    "
                                                    alt="Profile"
                                                />
                                            </div>
                                            <div>
                                                <p
                                                    class="font-medium text-gray-900"
                                                >
                                                    {{ applicant.name }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Resume -->
                                    <td class="px-6 py-4">
                                        <a
                                            :href="
                                                applicant.worker_profile
                                                    .resume_url
                                            "
                                            target="_blank"
                                            class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 transition-colors hover:text-blue-700"
                                        >
                                            <i
                                                class="bi bi-file-earmark-text"
                                            ></i>
                                            View Resume
                                        </a>
                                    </td>

                                    <!-- Profile -->
                                    <td class="px-6 py-4">
                                        <Link
                                            :href="
                                                route(
                                                    'worker.show.profile',
                                                    applicant.id,
                                                )
                                            "
                                            class="inline-flex items-center gap-1 text-gray-600 transition-colors hover:text-gray-800"
                                        >
                                            <i
                                                class="bi bi-box-arrow-up-right text-sm"
                                            ></i>
                                            View Profile
                                        </Link>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4">
                                        <span
                                            :class="[
                                                'inline-flex items-center rounded-full px-3 py-1 text-xs font-medium',
                                                {
                                                    'bg-amber-100 text-amber-800':
                                                        applicant.pivot
                                                            .status ===
                                                        'Pending',
                                                    'bg-blue-100 text-blue-800':
                                                        applicant.pivot
                                                            .status ===
                                                        'Under Review',
                                                    'bg-purple-100 text-purple-800':
                                                        applicant.pivot
                                                            .status ===
                                                        'Interview Scheduled',
                                                    'bg-green-100 text-green-800':
                                                        applicant.pivot
                                                            .status ===
                                                        'Accepted',
                                                    'bg-red-100 text-red-800':
                                                        applicant.pivot
                                                            .status ===
                                                        'Rejected',
                                                },
                                            ]"
                                        >
                                            {{ applicant.pivot.status }}
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4">
                                        <button
                                            @click="
                                                applicantData = applicant;
                                                showModal = true;
                                            "
                                            :disabled="
                                                page.props.auth.user.employer
                                                    .subscription
                                                    .subscription_type ===
                                                'Free'
                                            "
                                            :class="[
                                                'rounded-lg p-2 transition-all duration-200',
                                                page.props.auth.user.employer
                                                    .subscription
                                                    .subscription_type ===
                                                'Free'
                                                    ? 'cursor-not-allowed text-gray-400'
                                                    : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900',
                                            ]"
                                        >
                                            <i
                                                class="bi bi-gear-fill text-lg"
                                            ></i>
                                        </button>
                                    </td>
                                </tr>
                            </TransitionGroup>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <ReusableModal v-if="showModal" @closeModal="closeModal">
        <div class="w-full max-w-2xl rounded-2xl bg-white shadow-xl">
            <!-- Header -->
            <div
                class="flex items-center justify-between border-b border-gray-200 p-6"
            >
                <h2 class="text-xl font-semibold text-gray-900">
                    Manage Applicant
                </h2>
                <button
                    @click="closeModal"
                    class="rounded-lg p-2 transition-colors hover:bg-gray-100"
                >
                    <i class="bi bi-x-lg text-gray-500"></i>
                </button>
            </div>

            <!-- Applicant Info -->
            <div class="border-b border-gray-200 p-6">
                <div class="flex items-center gap-4">
                    <div class="h-16 w-16">
                        <img
                            class="h-16 w-16 rounded-full border border-gray-200 object-cover"
                            :src="
                                applicantData?.profile_img_url ||
                                '/assets/profile_placeholder.jpg'
                            "
                            @error="
                                (event) =>
                                    (event.target.src =
                                        '/assets/profile_placeholder.jpg')
                            "
                            alt="Profile"
                        />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ applicantData?.name }}
                        </h3>
                        <div class="mt-1 flex items-center gap-4">
                            <a
                                :href="
                                    applicantData?.worker_profile?.resume_url
                                "
                                target="_blank"
                                class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-700"
                            >
                                <i class="bi bi-file-earmark-text"></i>
                                Resume
                            </a>
                            <Link
                                :href="
                                    route(
                                        'worker.show.profile',
                                        applicantData?.id,
                                    )
                                "
                                class="inline-flex items-center gap-1 text-sm font-medium text-gray-600 hover:text-gray-800"
                            >
                                <i class="bi bi-box-arrow-up-right"></i>
                                Profile
                            </Link>
                        </div>
                    </div>
                    <div
                        :class="[
                            'rounded-full px-3 py-1 text-sm font-medium',
                            {
                                'bg-amber-100 text-amber-800':
                                    applicantData?.pivot?.status === 'Pending',
                                'bg-blue-100 text-blue-800':
                                    applicantData?.pivot?.status ===
                                    'Under Review',
                                'bg-purple-100 text-purple-800':
                                    applicantData?.pivot?.status ===
                                    'Interview Scheduled',
                                'bg-green-100 text-green-800':
                                    applicantData?.pivot?.status === 'Accepted',
                                'bg-red-100 text-red-800':
                                    applicantData?.pivot?.status === 'Rejected',
                            },
                        ]"
                    >
                        {{ applicantData?.pivot?.status }}
                    </div>
                </div>
            </div>

            <!-- Status Management -->
            <div class="max-h-96 space-y-6 overflow-y-auto p-6">
                <!-- Pending State -->
                <div
                    v-if="applicantData?.pivot?.status === 'Pending'"
                    class="space-y-4"
                >
                    <h4 class="font-medium text-gray-900">Update Status</h4>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <button
                            @click="
                                updateStatus(applicantData.pivot.id, {
                                    target: { value: 'Under Review' },
                                })
                            "
                            class="group rounded-lg border border-blue-200 bg-blue-50 p-3 text-left transition-colors hover:bg-blue-100"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 transition-colors group-hover:bg-blue-200"
                                >
                                    <i class="bi bi-eye text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-blue-900">
                                        Under Review
                                    </p>
                                    <p class="text-sm text-blue-600">
                                        Move to next stage
                                    </p>
                                </div>
                            </div>
                        </button>
                        <button
                            @click="
                                updateStatus(applicantData.pivot.id, {
                                    target: { value: 'Rejected' },
                                })
                            "
                            class="group rounded-lg border border-red-200 bg-red-50 p-3 text-left transition-colors hover:bg-red-100"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100 transition-colors group-hover:bg-red-200"
                                >
                                    <i class="bi bi-x-lg text-red-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-red-900">
                                        Reject
                                    </p>
                                    <p class="text-sm text-red-600">
                                        Decline application
                                    </p>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Under Review State -->
                <div
                    v-if="applicantData?.pivot?.status === 'Under Review'"
                    class="space-y-4"
                >
                    <h4 class="font-medium text-gray-900">Next Steps</h4>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <button
                            @click="
                                applicationPivotId = applicantData.pivot.id;
                                setInterview = true;
                            "
                            class="group rounded-lg border border-green-200 bg-green-50 p-3 text-left transition-colors hover:bg-green-100"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100 transition-colors group-hover:bg-green-200"
                                >
                                    <i
                                        class="bi bi-calendar-check text-green-600"
                                    ></i>
                                </div>
                                <div>
                                    <p class="font-medium text-green-900">
                                        Schedule Interview
                                    </p>
                                    <p class="text-sm text-green-600">
                                        Set up meeting
                                    </p>
                                </div>
                            </div>
                        </button>
                        <button
                            @click="
                                updateStatus(applicantData.pivot.id, {
                                    target: { value: 'Rejected' },
                                })
                            "
                            class="group rounded-lg border border-red-200 bg-red-50 p-3 text-left transition-colors hover:bg-red-100"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100 transition-colors group-hover:bg-red-200"
                                >
                                    <i class="bi bi-x-lg text-red-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-red-900">
                                        Reject
                                    </p>
                                    <p class="text-sm text-red-600">
                                        Decline application
                                    </p>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Interview Scheduled State -->
                <div
                    v-if="
                        applicantData?.pivot?.status === 'Interview Scheduled'
                    "
                    class="space-y-4"
                >
                    <!-- Interview Details -->
                    <div
                        class="rounded-lg border border-purple-200 bg-purple-50 p-4"
                    >
                        <h4 class="mb-2 font-medium text-purple-900">
                            Scheduled Interview
                        </h4>
                        <div class="space-y-1 text-sm text-purple-800">
                            <p class="flex items-center gap-2">
                                <i class="bi bi-calendar"></i>
                                {{
                                    dayjs(
                                        applicantData.pivot.interview_schedule,
                                    )
                                        .utc(userTz)
                                        .format("MMMM D, YYYY")
                                }}
                            </p>
                            <p class="flex items-center gap-2">
                                <i class="bi bi-clock"></i>
                                {{
                                    dayjs(
                                        applicantData.pivot.interview_schedule,
                                    )
                                        .utc(userTz)
                                        .format("h:mm A")
                                }}
                            </p>
                            <p class="flex items-center gap-2">
                                <i class="bi bi-geo-alt"></i>
                                {{
                                    applicantData.pivot.interview_mode ===
                                    "remote"
                                        ? "Remote Meeting"
                                        : "On-site"
                                }}
                            </p>
                        </div>
                    </div>

                    <h4 class="font-medium text-gray-900">Update Status</h4>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <button
                            @click="
                                applicationPivotId = applicantData.pivot.id;
                                setInterview = true;
                            "
                            class="group rounded-lg border border-blue-200 bg-blue-50 p-3 text-left transition-colors hover:bg-blue-100"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 transition-colors group-hover:bg-blue-200"
                                >
                                    <i
                                        class="bi bi-arrow-repeat text-blue-600"
                                    ></i>
                                </div>
                                <div>
                                    <p class="font-medium text-blue-900">
                                        Reschedule
                                    </p>
                                    <p class="text-sm text-blue-600">
                                        Change time/date
                                    </p>
                                </div>
                            </div>
                        </button>
                        <button
                            @click="
                                updateStatus(applicantData.pivot.id, {
                                    target: { value: 'Accepted' },
                                })
                            "
                            class="group rounded-lg border border-green-200 bg-green-50 p-3 text-left transition-colors hover:bg-green-100"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100 transition-colors group-hover:bg-green-200"
                                >
                                    <i
                                        class="bi bi-check-lg text-green-600"
                                    ></i>
                                </div>
                                <div>
                                    <p class="font-medium text-green-900">
                                        Accept
                                    </p>
                                    <p class="text-sm text-green-600">
                                        Hire applicant
                                    </p>
                                </div>
                            </div>
                        </button>
                        <button
                            @click="
                                updateStatus(applicantData.pivot.id, {
                                    target: { value: 'Rejected' },
                                })
                            "
                            class="group rounded-lg border border-red-200 bg-red-50 p-3 text-left transition-colors hover:bg-red-100"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100 transition-colors group-hover:bg-red-200"
                                >
                                    <i class="bi bi-x-lg text-red-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-red-900">
                                        Reject
                                    </p>
                                    <p class="text-sm text-red-600">
                                        Decline application
                                    </p>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Final States -->
                <div
                    v-if="
                        ['Accepted', 'Rejected'].includes(
                            applicantData?.pivot?.status,
                        )
                    "
                    class="py-8 text-center"
                >
                    <div
                        :class="[
                            'inline-flex flex-col items-center rounded-2xl p-6',
                            applicantData?.pivot?.status === 'Accepted'
                                ? 'border border-green-200 bg-green-50'
                                : 'border border-red-200 bg-red-50',
                        ]"
                    >
                        <i
                            :class="[
                                'mb-3 text-4xl',
                                applicantData?.pivot?.status === 'Accepted'
                                    ? 'bi bi-check-circle-fill text-green-500'
                                    : 'bi bi-x-circle-fill text-red-500',
                            ]"
                        ></i>
                        <h4 class="mb-1 font-semibold text-gray-900">
                            {{
                                applicantData?.pivot?.status === "Accepted"
                                    ? "Application Accepted"
                                    : "Application Rejected"
                            }}
                        </h4>
                        <p
                            :class="[
                                'text-sm',
                                applicantData?.pivot?.status === 'Accepted'
                                    ? 'text-green-700'
                                    : 'text-red-700',
                            ]"
                        >
                            {{
                                applicantData?.pivot?.status === "Accepted"
                                    ? "This applicant has been successfully hired."
                                    : "This application has been declined."
                            }}
                        </p>
                    </div>
                </div>

                <!-- Interview Scheduling Form -->
                <div
                    v-if="setInterview"
                    class="space-y-4 border-t border-gray-200 pt-4"
                >
                    <h4 class="font-medium text-gray-900">
                        Schedule Interview
                    </h4>
                    <form @submit.prevent="schedInterview" class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label
                                    class="mb-1 block text-sm font-medium text-gray-700"
                                    >Date</label
                                >
                                <input
                                    type="date"
                                    :min="
                                        dayjs()
                                            .tz(userTz)
                                            .add(12, 'hour')
                                            .format('YYYY-MM-DD')
                                    "
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                                    v-model="date"
                                    required
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-sm font-medium text-gray-700"
                                    >Time</label
                                >
                                <input
                                    type="time"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                                    v-model="time"
                                    required
                                />
                            </div>
                        </div>

                        <InputFlashMessage
                            v-if="errorMessage"
                            type="error"
                            :message="errorMessage"
                        />

                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-gray-700"
                                >Interview Mode</label
                            >
                            <select
                                v-model="interviewMode"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="remote">Remote</option>
                                <option value="onsite">On Site</option>
                            </select>
                        </div>

                        <div v-if="interviewMode === 'onsite'" class="mb-3">
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700"
                                >Location</label
                            >
                            <Maps
                                @update:coordinates="setCoordinates"
                                :centerProps="jobProps.location"
                                :markedCoordinatesProps="jobProps.location"
                            ></Maps>
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button
                                type="button"
                                @click="setInterview = false"
                                class="rounded-lg border border-gray-300 px-4 py-2 font-medium text-gray-700 transition-colors hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="rounded-lg bg-blue-600 px-4 py-2 font-medium text-white transition-colors hover:bg-blue-700"
                            >
                                Schedule Interview
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </ReusableModal>

    <SuccessfulMessage
        :type="page.props.errors?.message ? 'Error' : 'Success'"
        :messageShow="showMessage"
        :messageProp="page.props.errors?.message ?? messageProp"
    ></SuccessfulMessage>
</template>

<style scoped>
.applicant-move,
.applicant-enter-active {
    transition: all 0.3s ease-in-out;
}

.applicant-enter-from,
.applicant-leave-to {
    opacity: 0;
    transform: translateY(10px);
}

.applicant-leave-active {
    position: absolute;
}
</style>
