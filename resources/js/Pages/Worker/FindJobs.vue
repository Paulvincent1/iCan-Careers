<script setup>
import { Link, router, usePage } from "@inertiajs/vue3";
import { forEach } from "lodash";
import { FreeMode } from "swiper/modules";
import {
    onMounted,
    onUpdated,
    ref,
    useTemplateRef,
    watch,
    watchEffect,
} from "vue";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import JobCard from "../Components/JobCard.vue";
import { debounce } from "lodash";

let props = defineProps({
    jobsProps: null,
    messageProp: null,
});

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
let jobTitlesRawArray = ref([]);
let jobTitles = ref([]);
const params = ref({});
let tagActive = ref(false);

let intialJobArray = ref([]);
let jobs = ref(props.jobsProps);

onMounted(() => {
    tagActive.value = false;
    swiperContainer.value.swiper.update();
    workingSchedCheckbox = document.querySelectorAll(".workingsched");
    workingModesCheckbox = document.querySelectorAll(".workingmodes");
    experienceCheckbox = document.querySelectorAll(".experience");

    console.log(jobs.value);

    jobs.value.forEach((e) => {
        jobTitlesRawArray.value.push(e.job_title);
    });

    jobTitles.value = [...new Set(jobTitlesRawArray.value)];
    params.value = route().params;

    if (params.value.job_title) {
        search.value = params.value.job_title;
    }

    intialJobArray.value = props.jobsProps;
});

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

watch(
    () => props.jobsProps,
    () => {
        jobTitlesRawArray.value = [];
        props.jobsProps.forEach((e) => {
            jobTitlesRawArray.value.push(e.job_title);
        });

        jobTitles.value = [...new Set(jobTitlesRawArray.value)];
    },
);

let workingSched = ref([]);
function updateWorkingSched() {
    workingSchedCheckbox.forEach((e) => {
        if (!e.checked) {
            const index = workingSched.value.indexOf(e.value);
            if (index !== -1) {
                workingSched.value.splice(index, 1);
            }
        }

        if (workingSched.value.indexOf(e.value) === -1) {
            if (e.checked) {
                workingSched.value.push(e.value);
            }
        }
    });

    submit();
    resetFilter();
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
    resetFilter();
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
    resetFilter();
}

let lastTagValueClicked = ref(null);
function filterTag(tag, event) {
    const tagbutton = event.target;
    let tagbuttons = document.querySelectorAll(".tag-buttons");

    tagbuttons.forEach((e) => {
        console.log(e);
        e.classList.remove("bg-blue-300", "text-white");
    });

    if (lastTagValueClicked.value != tag) {
        if (tagActive.value) {
            jobs.value = props.jobsProps;
            jobs.value = jobs.value.filter((value, index) => {
                return value.job_title === tag;
            });
            tagbutton.classList.add("bg-blue-300", "text-white");
            lastTagValueClicked.value = tag;
            console.log("if-if");
            return;
        }

        jobs.value = jobs.value.filter((value, index) => {
            return value.job_title === tag;
        });
        tagbutton.classList.add("bg-blue-300", "text-white");
        tagActive.value = true;
        lastTagValueClicked.value = tag;
        console.log("if");
    } else {
        jobs.value = props.jobsProps;
        tagActive.value = false;
        lastTagValueClicked.value = null;
        console.log("else");
    }
}
function resetFilter() {
    let tagbuttons = document.querySelectorAll(".tag-buttons");

    tagbuttons.forEach((e) => {
        console.log(e);
        e.classList.remove("bg-blue-300", "text-white");
    });
    lastTagValueClicked.value = null;
    tagActive.value = false;
}

const submit = () => {
    router.get(
        route("jobsearch"),
        {
            job_type: workingSched.value,
            work_arrangement: workingModes.value,
            experience: experiences.value,
            job_title: search.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                jobs.value = props.jobsProps;
            },
        },
    );
};
let search = ref("");

