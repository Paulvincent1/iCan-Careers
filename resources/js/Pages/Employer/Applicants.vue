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
    // props.applicantsProps.forEach((job) => {
    //     job;
    // });

    jobId.value = route().params.jobid;
    search.value = route().params.q;
});

// let updateStatus = ref(null);

// watch(updateStatus, (value) => {
//     if (value === "under review" || value === "rejected") {
//         console.log("hi");
//     }
// });

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
function updateStatus(applicationId, e) {
    if (e.target.value === "Interview Scheduled") {
        openModal(e);

        applicationPivotId.value = applicationId;
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
                        // applicants.value[indexOfApplicant].name = e.target.value;
                    },
                    onError: (e) => {
                        showMessageProp();
                    },
                    preserveState: true,
                    preserveScroll: true,
                },
            );
        } else {
            e.target.value = "";
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
                // showSpecificStatus(lastTagValueClicked.value);
                showSpecificStatus();
                // applicants.value = props.applicantsProps;
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
            },
            onError: () => {
                applicationPivotId.value = null;
                showMessageProp();
                event.target.value = "";
                closeModal();
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
}
let event;
function openModal(e) {
    event = e;
    showModal.value = true;
}

dayjs.extend(utc);
dayjs.extend(timezone);

// get the timezone of the user
const userTz = Intl.DateTimeFormat().resolvedOptions().timeZone;
// console.log(userTz);

