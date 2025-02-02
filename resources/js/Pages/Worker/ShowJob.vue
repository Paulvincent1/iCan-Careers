<script setup>
import { uniqueId } from "lodash";
import Maps from "../Components/Maps.vue";

let props = defineProps({
    jobPostProps: null,
});

console.log(props.jobPostProps);
</script>
<template>
    <div class="xs container mx-auto px-[0.5rem] xl:max-w-7xl">
        <div class="flex flex-col gap-5 p-4 pt-7 sm:flex-row">
            <div class="w-20 sm:w-32 md:w-48">
                <img class="rounded" src="/assets/images.png" alt="" />
            </div>
            <div class="flex flex-col items-start justify-around">
                <div class="mb-2">
                    <p class="text-[26px] sm:text-[32px]">
                        {{ jobPostProps.job_title }}
                    </p>
                    <p>
                        {{
                            jobPostProps.employer.employer_profile
                                .business_information
                                ? jobPostProps.employer.employer_profile
                                      .business_information.business_name
                                : jobPostProps.employer.name
                        }}
                    </p>
                </div>
                <div class="flex gap-4">
                    <div>Full Time</div>
                    <div>Onsite</div>
                    <div>$8/hr</div>
                    <div>0-2 years</div>
                </div>

                <div class="mb-2 flex w-full gap-4">
                    <button
                        class="w-28 flex-1 rounded bg-green-500 p-2 text-white md:w-36 md:p-3"
                    >
                        Saved
                    </button>
                    <!-- <button
                        class="w-36 rounded border border-green-500 p-3 text-green-500"
                    >
                        Saved
                    </button> -->
                    <!-- <button class="w-36 rounded bg-green-500 p-3 text-white">
                        Applied
                    </button> -->
                    <button
                        class="w-28 flex-1 rounded border border-green-500 p-2 text-green-500 md:w-36 md:p-3"
                    >
                        Apply
                    </button>
                </div>
                <!-- <div class="flex gap-5">
                    <p>Full time</p>
                    <p>Remote</p>
                    <p>Fresher</p>
                    <p>$20hr</p>
                    <p>8+ hour per day</p>
                </div> -->
            </div>
        </div>

        <div class="p-4">
            <div class="mb-3">
                <p class="text-lg font-bold">Hour per day</p>
                <p class="whitespace-pre-wrap">
                    {{ jobPostProps.hour_per_day }}
                </p>
            </div>
            <div class="mb-3">
                <p class="text-lg font-bold">Description</p>
                <p class="whitespace-pre-wrap">
                    {{ jobPostProps.description }}
                </p>
            </div>

            <div class="mb-3">
                <p class="text-lg font-bold">Preferred worker</p>
                <p>{{ jobPostProps.preferred_worker_types }}</p>
            </div>
            <div class="mb-3">
                <p class="text-lg font-bold">
                    Preferred educational attainment
                </p>
                <p>{{ jobPostProps.preferred_educational_attainment }}</p>
            </div>
            <div class="mb-3">
                <p class="text-lg font-bold">Required Skills</p>
                <p>{{ jobPostProps.skills }}</p>
            </div>
            <div>
                <Maps
                    v-if="jobPostProps.location"
                    :markedCoordinatesProps="jobPostProps.location"
                ></Maps>
            </div>
        </div>
    </div>
</template>