watch(search, debounce(submit, 500));
</script>
<template>
    <div class="container mx-auto px-[0.5rem] xl:max-w-7xl">
        <div
            class="grid items-start gap-8 pt-4 md:grid-cols-[200px,1fr] xl:grid-cols-[300px,1fr]"
        >
            <div class="top-20 p-3 md:sticky">
                <p class="mb-3 text-xl">Filters</p>
                <div
                    class="flex flex-col sm:flex-row sm:justify-around md:flex-col"
                >
                    <div class="mb-3">
                        <p>Working Schedule</p>

                        <div>
                            <input
                                @change="updateWorkingSched"
                                id="full"
                                type="checkbox"
                                value="full time"
                                class="workingsched mr-2"
                                :checked="
                                    params.job_type
                                        ? params.job_type.includes('full time')
                                        : false
                                "
                            />
                            <label for="full">Full time</label>
                        </div>
                        <div>
                            <input
                                @change="updateWorkingSched"
                                id="part"
                                type="checkbox"
                                class="workingsched mr-2"
                                value="part time"
                                :checked="
                                    params.job_type
                                        ? params.job_type.includes('part time')
                                        : false
                                "
                            />
                            <label for="part">Part time</label>
                        </div>
                        <div>
                            <input
                                @change="updateWorkingSched"
                                id="contract"
                                type="checkbox"
                                class="workingsched mr-2"
                                value="contract"
                                :checked="
                                    params.job_type
                                        ? params.job_type.includes('contract')
                                        : false
                                "
                            />
                            <label for="contract">Contract</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <p>Working Modes</p>

                        <div>
                            <input
                                @change="updateWorkingModes"
                                id="remote"
                                type="checkbox"
                                class="workingmodes mr-2"
                                value="Remote"
                                :checked="
                                    params.work_arrangement
                                        ? params.work_arrangement.includes(
                                              'Remote',
                                          )
                                        : false
                                "
                            />
                            <label for="remote">Remote</label>
                        </div>
                        <div>
                            <input
                                @change="updateWorkingModes"
                                id="hybrid"
                                type="checkbox"
                                class="workingmodes mr-2"
                                value="Hybrid"
                                :checked="
                                    params.work_arrangement
                                        ? params.work_arrangement.includes(
                                              'Hybrid',
                                          )
                                        : false
                                "
                            />
                            <label for="hybrid">Hybrid</label>
                        </div>
                        <div>
                            <input
                                @change="updateWorkingModes"
                                id="office"
                                type="checkbox"
                                class="workingmodes mr-2"
                                value="Onsite"
                                :checked="
                                    params.work_arrangement
                                        ? params.work_arrangement.includes(
                                              'Onsite',
                                          )
                                        : false
                                "
                            />
                            <label for="office">On site</label>
                        </div>
                    </div>
                    <div>
                        <p>Experience</p>

                        <div>
                            <input
                                @change="updateExperiences"
                                id="fresher"
                                type="checkbox"
                                class="experience mr-2"
                                value="Fresher"
                                :checked="
                                    params.work_arrangement
                                        ? params.work_arrangement.includes(
                                              'Fresher',
                                          )
                                        : false
                                "
                            />
                            <label for="fresher">Fresher</label>
                        </div>
                        <div>
                            <input
                                @change="updateExperiences"
                                id="year0-2"
                                type="checkbox"
                                class="experience mr-2"
                                value="0-2 years"
                                :checked="
                                    params.experience
                                        ? params.experience.includes(
                                              '0-2 years',
                                          )
                                        : false
                                "
                            />
                            <label for="year0-2">0-2 years</label>
                        </div>
                        <div>
                            <input
                                @change="updateExperiences"
                                id="year2-4"
                                type="checkbox"
                                class="experience mr-2"
                                value="2-4 years"
                                :checked="
                                    params.experience
                                        ? params.experience.includes(
                                              '2-4 years',
                                          )
                                        : false
                                "
                            />
                            <label for="year2-4">2-4 years</label>
                        </div>
                        <div>
                            <input
                                @change="updateExperiences"
                                id="year5"
                                type="checkbox"
                                class="experience mr-2"
                                value="5+ years"
                                :checked="
                                    params.experience
                                        ? params.experience.includes('5+ years')
                                        : false
                                "
                            />
                            <label for="year5">5+ years</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="overflow-hidden p-3">
                <header class="mb-4 flex justify-end">
                    <input
                        v-model="search"
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
                        <TransitionGroup name="list">
                            <swiper-slide
                                v-for="(jobTitle, index) in jobTitles"
                                :key="index"
                                class="flex w-auto items-center justify-center"
                            >
                                <li
                                    @click="filterTag(jobTitle, $event)"
                                    href="/"
                                    :class="[
                                        'tag-buttons rounded-full border border-blue-500 px-5 py-2 text-[12px] text-blue-500 hover:cursor-pointer',
                                    ]"
                                >
                                    {{ jobTitle }}
                                </li>
                            </swiper-slide>
                        </TransitionGroup>
                    </swiper-container>
                </div>

                <TransitionGroup
                    tag="div"
                    name="jobcard"
                    class="grid gap-2 lg:grid-cols-2"
                >
                    <JobCard
                        v-for="job in jobs"
                        :key="job.id"
                        :job="job"
                        @saveJob="showMessage"
                    ></JobCard>
                    <!-- <JobCard></JobCard> -->
                </TransitionGroup>
            </div>
        </div>
        <Teleport defer to="body">
            <Transition name="message">
                <div v-if="messageShow" class="">
                    <div
                        class="fixed left-[50%] top-20 flex translate-x-[-50%] items-center gap-2 rounded bg-green-200 p-4 text-green-600"
                    >
                        <i class="bi bi-check-circle-fill"></i>
                        <p class="text-center">{{ props.messageProp }}</p>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>
<style scoped>
.jobcard-move, /* added class to all moving elements */
.jobcard-leave-active,
.jobcard-enter-active {
    transition: all 0.5s ease-in;
}

.jobcard-enter-from,
.jobcard-leave-to {
    opacity: 0;
}

.jobcard-leave-active {
    min-width: 465px;
    position: absolute;
}

.list-move,
.list-leave-active,
.list-enter-active {
    transition: all 0.5s ease-in;
}

.list-enter-from,
.list-leave-to {
    opacity: 0;
}

.message-enter-from,
.message-leave-to {
    opacity: 0;
}

.message-enter-active,
.message-leave-active {
    transition: all 0.5s ease-in;
}
</style>
