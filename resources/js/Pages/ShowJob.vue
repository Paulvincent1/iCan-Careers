<script setup>
import { uniqueId } from "lodash";
import Maps from "./Components/Maps.vue";
import { ref, watchEffect } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import ReusableModal from "./Components/Modal/ReusableModal.vue";
import SuccessfulMessage from "./Components/Popup/SuccessfulMessage.vue";
import InputFlashMessage from "./Components/InputFlashMessage.vue";
import { route } from "../../../vendor/tightenco/ziggy/src/js";
import dayjs from "dayjs";
import utc from "dayjs/plugin/utc";
import timezone from "dayjs/plugin/timezone";

let props = defineProps({
    jobPostProps: null,
    workerProfileProps: null,
    interviewDetails: null,
    messageProp: null,
});

let page = usePage();
console.log(page.props.auth.user.role.name);

let messageShow = ref(false);
function showMessage() {
    console.log(props.messageProp);

    if (props.messageProp) {
        messageShow.value = true;
        setTimeout(() => {
            messageShow.value = false;
        }, 2000);
    }
}

watchEffect(() => {
    if (props.messageProp) {
        showMessage();
    }
});

console.log(props.jobPostProps);

let preferredWorkers = ref(props.jobPostProps.preferred_worker_types);
swapArrayValues();
function swapArrayValues() {
    let senior = preferredWorkers.value.indexOf("Seniors Citizens");

    if (senior !== -1 && senior !== 0) {
        [preferredWorkers.value[0], preferredWorkers.value[senior]] = [
            preferredWorkers.value[senior],
            preferredWorkers.value[0],
        ];
    }
}

let isSaved = ref(props.jobPostProps.users_who_saved?.length ?? 0);

function saveJob() {
    if (isSaved.value === 0) {
        isSaved.value = 1;
        console.log("hui");
    } else {
        console.log("hello");

        isSaved.value = 0;
    }
}

let showModal = ref(false);
function closeModal() {
    showModal.value = false;
}

let isApplied = ref(props.jobPostProps.users_who_applied?.length ?? 0);
let inputErrorResume = ref(null);
function submitResume() {
    if (!props.workerProfileProps.resume_path) {
        inputErrorResume.value = "Please add resume first.";
        return;
    }
    closeModal();
    router.post(
        route("jobsearch.apply", props.jobPostProps.id),
        {},
        {
            onSuccess: () => {
                isApplied.value = 1;
            },
        },
    );
}

function addResume(e) {
    router.post(
        "/jobseekers/myprofile/updateprofile",
        {
            _method: "put",
            resume: e.target.files[0],
        },
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {},
        },
    );
}

// employer side
let isClosed = ref(props.jobPostProps.job_status === "Closed");
function closeJob() {
    isClosed.value = true;
}

const reports = [
    {
        reason: "Payment Concerns",
        descriptions: [
            "Unclear or Missing Payment Details",
            "Unrealistic Pay for Work",
            "Asking for Free Work",
            "Delayed or No Payment History",
        ],
    },
    {
        reason: "Misleading Job Information",
        descriptions: [
            "Fake Job Posting",
            "Misleading Job Description",
            "Unrealistic Job Expectations",
            "Job Removed After Application",
        ],
    },
    {
        reason: "Unprofessional or Unethical Behavior",
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
            "Spam or Scam Job Post",
            "Violates Platform Policies",
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
    console.log(route().params.id);

    router.post(
        route("report.job-post", { jobpostId: route().params.id }),
        {
            reason,
        },
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                isShowReportModal.value = false;
                showMessage();
            },
        },
    );
}

dayjs.extend(utc);
dayjs.extend(timezone);

