<script setup>
import { useForm } from "@inertiajs/vue3";
import InputFlashMessage from "../Components/InputFlashMessage.vue";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import EducationalAttainment from "../Components/EducationalAttainment.vue";
import { onMounted, ref, useTemplateRef, watch } from "vue";
import { nanoid } from "nanoid/non-secure";
import Maps from "../Components/Maps.vue";

let props = defineProps({
    locationProps: null,
});

console.log(props.locationProps);

let form = useForm({
    job_title: null,
    job_type: null,
    work_arrangement: null,
    location: props.locationProps,
    experience: null,
    hour_per_day: null,
    hourly_rate: null,
    salary: null,
    description: null,
    preferred_educational_attainment: null,
    skills: null,
    preferred_worker_types: null,
});

onMounted(() => {
    form.location = props.locationProps
        ? [...props.locationProps]
        : [120.9842, 14.5995];
    console.log(form.location);
});

let isMapShow = ref(false);
function showMap() {
    if (form.work_arrangement === "Onsite") {
        isMapShow.value = true;
        return;
    }

    if (form.work_arrangement === "Hybrid") {
        isMapShow.value = true;
        return;
    }

    isMapShow.value = false;
}

function setCoordinates(coordinates) {
    form.location = [...coordinates];
}
watch(
    () => form.work_arrangement,
    (value) => {
        showMap();
    },
);

let isCheck = ref(false);
function check() {
    isCheck.value = !isCheck.value;
}

let isOpenToAllDisability = ref(false);
function openToAll() {
    isOpenToAllDisability.value = !isOpenToAllDisability.value;
}

let isOtherCheck = ref(false);
function otherCheck() {
    isOtherCheck.value = !isOtherCheck.value;
}

let candidateType = ref([]);
function addCandidateType(e) {
    if (!e.target.checked) {
        candidateType.value.forEach((candidate, index) => {
            if (candidate === e.target.value) {
                candidateType.value.splice(index, 1);
            }
        });

        if (e.target.value === "PWD") {
            isOpenToAllDisability.value = false;
            const index = candidateType.value.indexOf(
                "Open to All Disabalities",
            );

            if (index != -1) {
                candidateType.value.splice(index, 1);
            }
        }

        if (e.target.value === "Other") {
            otherInput.value.value = "";
            isOtherCheck.value = false;

            if (candidateType.value.indexOf(otherValue.value) != -1) {
                candidateType.value.splice(
                    candidateType.value.indexOf(otherValue.value),
                    1,
                );
                console.log(3);
            }
        }
    }

    if (e.target.checked) {
        candidateType.value.push(e.target.value);

        if (e.target.value === "Open to All Disabalities") {
            for (let i = candidateType.value.length - 1; i >= 0; i--) {
                const candidate = candidateType.value[i];
                if (
                    candidate != "PWD" &&
                    candidate != "Seniors Citizens" &&
                    candidate != "Open to All Disabalities"
                ) {
                    candidateType.value.splice(i, 1);
                }
            }
        }
    }

    console.log(candidateType.value);
}

let otherInput = useTemplateRef("otherInput");
let otherValue = ref("");

let skillInput = useTemplateRef("skillInput");
let skills = ref([]);
function addSkill() {
    if (skillInput.value.value != "") {
        skills.value.push({
            id: nanoid(),
            name: skillInput.value.value,
        });
        skillInput.value.value = "";
    }

    console.log(skillInput.value.value);
}

function removeSkill(skill) {
    skills.value.forEach((e, index) => {
        if (e.id === skill.id) {
            skills.value.splice(index, 1);
        }
    });
}

onMounted(() => {});

const submit = () => {
    if (candidateType.value.indexOf("Other") != -1) {
        candidateType.value[candidateType.value.indexOf("Other")] =
            otherInput.value.value;
        otherValue.value = otherInput.value.value;
    }
    if (otherInput.value) {
        if (otherInput.value.value != otherValue.value) {
            candidateType.value[candidateType.value.indexOf(otherValue.value)] =
                otherInput.value.value;

            otherValue.value = otherInput.value.value;
        }
    }

    form.skills = skills.value;
    form.preferred_worker_types = candidateType.value;
    console.log(candidateType.value);
    form.post(route("create.job.post"));
};
</script>

