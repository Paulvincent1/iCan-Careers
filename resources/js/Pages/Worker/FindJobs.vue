<script setup>
import { Link, router } from "@inertiajs/vue3";
import { forEach } from "lodash";
import { FreeMode } from "swiper/modules";
import { onMounted, ref, useTemplateRef } from "vue";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import JobCard from "../Components/JobCard.vue";

let props = defineProps({
    jobs: null,
});
console.log(props.jobs);

let swiperContainer = useTemplateRef("swiper-container");
// let next = useTemplateRef("next");
// let prev = useTemplateRef("prev");

// onMounted(() => {
//     next.value.addEventListener("click", () => {
//         swiperContainer.value.swiper.slideNext();
//     });
// });
let workingSchedCheckbox;
let workingModesCheckbox;
let experienceCheckbox;
onMounted(() => {
    swiperContainer.value.swiper.update();
    workingSchedCheckbox = document.querySelectorAll(".workingsched");
    workingModesCheckbox = document.querySelectorAll(".workingmodes");
    experienceCheckbox = document.querySelectorAll(".experience");
});

let workingSched = ref([]);
function updateWorkingSched() {
    workingSchedCheckbox.forEach((e) => {
        if (!e.checked) {
            const index = workingSched.value.indexOf(e.value);
            if (index !== -1) {
                workingSched.value.splice(index, 1);
            }
        }
        // console.log(workingSched.value.indexOf(e.value) != -1);

        if (workingSched.value.indexOf(e.value) === -1) {
            if (e.checked) {
                workingSched.value.push(e.value);
            }
        }
    });
    console.log(workingSched.value);
    submit();
}

let workingModes = ref([]);
function updateWorkingModes() {
    workingModesCheckbox.forEach((e) => {
        if (!e.checked) {
            const index = workingModes.value.indexOf(e.value);
            if (index !== -1) {
                workingModes.value.splice(index, 1);
            }
        }
        // console.log(workingSched.value.indexOf(e.value) != -1);

        if (workingModes.value.indexOf(e.value) === -1) {
            if (e.checked) {
                workingModes.value.push(e.value);
            }
        }
    });
    console.log(workingModes.value);
    submit();
}

let experiences = ref([]);
function updateExperiences() {
    experienceCheckbox.forEach((e) => {
        if (!e.checked) {
            const index = experiences.value.indexOf(e.value);
            if (index !== -1) {
                experiences.value.splice(index, 1);
            }
        }
        // console.log(workingSched.value.indexOf(e.value) != -1);

        if (experiences.value.indexOf(e.value) === -1) {
            if (e.checked) {
                experiences.value.push(e.value);
            }
        }
    });
    console.log(experiences.value);
    submit();
}

