<script setup>
import { uniqueId } from "lodash";
import Maps from "./Components/Maps.vue";
import { ref, watchEffect } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import ReusableModal from "./Components/Modal/ReusableModal.vue";
import SuccessfulMessage from "./Components/Popup/SuccessfulMessage.vue";

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
function submitResume() {
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

// employer side
let isClosed = ref(null);
function closeJob() {}
</script>
<template>
    <div class="flex min-h-[calc(100vh-4.625rem)] flex-col">
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
                </div>
            </div>
        </div>
        <div class="xs container mx-auto mt-12 px-[0.5rem] xl:max-w-7xl">
            <p class="break-words text-center text-[24px] md:text-[32px]">
                {{ jobPostProps.job_title }}
            </p>
            <p class="text-center">
                {{
                    jobPostProps.employer.employer_profile.business_information
                        ? jobPostProps.employer.employer_profile
                              .business_information.business_name
                        : jobPostProps.employer.name
                }}
            </p>
            <div
                v-if="
                    page.props.auth.user.role.name === 'Senior' ||
                    page.props.auth.user.role.name === 'PWD'
                "
                class="mb-3 mt-3 flex justify-center gap-2"
            >
                <Link
                    @click="saveJob"
                    as="button"
                    method="post"
                    preserve-scroll
                    :href="route('jobsearch.save.job', jobPostProps.id)"
                    class="w-44 rounded bg-green-400 p-2 px-8 text-white"
                >
                    {{ isSaved ? "Saved" : "Save for later" }}
                </Link>
                <button
                    @click="showModal = true"
                    :class="[
                        'w-44 rounded border border-green-400 p-2 px-8 text-green-400',
                        {
                            'pointer-events-none bg-green-400 text-white':
                                isApplied,
                        },
                    ]"
                >
                    {{ isApplied ? "Applied!" : "Apply" }}
                </button>
            </div>
            <div v-else class="mb-3 mt-3 flex justify-center gap-2">
                <Link
                    as="button"
                    method="put"
                    preserve-scroll
                    :href="route('employer.jobpost.close', jobPostProps.id)"
                    class="w-44 rounded border border-red-400 p-2 px-8 font-bold text-red-400"
                >
                    {{ isSaved ? "Closed" : "Close this job" }}
                </Link>
                <Link
                    as="button"
                    method="post"
                    preserve-scroll
                    :href="route('jobsearch.save.job', jobPostProps.id)"
                    class="w-44 rounded border border-blue-400 p-2 px-8 font-bold text-blue-400"
                >
                    Edit
                </Link>
            </div>
        </div>
        <div class="flex-1 bg-[#f3f7fa]">
            <div class="xs container mx-auto px-[0.5rem] xl:max-w-7xl">
                <div class="grid gap-5 pt-5 lg:grid-cols-[1fr,300px]">
                    <div class="rounded bg-white p-8 shadow">
                        <div class="mb-3">
                            <p class="text-[20px] font-bold">Description</p>
                            <p class="whitespace-pre-wrap break-words">
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
                                    :class="{
                                        'ml-5 list-item list-inside':
                                            worker !== 'PWD' &&
                                            worker !== 'Seniors Citizens',
                                    }"
                                >
                                    {{ worker }}
                                </p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <p class="text-lg font-bold">
                                Preferred educational attainment
                            </p>
                            <p>
                                {{
                                    jobPostProps.preferred_educational_attainment
                                }}
                            </p>
                        </div>
                        <div class="mb-3">
                            <p class="text-lg font-bold">Required Skills</p>
                            <div>
                                <p
                                    class="w-fit rounded bg-slate-300 px-2 font-bold"
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
                        class="row-start-1 self-start rounded bg-white p-8 shadow lg:row-start-auto"
                    >
                        <p class="mb-3 text-[20px] font-bold">Overview</p>
                        <div class="text-[16px]">
                            <div class="mb-2">
                                <p>Job type</p>
                                <p>{{ jobPostProps.job_type }}</p>
                            </div>
                            <div class="mb-2">
                                <p>Working Mode</p>
                                <p>{{ jobPostProps.work_arrangement }}</p>
                            </div>
                            <div class="mb-2">
                                <p>Hour per day</p>
                                <p>{{ jobPostProps.hour_per_day }}</p>
                            </div>
                            <div class="mb-2">
                                <p>Hourly rate</p>
                                <p>{{ jobPostProps.hourly_rate }}</p>
                            </div>
                            <div class="mb-2">
                                <p>Salary</p>
                                <p>{{ jobPostProps.salary }}</p>
                            </div>
                            <div class="mb-2">
                                <p>Experience required</p>
                                <p>{{ jobPostProps.experience }}</p>
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
                    <div class="mb-4 flex gap-2 border p-2">
                        <p>Your active resume:</p>
                        <a
                            class="text-blue-500"
                            :href="
                                route(
                                    'show.resume',
                                    workerProfileProps.resume_path,
                                )
                            "
                            target="_blank"
                            >{{ workerProfileProps.resume }}</a
                        >
                    </div>
                    <div class="flex justify-end gap-2">
                        <button @click="closeModal" class="rounded border p-2">
                            Cancel
                        </button>
                        <button
                            @click="
                                submitResume();
                                closeModal();
                            "
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
        ></SuccessfulMessage>
    </div>
</template>