// get the timezone of the user
const userTz = Intl.DateTimeFormat().resolvedOptions().timeZone;
</script>
<template>
    <Head title="Show Job | iCan Careers" />
    <div class="flex min-h-[calc(100vh-4.625rem)] flex-col text-[#171816]">
        <div class="relative h-32 bg-[#FAFAFA]">
            <div class="xs container mx-auto px-[0.5rem] xl:max-w-7xl">
                <div
                    class="absolute left-[50%] top-[50px] flex h-32 w-32 translate-x-[-50%] flex-col items-center"
                >
                    <img
                        class="h-full w-full rounded-lg object-obtain"
                        :src="
                            jobPostProps.employer.employer_profile
                                .business_information
                                ? jobPostProps.employer.employer_profile
                                      .business_information.business_logo
                                : '/assets/logo-placeholder-image.png'
                        "
                        alt=""
                    />
                    <Link
                        v-if="
                            page.props.auth.user.role.name === 'Senior' ||
                            page.props.auth.user.role.name === 'PWD'
                        "
                        @click="saveJob"
                        as="button"
                        method="post"
                        preserve-scroll
                        :href="route('jobsearch.save.job', jobPostProps.id)"
                        class=""
                    >
                        <i
                            :class="[
                                'bi absolute bottom-[-5px] right-[-10px] text-lg text-orange-500',
                                {
                                    'bi-bookmark-dash': !isSaved,
                                    'bi-bookmark-dash-fill': isSaved,
                                },
                            ]"
                        ></i>
                    </Link>
                    <Link
                        v-else
                        v-if="!isClosed"
                        as="button"
                        method="get"
                        preserve-scroll
                        :href="route('employer.jobpost.edit', jobPostProps.id)"
                        class=""
                    >
                        <i
                            class="bi bi-pencil-fill absolute bottom-[-5px] right-[-10px] text-lg text-orange-500"
                        ></i>
                    </Link>
                </div>
            </div>
        </div>
        <div class="xs container mx-auto mt-12 px-[0.5rem] xl:max-w-7xl">
            <p
                class="my-2 break-words text-center text-[18px] font-medium md:text-[24px]"
            >
                {{ jobPostProps.job_title }}
            </p>
            <Link
                :href="
                    route('visit.employer.profile', jobPostProps.employer.id)
                "
                class="flex items-center justify-center gap-2"
            >
                <p class="text-center text-sm text-gray-500 underline">
                    {{ jobPostProps.employer.name }}
                </p>
                <i class="bi bi-arrow-right"></i>
            </Link>
            <div
                v-if="
                    page.props.auth.user.role.name === 'Senior' ||
                    page.props.auth.user.role.name === 'PWD'
                "
            >
                <div
                    class="mb-3 mt-3 flex flex-col items-center justify-center gap-2"
                    v-if="jobPostProps.job_status === 'Open'"
                >
                    <button
                        v-if="page.props.auth?.wokerIsVerified"
                        @click="showModal = true"
                        :class="[
                            'job w-fit rounded-full border border-[#171816] p-2 px-8 text-sm font-bold text-[#171816]',
                            {
                                'pointer-events-none bg-[#171816] text-white':
                                    isApplied,
                            },
                        ]"
                    >
                        {{ isApplied ? "Applied!" : "Apply" }}
                        <!-- <i class="bi bi-patch-check-fill"></i> -->
                        <i class="bi bi-file-person"></i>
                    </button>
                    <p v-else class="font-bold text-orange-500">
                        Your account is not yet verified.
                    </p>
                    <p v-if ="interviewDetails?.interview_schedule" >
                        Interview Date & Time:
                        {{
                            dayjs
                                .utc(interviewDetails?.interview_schedule)
                                .tz(userTz)
                                .format("YYYY-MM-DD h:mmA")
                        }}
                    </p>
                </div>
                <div v-else class="mx-auto mb-3 mt-3 w-fit">
                    <p
                        class="rounded border border-[#171816] bg-[#171816] p-2 px-5 text-[12px] font-bold text-white"
                    >
                        CLOSED
                        <i class="bi bi-x-circle-fill"></i>
                    </p>
                </div>
            </div>
            <div v-else class="mb-3 mt-3 flex justify-center gap-2">
                <Link
                    @click="closeJob"
                    as="button"
                    method="put"
                    preserve-scroll
                    :href="route('employer.jobpost.close', jobPostProps.id)"
                    :class="[
                        'rounded border border-[#171816] p-2 px-5 text-[12px] font-bold text-[#171816]',
                        {
                            'pointer-events-none bg-[#171816] text-white':
                                isClosed,
                        },
                    ]"
                >
                    {{ isClosed ? "CLOSED" : "CLOSE THIS JOB" }}
                    <i class="bi bi-x-circle-fill"></i>
                </Link>
                <!-- <Link
                    v-if="!isClosed"
                    as="button"
                    method="get"
                    preserve-scroll
                    :href="route('employer.jobpost.edit', jobPostProps.id)"
                    class="w-44 rounded border border-blue-400 p-2 px-8 font-bold text-blue-400"
                >
                    Edit
                </Link> -->
            </div>
        </div>
        <div class="flex-1 bg-[#f3f7fa]">
            <div class="xs container mx-auto px-[0.5rem] xl:max-w-7xl">
                <div class="grid gap-5 pt-5 lg:grid-cols-[1fr,300px]">
                    <div class="self-start rounded-lg bg-white px-8 py-5">
                        <div class="mb-3">
                            <div class="flex justify-between">
                                <p class="text-[20px] font-bold">Description</p>
                                <button @click="isShowReportModal = true">
                                    <i
                                        class="bi bi-exclamation-diamond-fill text-red-600"
                                    ></i>
                                </button>
                            </div>
                            <p class="whitespace-pre-wrap break-words text-sm">
                                {{ jobPostProps.description }}
                            </p>
                        </div>

                        <div class="mb-3">
                            <p class="text-lg font-bold">Open to</p>
                            <div
                                v-for="(worker, index) in preferredWorkers"
                                :key="index"
                            >
                                <p
                                    :class="[
                                        'text-sm',
                                        {
                                            'ml-5 list-item list-inside':
                                                worker !== 'PWD' &&
                                                worker !== 'Seniors Citizens',
                                        },
                                    ]"
                                >
                                    {{ worker }}
                                </p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <p class="text-lg font-bold">
                                Preferred educational attainment
                            </p>
                            <p class="text-sm">
                                {{
                                    jobPostProps.preferred_educational_attainment
                                }}
                            </p>
                        </div>
                        <div class="mb-3">
                            <p class="text-lg font-bold">Required Skills</p>
                            <div>
                                <p
                                    class="mb-1 w-fit rounded bg-slate-300 px-2 text-sm font-bold"
                                    v-for="(
                                        skill, index
                                    ) in jobPostProps.skills"
                                    :key="index"
                                >
                                    {{ skill }}
                                </p>
                            </div>
                        </div>
                        <div v-if="jobPostProps.work_arrangement != 'Remote'">
                            <p class="text-lg font-bold">Location</p>
                            <Maps
                                v-if="jobPostProps.location"
                                :disableSearch="true"
                                :disableSetMaker="true"
                                :centerProps="jobPostProps.location"
                                :markedCoordinatesProps="jobPostProps.location"
                            ></Maps>
                        </div>
                    </div>
                    <div
                        class="row-start-1 self-start rounded-lg bg-white px-8 py-5 lg:row-start-auto"
                    >
                        <p class="mb-1 text-[20px] font-bold">Overview</p>
                        <div class="text-[16px]">
                            <div class="mb-2">
                                <p class="">Job type</p>
                                <p class="text-sm text-gray-500">
                                    {{ jobPostProps.job_type }}
                                </p>
                            </div>
                            <div class="mb-2">
                                <p class="">Working Mode</p>
                                <p class="text-sm text-gray-500">
                                    {{ jobPostProps.work_arrangement }}
                                </p>
                            </div>
                            <div class="mb-2">
                                <p class="">Hour per day</p>
                                <p class="text-sm text-gray-500">
                                    {{ jobPostProps.hour_per_day }}
                                </p>
                            </div>
                            <div class="mb-2">
                                <p class="">Hourly rate</p>
                                <p class="text-sm text-gray-500">
                                    {{ jobPostProps.hourly_rate }}
                                </p>
                            </div>
                            <div class="mb-2">
                                <p class="">Salary</p>
                                <p class="text-sm text-gray-500">
                                    {{ jobPostProps.salary }}
                                </p>
                            </div>
                            <div class="mb-2">
                                <p class="">Experience required</p>
                                <p class="text-sm text-gray-500">
                                    {{ jobPostProps.experience }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Teleport defer to="body">
            <ReusableModal
                v-if="
                    showModal &&
                    (page.props.auth.user.role.name === 'Senior' ||
                        page.props.auth.user.role.name === 'PWD')
                "
                @closeModal="closeModal"
            >
                <div class="w-[500px] rounded bg-white p-4">
                    <div class="flex justify-between">
                        <p class="text-lg font-bold">Are you sure?</p>
                        <button @click="closeModal">
                            <i class="bi bi-x font-bold"></i>
                        </button>
                    </div>
                    <p class="mb-3">This action cannot be undone!</p>
                    <div class="flex gap-2 border p-2">
                        <p v-if="workerProfileProps?.resume_path">
                            Your active resume:
                        </p>
                        <p v-else>No active resume</p>
                        <a
                            v-if="workerProfileProps?.resume_path"
                            class="text-blue-500"
                            :href="
                                route(
                                    'show.resume',
                                    workerProfileProps?.resume_path,
                                )
                            "
                            target="_blank"
                            >{{ workerProfileProps?.resume }}</a
                        >
                        <label v-else class="text-blue-500">
                            <p class="cursor-pointer">
                                Click here to add resume
                            </p>
                            <input
                                @change="addResume"
                                type="file"
                                class="hidden"
                            />
                        </label>
                    </div>
                    <InputFlashMessage
                        :message="inputErrorResume"
                        type="error"
                    ></InputFlashMessage>
                    <div class="mt-4 flex justify-end gap-2">
                        <button @click="closeModal" class="rounded border p-2">
                            Cancel
                        </button>
                        <button
                            @click="submitResume()"
                            class="rounded bg-green-500 p-2 text-white"
                        >
                            Submit
                        </button>
                    </div>
                </div>
            </ReusableModal>
        </Teleport>
        <SuccessfulMessage
            :messageProp="messageProp"
            :messageShow="messageShow"
            type="Success"
        ></SuccessfulMessage>
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