const submit = () => {
    router.get(
        route("jobsearch"),
        {
            job_type: workingSched.value,
            work_arrangement: workingModes.value,
            experience: experiences.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};
</script>
<template>
    <div class="container mx-auto px-[0.5rem] xl:max-w-7xl">
        <div
            class="grid pt-4 md:grid-cols-[200px,1fr] xl:grid-cols-[300px,1fr]"
        >
            <div class="border p-3">
                <p>Filters</p>
                <div>
                    <p>Working Schedule</p>

                    <div>
                        <label for="full">Full time</label>
                        <input
                            @change="updateWorkingSched"
                            id="full"
                            type="checkbox"
                            value="full time"
                            class="workingsched"
                        />
                    </div>
                    <div>
                        <label for="part">Part time</label>
                        <input
                            @change="updateWorkingSched"
                            id="part"
                            type="checkbox"
                            class="workingsched"
                            value="part time"
                        />
                    </div>
                    <div>
                        <label for="contract">Contract</label>
                        <input
                            @change="updateWorkingSched"
                            id="contract"
                            type="checkbox"
                            class="workingsched"
                            value="contract"
                        />
                    </div>
                </div>
                <div>
                    <p>Working Modes</p>

                    <div>
                        <label for="remote">Remote</label>
                        <input
                            @change="updateWorkingModes"
                            id="remote"
                            type="checkbox"
                            class="workingmodes"
                            value="Remote"
                        />
                    </div>
                    <div>
                        <label for="hybrid">Hybrid</label>
                        <input
                            @change="updateWorkingModes"
                            id="hybrid"
                            type="checkbox"
                            class="workingmodes"
                            value="Hybrid"
                        />
                    </div>
                    <div>
                        <label for="office">Office</label>
                        <input
                            @change="updateWorkingModes"
                            id="office"
                            type="checkbox"
                            class="workingmodes"
                            value="Office"
                        />
                    </div>
                </div>
                <div>
                    <p>Experience</p>

                    <div>
                        <label for="fresher">Fresher</label>
                        <input
                            @change="updateExperiences"
                            id="fresher"
                            type="checkbox"
                            class="experience"
                            value="Fresher"
                        />
                    </div>
                    <div>
                        <label for="year0-2">0-2 years</label>
                        <input
                            @change="updateExperiences"
                            id="year0-2"
                            type="checkbox"
                            class="experience"
                            value="0-2 years"
                        />
                    </div>
                    <div>
                        <label for="year2-4">2-4 years</label>
                        <input
                            @change="updateExperiences"
                            id="year2-4"
                            type="checkbox"
                            class="experience"
                            value="2-4 years"
                        />
                    </div>
                    <div>
                        <label for="year5">5+ years</label>
                        <input
                            @change="updateExperiences"
                            id="year5"
                            type="checkbox"
                            class="experience"
                            value="5+ years"
                        />
                    </div>
                </div>
            </div>
            <div class="h-[1000px] overflow-hidden border p-3">
                <header class="mb-4 flex justify-end">
                    <input
                        type="text"
                        class="w-96 rounded-full border p-2 px-5"
                        placeholder="Search job title"
                    />
                </header>

                <div class="h-16">
                    <swiper-container
                        ref="swiper-container"
                        class="h-full"
                        :space-between="10"
                        slides-per-view="auto"
                        :free-mode="true"
                        :free-mode-sticky="false"
                        :navigation="true"
                    >
                        <swiper-slide
                            v-for="(job, index) in jobs"
                            :key="job.id"
                            class="flex w-auto items-center justify-center"
                        >
                            <p
                                href="/"
                                method="get"
                                class="rounded-full border border-blue-500 p-3 text-[12px] text-blue-500"
                            >
                                {{ job.job_title }}
                            </p>
                        </swiper-slide>
                        <swiper-slide
                            class="flex w-auto items-center justify-center"
                        >
                            <p
                                href="/"
                                method="get"
                                class="rounded-full border border-blue-500 p-3 text-[12px] text-blue-500"
                            >
                                Social Media Manager
                            </p>
                        </swiper-slide>
                        <swiper-slide
                            class="flex w-auto items-center justify-center"
                        >
                            <p
                                href="/"
                                method="get"
                                class="rounded-full border border-blue-500 p-3 text-[12px] text-blue-500"
                            >
                                Social Media Manager
                            </p>
                        </swiper-slide>
                        <swiper-slide
                            class="flex w-auto items-center justify-center"
                        >
                            <p
                                href="/"
                                method="get"
                                class="rounded-full border border-blue-500 p-3 text-[12px] text-blue-500"
                            >
                                Social Media Manager
                            </p>
                        </swiper-slide>
                        <swiper-slide
                            class="flex w-auto items-center justify-center"
                        >
                            <p
                                href="/"
                                method="get"
                                class="rounded-full border border-blue-500 p-3 text-[12px] text-blue-500"
                            >
                                Social Media Manager
                            </p>
                        </swiper-slide>
                        <swiper-slide
                            class="flex w-auto items-center justify-center"
                        >
                            <p
                                href="/"
                                method="get"
                                class="rounded-full border border-blue-500 p-3 text-[12px] text-blue-500"
                            >
                                Social Media Manager
                            </p>
                        </swiper-slide>
                        <swiper-slide
                            class="flex w-auto items-center justify-center"
                        >
                            <p
                                href="/"
                                method="get"
                                class="rounded-full border border-blue-500 p-3 text-[12px] text-blue-500"
                            >
                                Social Media Manager
                            </p>
                        </swiper-slide>
                        <swiper-slide
                            class="flex w-auto items-center justify-center"
                        >
                            <p
                                href="/"
                                method="get"
                                class="rounded-full border border-blue-500 p-3 text-[12px] text-blue-500"
                            >
                                Social Media Manager
                            </p>
                        </swiper-slide>
                    </swiper-container>
                </div>

                <div class="grid gap-2 lg:grid-cols-2">
                    <JobCard
                        v-for="job in jobs"
                        :key="job.id"
                        :job="job"
                    ></JobCard>
                    <!-- <JobCard></JobCard> -->

                    <div class="bg-yellow-300">dasdas</div>
                    <div class="bg-red-300">dasdas</div>
                    <div class="bg-yellow-300">dasdas</div>
                    <div class="bg-red-300">dasdas</div>
                    <div class="bg-yellow-300">dasdas</div>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped></style>