<template>
    <div class="container mx-auto mt-9 p-7 px-[0.5rem] lg:max-w-7xl">
        <h2 class="my-3 text-2xl font-semibold">Post a job</h2>
        <p class="text-sm">
            Lorem, ipsum dolor sit amet consectetur adipisicing elit. Reiciendis
            corporis itaque eum eius unde aperiam aliquid vel ratione
            doloremque! Ullam illo pariatur officiis tempore iste totam soluta
            dignissimos libero. Hic?
        </p>

        <form @submit.prevent="submit">
            <div class="flex flex-col">
                <label class="mb-2 mt-4 font-semibold">Job title</label>
                <input
                    v-model="form.job_title"
                    type="text"
                    class="border px-3 py-2 outline-blue-400"
                    placeholder="John Doe"
                    required
                />
                <InputFlashMessage
                    type="error"
                    :message="form.errors.job_title"
                />
            </div>
            <div class="flex flex-col">
                <label class="mb-2 mt-4 font-semibold">Job type</label>
                <select name="" id="" v-model="form.job_type">
                    <option value="Full time">Full time</option>
                    <option value="Part time">Part time</option>
                    <option value="Contract">Contract</option>
                </select>
                <InputFlashMessage
                    type="error"
                    :message="form.errors.job_type"
                />
            </div>

            <div class="flex flex-col">
                <p class="mb-2 mt-4 font-semibold">Work arrangement</p>
                <select name="" id="" v-model="form.work_arrangement">
                    <option value="Onsite">Onsite</option>
                    <option value="Hybrid">Hybrid</option>
                    <option value="Remote">Remote</option>
                </select>
                <InputFlashMessage
                    type="error"
                    :message="form.errors.work_arrangement"
                />

                <Maps
                    v-if="isMapShow"
                    :markedCoordinatesProps="form.location"
                    :centerProps="form.location"
                    @update:coordinates="setCoordinates"
                ></Maps>
            </div>
            <div class="flex flex-col">
                <p class="mb-2 mt-4 font-semibold">Preferred Experience</p>
                <select name="" id="" v-model="form.experience">
                    <option value="Fresher">Fresher</option>
                    <option value="0-2 years">0-2 years</option>
                    <option value="2-4 years">2-4 years</option>
                    <option value="5+ years">5+ years</option>
                </select>
                <InputFlashMessage
                    type="error"
                    :message="form.errors.experience"
                />
            </div>

            <div class="flex flex-col">
                <label class="mb-2 mt-4 font-semibold">Hour per day</label>
                <input
                    v-model="form.hour_per_day"
                    type="text"
                    class="border px-3 py-2 outline-blue-400"
                    placeholder="John Doe"
                    required
                />
                <InputFlashMessage
                    type="error"
                    :message="form.errors.hour_per_day"
                />
            </div>
            <div class="flex flex-col">
                <label class="mb-2 mt-4 font-semibold">Hourly rate</label>
                <input
                    v-model="form.hourly_rate"
                    type="text"
                    class="border px-3 py-2 outline-blue-400"
                    placeholder="John Doe"
                    required
                />
                <InputFlashMessage
                    type="error"
                    :message="form.errors.hourly_rate"
                />
            </div>
            <div class="flex flex-col">
                <label class="mb-2 mt-4 font-semibold">Salary</label>
                <input
                    v-model="form.salary"
                    type="text"
                    class="border px-3 py-2 outline-blue-400"
                    placeholder="John Doe"
                    required
                />
                <InputFlashMessage type="error" :message="form.errors.salary" />
            </div>
            <div class="flex flex-col">
                <label class="mb-2 mt-4 font-semibold">Description</label>
                <input
                    v-model="form.description"
                    type="text"
                    class="border px-3 py-2 outline-blue-400"
                    placeholder="John Doe"
                    required
                />
                <InputFlashMessage
                    type="error"
                    :message="form.errors.description"
                />
            </div>
            <div class="flex flex-col">
                <label class="mb-2 mt-4 font-semibold"
                    >Preffered Educational Attainment</label
                >

                <EducationalAttainment
                    v-model="form.preferred_educational_attainment"
                    :error="form.errors.preferred_educational_attainment"
                    :openToAll="true"
                ></EducationalAttainment>
            </div>
            <div class="flex flex-col">
                <div class="flex flex-col">
                    <label class="mb-2 mt-4 font-semibold"
                        >Required Skills</label
                    >
                    <div class="mb-3">
                        <input
                            ref="skillInput"
                            type="text"
                            class="mr-3 border px-3 py-2 outline-blue-400"
                            placeholder="John Doe"
                        />
                        <input
                            @click="addSkill"
                            class="rounded bg-green-500 p-2 text-white"
                            type="button"
                            value="Add"
                        />
                    </div>
                    <div
                        class="flex min-h-20 flex-wrap items-start justify-start gap-1 border p-2"
                    >
                        <div
                            v-for="(skill, index) in skills"
                            :key="skill.id"
                            class="flex w-fit rounded bg-gray-300 p-1"
                        >
                            <p class="">{{ skill.name }}</p>
                            <i
                                @click="removeSkill(skill)"
                                class="bi bi-x cursor-pointer"
                            ></i>
                        </div>
                    </div>
                </div>
                <InputFlashMessage type="error" :message="form.errors.skills" />
            </div>
            <div class="flex flex-col">
                <label class="mb-2 mt-4 font-semibold"
                    >Candidate type options:</label
                >

                <div>
                    <label for="">Seniors</label>
                    <input
                        @change="addCandidateType"
                        value="Seniors Citizens"
                        type="checkbox"
                    />
                </div>
                <div>
                    <label for="">PWD</label>
                    <input
                        @change="
                            check();
                            addCandidateType($event);
                        "
                        value="PWD"
                        type="checkbox"
                    />
                </div>
                <div v-if="isCheck">
                    <div v-if="!isOpenToAllDisability">
                        <div>
                            <label for="">Physical Disabalities</label>
                            <input
                                @change="addCandidateType"
                                type="checkbox"
                                value="Physical Disabalities"
                            />
                        </div>
                        <div>
                            <label for="">Visual Impairments</label>
                            <input
                                @change="addCandidateType"
                                type="checkbox"
                                value="Visual Impairments"
                            />
                        </div>
                        <div>
                            <label for="">Hearing Impairments</label>
                            <input
                                @change="addCandidateType"
                                type="checkbox"
                                value="Hearing Impairments"
                            />
                        </div>
                        <div>
                            <label for=""
                                >Cognitive/Developmental Disabilities</label
                            >
                            <input
                                @change="addCandidateType"
                                type="checkbox"
                                value="Cognitive/Developmental Disabilities"
                            />
                        </div>
                        <div>
                            <label for="">Mental Health Disabalities</label>
                            <input
                                @change="addCandidateType"
                                type="checkbox"
                                value="Mental Health Disabalities"
                            />
                        </div>

                        <div>
                            <label for="">Other</label>
                            <input
                                @change="
                                    otherCheck();
                                    addCandidateType($event);
                                "
                                type="checkbox"
                                value="Other"
                            />
                            <br />
                            <input
                                v-if="isOtherCheck"
                                ref="otherInput"
                                type="text"
                                placeholder="Please Specify"
                                class="border-b outline-none"
                                required
                            />
                        </div>
                    </div>
                    <div>
                        <label for="">Open to All Disabalities</label>
                        <input
                            @change="
                                openToAll();
                                addCandidateType($event);
                            "
                            type="checkbox"
                            value="Open to All Disabalities"
                        />
                    </div>
                </div>

                <InputFlashMessage
                    type="error"
                    :message="form.errors.preferred_worker_types"
                />
            </div>

            <button class="mt-3 rounded bg-blue-500 p-2 text-white">
                Post
            </button>
        </form>
    </div>
</template>
<style></style>
