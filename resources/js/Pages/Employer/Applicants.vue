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

let props = defineProps({
    jobProps: null,
    applicantsProps: null,
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

let date = ref(null);
let time = ref(null);

let errorMessage = ref(null);
function schedInterview(e) {
    if (!date.value || !time.value) {
        errorMessage.value = "Please fill all the field.";
        return;
    }

    router.put(
        route("job.applicants.addinterview", {
            pivotId: applicationPivotId.value,
        }),
        {
            date: date.value,
            time: time.value,
        },
        {
            onSuccess: () => {
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
</script>
<template>
    <Head title="Applicants| iCan Careers" />
    <div class="container mx-auto px-[0.5rem] pt-3 xl:max-w-7xl">
        <div class="mb-2">
            <h2 class="text-[32px] font-bold">{{ jobProps.job_title }}</h2>
        </div>
        <div class="mb-3 flex flex-col justify-between gap-3 lg:flex-row">
            <div>
                <swiper-container slides-per-view="auto" :space-between="10">
                    <swiper-slide class="w-fit">
                        <li
                            @click="showSpecificStatus('Pending', $event)"
                            :class="[
                                'cursor-pointer rounded border border-yellow-400 p-1',
                                {
                                    'bg-yellow-400 text-white':
                                        lastTagValueClicked === 'Pending',
                                    'text-yellow-400':
                                        lastTagValueClicked != 'Pending',
                                },
                            ]"
                        >
                            Pending ({{ pendingCount ?? 0 }})
                        </li></swiper-slide
                    >
                    <swiper-slide class="w-fit">
                        <li
                            @click="showSpecificStatus('Under Review', $event)"
                            :class="[
                                'cursor-pointer rounded border border-slate-400 p-1 text-slate-400',
                                {
                                    'bg-slate-400 text-white':
                                        lastTagValueClicked === 'Under Review',
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
                                'cursor-pointer rounded border border-slate-400 p-1 text-slate-400',
                                {
                                    'bg-slate-400 text-white':
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
                                'cursor-pointer rounded border border-green-400 p-1 text-green-400',
                                {
                                    'bg-green-400 text-white':
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
                                'cursor-pointer rounded border border-red-400 p-1 text-red-400',
                                {
                                    'bg-red-400 text-white':
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
                class="max-w-96 rounded-full border p-2 px-5"
                placeholder="Search Applicant Name"
            />
        </div>

        <div class="overflow-x-auto">
            <table class="relative min-w-[800px] table-fixed md:w-full">
                <thead class="">
                    <tr class="border-b">
                        <th class="p-3 text-start">Image</th>
                        <th class="p-3 text-start">Name</th>
                        <th class="p-3 text-start">Resume</th>
                        <th class="p-3 text-start">Profile</th>
                        <th class="p-3 text-start">Status</th>
                        <th class="p-3 text-start">
                            Update Application Status
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <TransitionGroup name="applicant">
                        <tr
                            class="border-b"
                            v-for="(applicant, index) in applicants"
                            :key="applicant.id"
                        >
                            <td class="p-3 text-start">
                                <div class="h-20 w-20">
                                    <img
                                        class="w-full rounded-full object-cover"
                                        src="/assets/profile_placeholder.jpg"
                                        alt=""
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
                                    >{{ applicant.worker_profile.resume }}</a
                                >
                            </td>
                            <td class="p-3 text-start font-normal">
                                <Link
                                    class="text-blue-500"
                                    :href="
                                        route(
                                            'worker.show.profile',
                                            applicant.id,
                                        )
                                    "
                                    >View Profile</Link
                                >
                            </td>
                            <td class="p-3 text-start font-normal">
                                <p
                                    :class="[
                                        'w-fit rounded-full p-2 text-center text-white xl:text-start',
                                        {
                                            'bg-yellow-400':
                                                applicant.pivot.status ===
                                                'Pending',
                                            'bg-slate-400':
                                                applicant.pivot.status ===
                                                    'Interview Scheduled' ||
                                                applicant.pivot.status ===
                                                    'Under Review',
                                            'bg-green-400':
                                                applicant.pivot.status ===
                                                'Accepted',
                                            'bg-red-400':
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
                                    v-if="
                                        applicant.pivot.status != 'Rejected' &&
                                        applicant.pivot.status != 'Accepted'
                                    "
                                    name=""
                                    id=""
                                    @change="
                                        updateStatus(applicant.pivot.id, $event)
                                    "
                                >
                                    <option value="">
                                        {{ applicant.pivot.status }}
                                    </option>
                                    <option
                                        v-if="
                                            applicant.pivot.status === 'Pending'
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
                                    <option value="Rejected">Rejected</option>
                                </select>

                                <p v-else>{{ applicant.pivot.status }}</p>
                            </td>
                        </tr>
                    </TransitionGroup>
                </tbody>
            </table>
        </div>
    </div>
    <ReusableModal v-if="showModal" @closeModal="closeModal">
        <div class="w-[400px] rounded bg-white px-2 py-4">
            <h2 class="mb-3 text-xl font-bold">Set an interview date</h2>
            <form @submit.prevent="schedInterview">
                <div class="flex justify-start gap-3">
                    <div class="flex flex-col">
                        <label for="" class="text-gray-500">Date</label>
                        <input
                            type="date"
                            :min="dayjs().format('YYYY-MM-DD')"
                            class="border p-2"
                            v-model="date"
                        />
                    </div>
                    <div class="flex flex-col">
                        <label for="" class="text-gray-500">Time</label>
                        <input type="time" class="border p-2" v-model="time" />
                    </div>
                </div>
                <InputFlashMessage
                    class="mb-5"
                    type="error"
                    :message="errorMessage"
                ></InputFlashMessage>
                <select name="" id="" class="border p-2">
                    <option value="">remote</option>
                    <option value="">onsite</option>
                </select>
                <div>
                    <Maps
                        :centerProps="jobProps.location"
                        :markedCoordinatesProps="jobProps.location"
                    ></Maps>
                </div>
                <div class="flex justify-end">
                    <button
                        class="inline-block rounded bg-slate-500 p-2 text-white"
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