// console.log(dayjs().tz(userTz).format("YYYY-MM-DD"));
</script>
<template>
    <Head title="Applicants| iCan Careers" />
    <div class="h-[calc(100vh-4.625rem)] bg-[#eff2f6] pt-5 text-[#171816]">
        <div
            class="container mx-auto flex h-[90%] flex-col rounded bg-white p-5 xl:max-w-7xl"
        >
            <div class="mb-2">
                <h2 class="text-[26px]">{{ jobProps.job_title }}</h2>
                <div class="flex items-center gap-2">
                    <p class="text-[#171816]">
                        Applicants: {{ applicantsCount }}
                    </p>
                    <p
                        v-if="
                            page.props.auth.user.employer.subscription
                                .subscription_type === 'Free'
                        "
                        class="font-bold text-orange-500"
                    >
                        <i class="bi bi-exclamation-triangle-fill"></i> You
                        cannot hire applicants in free tier.
                    </p>
                </div>
            </div>
            <div class="mb-3 flex flex-col justify-between gap-3 lg:flex-row">
                <div>
                    <swiper-container slides-per-view="auto" :space-between="5">
                        <swiper-slide class="w-fit">
                            <li
                                @click="showSpecificStatus('Pending', $event)"
                                :class="[
                                    'cursor-pointer rounded border border-[#F1F1F1] p-1 text-sm',
                                    {
                                        'bg-[#171816] text-white':
                                            lastTagValueClicked === 'Pending',
                                        'text-gray-500':
                                            lastTagValueClicked != 'Pending',
                                    },
                                ]"
                            >
                                Pending ({{ pendingCount ?? 0 }})
                            </li></swiper-slide
                        >
                        <swiper-slide class="w-fit">
                            <li
                                @click="
                                    showSpecificStatus('Under Review', $event)
                                "
                                :class="[
                                    'cursor-pointer rounded border border-[#F1F1F1] p-1 text-sm text-gray-500',
                                    {
                                        'bg-[#171816] text-white':
                                            lastTagValueClicked ===
                                            'Under Review',
                                    },
                                ]"
                            >
                                Under Review ({{ underReviewCount ?? 0 }})
                            </li></swiper-slide
                        >
                        <swiper-slide class="w-fit">
                            <li
                                @click="
                                    showSpecificStatus(
                                        'Interview Scheduled',
                                        $event,
                                    )
                                "
                                :class="[
                                    'cursor-pointer rounded border border-[#F1F1F1] p-1 text-sm text-gray-500',
                                    {
                                        'bg-[#171816] text-white':
                                            lastTagValueClicked ===
                                            'Interview Scheduled',
                                    },
                                ]"
                            >
                                Interview Scheduled ({{ interviewCount ?? 0 }})
                            </li></swiper-slide
                        >
                        <swiper-slide class="w-fit">
                            <li
                                @click="showSpecificStatus('Accepted', $event)"
                                :class="[
                                    'cursor-pointer rounded border border-[#F1F1F1] p-1 text-sm text-gray-500',
                                    {
                                        'bg-[#171816] text-white':
                                            lastTagValueClicked === 'Accepted',
                                    },
                                ]"
                            >
                                Accepted ({{ acceptedCount ?? 0 }})
                            </li></swiper-slide
                        >
                        <swiper-slide class="w-fit">
                            <li
                                @click="showSpecificStatus('Rejected', $event)"
                                :class="[
                                    'cursor-pointer rounded border border-[#F1F1F1] p-1 text-sm text-gray-500',
                                    {
                                        'bg-[#171816] text-white':
                                            lastTagValueClicked === 'Rejected',
                                    },
                                ]"
                            >
                                Rejected ({{ rejectedCount ?? 0 }})
                            </li></swiper-slide
                        >
                    </swiper-container>
                </div>

                <input
                    v-model="search"
                    type="text"
                    class="max-w-96 rounded border px-2 text-sm"
                    placeholder="Search Applicant Name"
                />
            </div>

            <div class="flex-1 basis-1 overflow-x-auto">
                <table class="relative min-w-[800px] table-fixed md:w-full">
                    <thead class="">
                        <tr class="text-sm text-slate-500">
                            <th class="p-3 text-start font-normal">Image</th>
                            <th class="p-3 text-start font-normal">Name</th>
                            <th class="p-3 text-start font-normal">Resume</th>
                            <th class="p-3 text-start font-normal">Profile</th>
                            <th class="p-3 text-start font-normal">Status</th>
                            <th class="p-3 text-start font-normal">Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        <TransitionGroup name="applicant">
                            <tr
                                class="text-sm"
                                v-for="(applicant, index) in applicants"
                                :key="applicant.id"
                            >
                                <td class="p-3 text-start">
                                    <div class="h-12 w-12">
                                        <img
                                        class="h-full w-full rounded-full object-cover"
                                        :src="applicant.profile_img || '/assets/profile_placeholder.jpg'"
                                        @error="event => event.target.src = '/assets/profile_placeholder.jpg'"
                                        alt="Profile"
                                        />
                                    </div>
                                </td>
                                <td class="p-3 text-start font-normal">
                                    {{ applicant.name }}
                                </td>
                                <td class="p-3 text-start font-normal">
                                    <a
                                        class="text-blue-500 underline"
                                        :href="
                                            route('show.resume', {
                                                path: applicant.worker_profile
                                                    .resume_path,
                                                workerId: applicant.id,
                                            })
                                        "
                                        target="_blank"
                                        >{{
                                            applicant.worker_profile.resume
                                        }}</a
                                    >
                                </td>
                                <td class="p-3 text-start font-normal">
                                    <Link
                                        class="text-slate-500"
                                        :href="
                                            route(
                                                'worker.show.profile',
                                                applicant.id,
                                            )
                                        "
                                        ><i class="bi bi-arrow-right"></i
                                    ></Link>
                                </td>
                                <td class="p-3 text-start font-normal">
                                    <p
                                        :class="[
                                            'w-fit rounded-full px-2 text-start text-white xl:text-start',
                                            {
                                                'bg-yellow-600':
                                                    applicant.pivot.status ===
                                                    'Pending',
                                                'bg-slate-600':
                                                    applicant.pivot.status ===
                                                        'Interview Scheduled' ||
                                                    applicant.pivot.status ===
                                                        'Under Review',
                                                'bg-green-600':
                                                    applicant.pivot.status ===
                                                    'Accepted',
                                                'bg-red-600':
                                                    applicant.pivot.status ===
                                                    'Rejected',
                                            },
                                        ]"
                                    >
                                        {{ applicant.pivot.status }}
                                    </p>
                                </td>
                                <td class="p-3 text-start font-normal">
                                    <select
                                        :disabled="
                                            page.props.auth.user.employer
                                                .subscription
                                                .subscription_type === 'Free'
                                        "
                                        v-if="
                                            applicant.pivot.status !=
                                                'Rejected' &&
                                            applicant.pivot.status != 'Accepted'
                                        "
                                        name=""
                                        id=""
                                        @change="
                                            updateStatus(
                                                applicant.pivot.id,
                                                $event,
                                            )
                                        "
                                    >
                                        <option value="">
                                            {{ applicant.pivot.status }}
                                        </option>
                                        <option
                                            v-if="
                                                applicant.pivot.status ===
                                                'Pending'
                                            "
                                            value="Under Review"
                                        >
                                            Under Review
                                        </option>
                                        <option
                                            v-if="
                                                applicant.pivot.status ===
                                                'Under Review'
                                            "
                                            value="Interview Scheduled"
                                        >
                                            Interview Scheduled
                                        </option>
                                        <option
                                            v-if="
                                                applicant.pivot.status ===
                                                'Interview Scheduled'
                                            "
                                            value="Accepted"
                                        >
                                            Accepted
                                        </option>
                                        <option value="Rejected">
                                            Rejected
                                        </option>
                                    </select>

                                    <p v-else>{{ applicant.pivot.status }}</p>
                                </td>
                            </tr>
                        </TransitionGroup>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <ReusableModal v-if="showModal" @closeModal="closeModal">
        <div
            class="h-fit max-h-[400px] w-[350px] max-w-[400px] overflow-auto overflow-y-auto rounded bg-white p-4 text-[#171816] sm:w-[400px]"
        >
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-xl">Set an interview date</h2>
                <button @click="closeModal" class="cursor-pointer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <form @submit.prevent="schedInterview">
                <div class="mb-3 flex justify-start gap-3">
                    <div class="flex flex-1 flex-col">
                        <label for="" class="text-gray-500">Date</label>
                        <input
                            type="date"
                            :min="
                                dayjs()
                                    .tz(userTz)
                                    .add(12, 'hour')
                                    .format('YYYY-MM-DD')
                            "
                            class="rounded border p-2"
                            v-model="date"
                        />
                    </div>
                    <div class="flex flex-1 flex-col">
                        <label for="" class="text-gray-500">Time</label>
                        <input
                            type="time"
                            class="rounded border p-2"
                            v-model="time"
                        />
                    </div>
                </div>
                <InputFlashMessage
                    class="mb-5"
                    type="error"
                    :message="errorMessage"
                ></InputFlashMessage>
                <div class="flex flex-col">
                    <label for="" class="text-gray-500">Interview Mode</label>
                    <select
                        v-model="interviewMode"
                        name=""
                        id=""
                        class="mb-3 rounded border p-2"
                    >
                        <option value="remote">Remote</option>
                        <option value="onsite">On site</option>
                    </select>
                </div>
                <div v-if="interviewMode === 'onsite'" class="mb-3">
                    <Maps
                        @update:coordinates="setCoordinates"
                        :centerProps="jobProps.location"
                        :markedCoordinatesProps="jobProps.location"
                    ></Maps>
                </div>
                <div class="flex justify-end">
                    <button
                        class="inline-block rounded bg-[#171816] p-2 text-white"
                    >
                        Update status
                    </button>
                </div>
            </form>
            <div class="flex justify-end"></div>
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
    transition: all 0.5s ease-in;
}

.applicant-enter-from,
.applicant-leave-to {
    opacity: 0;
}

.applicant-leave-active {
    @apply invisible;
    position: absolute;
}
</style>
