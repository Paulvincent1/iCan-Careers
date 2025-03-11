<script setup>
import { uniqueId } from "lodash";
import Maps from "./Components/Maps.vue";
import { ref, watchEffect } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import ReusableModal from "./Components/Modal/ReusableModal.vue";
import SuccessfulMessage from "./Components/Popup/SuccessfulMessage.vue";
import InputFlashMessage from "./Components/InputFlashMessage.vue";

let props = defineProps({
    jobPostProps: null,
    workerProfileProps: null,
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
                        class="h-full w-full rounded-lg object-cover"
                        :src="
                            jobPostProps.employer.employer_profile
                                .business_information
                                ? jobPostProps.employer.employer_profile
                                      .business_information.business_logo
                                : '/assets/images.png'
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
            <Link class="flex items-center justify-center gap-2">
                <p class="text-center text-sm text-gray-500 underline">
                    {{
                        jobPostProps.employer.employer_profile
                            .business_information
                            ? jobPostProps.employer.employer_profile
                                  .business_information.business_name
                            : jobPostProps.employer.name
                    }}
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
                    class="mb-3 mt-3 flex justify-center gap-2"
                    v-if="jobPostProps.job_status === 'Open'"
                >
                    <button
                        @click="showModal = true"
                        :class="[
                            'job w-fit rounded-full border border-[#171816] p-2 px-8 font-bold text-[#171816]',
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
                </div>
                <div v-else class="mb-3 mt-3">
                    <p class="text-center text-lg font-bold text-red-500">
                        This job listing is closed
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
                    <div class="rounded-lg bg-white px-8 py-5">
                        <div class="mb-3">
                            <p class="text-[20px] font-bold">Description</p>
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
</template>
