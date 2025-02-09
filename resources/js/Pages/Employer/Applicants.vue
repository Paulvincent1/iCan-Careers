<script setup>
import { Link, router, useForm } from "@inertiajs/vue3";
import { onMounted, ref, TransitionGroup, watch } from "vue";
import SuccessfulMessage from "../Components/Popup/SuccessfulMessage.vue";
import { debounce } from "lodash";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";

let props = defineProps({
    jobProps: null,
    applicantsProps: null,
    statusCountProps: null,
    messageProp: null,
});

let showMessage = ref(false);

function showMessageProp() {
    showMessage.value = true;
    setTimeout(() => {
        showMessage.value = false;
    }, 1000);
}

console.log(props.statusCountProps);
console.log(props.applicantsProps);

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
        console.log("if");

        lastTagValueClicked.value = status ?? lastTagValueClicked.value;
    } else {
        if (lastTagValueClicked.value != status) {
            applicants.value = props.applicantsProps.filter((e, index) => {
                return e.pivot.status === (status ?? lastTagValueClicked.value);
            });
            lastTagValueClicked.value = status ?? lastTagValueClicked.value;

            console.log("if if");
            return;
        }

        applicants.value = props.applicantsProps;
        console.log("else");

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

function updateStatus(applicationId, e) {
    if (
        confirm("Are you sure you want to update the status?") &&
        e.target.value != ""
    ) {
        console.log(e.target.value);

        router.post(
            route("job.applicants.update.status", { pivotId: applicationId }),
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

                    console.log(applicants.value);

                    showSpecificStatus();
                    // applicants.value[indexOfApplicant].name = e.target.value;
                },
            },
        );
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
onMounted(() => console.log("mounted"));
</script>
<template>
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
                                Lorem ipsum dolor sit amet consectetur,
                                adipisicing elit. Corrupti quidem minus porro
                                hic ipsam, earum accusamus. Voluptatum, quaerat
                                minima esse laboriosam maxime dolorem nisi quia
                                id molestiae optio, quae tempore.
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
                                        'w-fit rounded-full p-2 text-start text-white',
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
    <SuccessfulMessage
        :messageShow="showMessage"
        :messageProp="messageProp"
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
