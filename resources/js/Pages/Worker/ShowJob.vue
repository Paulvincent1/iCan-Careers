<script setup>
import { uniqueId } from "lodash";
import Maps from "../Components/Maps.vue";
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import ReusableModal from "../Components/Modal/ReusableModal.vue";

let props = defineProps({
    jobPostProps: null,
});

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

let isSaved = ref(props.jobPostProps.users_who_saved);

function saveJob() {
    if (isSaved.value === 0) {
        isSaved.value = 1;
        console.log("hui");
    } else {
        console.log("hello");

        isSaved.value = 0;
    }
}

console.log(isSaved.value);
</script>
<template>
    <div class="">
        <div class="relative h-32 bg-[#FAFAFA]">
            <div class="xs container mx-auto px-[0.5rem] xl:max-w-7xl">
                <div
                    class="absolute left-[50%] top-[50px] flex h-32 w-full translate-x-[-50%] flex-col items-center"
                >
                    <img
                        class="h-full rounded-lg"
                        src="/assets/images.png"
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
            <div class="mb-3 mt-3 flex justify-center gap-5">
                <Link
                    @click="saveJob"
                    as="button"
                    method="post"
                    preserve-scroll
                    :href="route('jobsearch.save.job', jobPostProps.id)"
                    class="rounded bg-green-400 p-2 px-8 text-white"
                >
                    {{ isSaved ? "Saved" : "Save for later" }}
                </Link>
                <button
                    class="rounded border border-green-400 p-2 px-8 text-green-400"
                >
                    Apply
                </button>
            </div>
        </div>
        <div class="bg-[#f3f7fa]">
            <div class="xs container mx-auto px-[0.5rem] xl:max-w-7xl">
                <div
                    class="grid items-start gap-5 pt-5 lg:grid-cols-[1fr,300px]"
                >
                    <div class="rounded bg-white p-8 shadow">
                        <div class="mb-3">
                            <p class="mb-3 text-[20px] font-bold">
                                Description
                            </p>
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
                        <div>
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
                        class="row-start-1 rounded bg-white p-8 shadow lg:row-start-auto"
                    >
                        <p class="mb-3 text-[20px] font-bold">Overview</p>
                        <div class="text-[16px]">
                            <div class="mb-2">
                                <p>Job type</p>
                                <p>Full Time</p>
                            </div>
                            <div class="mb-2">
                                <p>Working Mode</p>
                                <p>Onsite</p>
                            </div>
                            <div class="mb-2">
                                <p>Hour per day</p>
                                <p>8</p>
                            </div>
                            <div class="mb-2">
                                <p>Hourly rate</p>
                                <p>₱8</p>
                            </div>
                            <div class="mb-2">
                                <p>Salary</p>
                                <p>₱8</p>
                            </div>
                            <div class="mb-2">
                                <p>Experience required</p>
                                <p>0-2 years</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Teleport defer to="body">
            <ReusableModal>
                <div class="w-[500px] bg-white p-2">
                    <p>Are you sure?</p>
                    <p>This action cannot be undone!</p>
                </div>
            </ReusableModal>
        </Teleport>
    </div>
</template>
