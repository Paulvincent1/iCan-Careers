<script setup>
import { computed, onMounted, onUpdated, ref, watch } from "vue";
import Skill from "../Components/Skill.vue";
import dayjs from "dayjs";
import WorkDetailsForm from "../Components/WorkDetailsForm.vue";
import { router, useForm } from "@inertiajs/vue3";

let props = defineProps({
    userProp: Object,
    workerSkillsProp: Object,
    workerProfileProp: Object,
    messageProp: String,
});

let messageShow = ref(false);

let isEditProfileActive = ref(false);
let isEditJobTitleActive = ref(false);

let workerSkills = ref(props.workerSkillsProp);
console.log(workerSkills.value[0].rating);

let workerProfile = ref(props.workerProfileProp);
const memberSince = computed(() => {
    dayjs(props.userProp.created_at).format("MMMM DD, YYYY");
});

const age = dayjs().format("YYYY") - workerProfile.value.birth_year;

let hourPay = ref(formatCurrency(workerProfile.value.hour_pay));
let monthPay = ref(formatCurrency(workerProfile.value.month_pay));

function formatCurrency(value) {
    return new Intl.NumberFormat("en-PH", {
        style: "currency",
        currency: "PHP",
    }).format(value);
}

function updateExperience(exp, skillId) {
    console.log("update experience" + exp + skillId);
}

function updateJobTitle() {
    router.put(
        "/jobseekers/myprofile/updateprofile",
        {
            job_title: workerProfile.value.job_title,
        },
        {
            onSuccess: () => {
                if (props.messageProp) {
                    messageShow.value = true;
                    setTimeout(() => {
                        messageShow.value = false;
                    }, 2000);
                }
            },
            preserveScroll: true,
        },
    );
}

function updateWorkDetails() {
    router.put(
        "/jobseekers/myprofile/updateprofile",
        {
            job_type: workerProfile.value.jobType,
            work_hour_per_day: workerProfile.value.work_hour_per_day,
            hour_pay: workerProfile.value.hour_pay,
            month_pay: workerProfile.value.month_pay,
        },
        {
            onSuccess: () => {
                if (props.messageProp) {
                    messageShow.value = true;
                    setTimeout(() => {
                        messageShow.value = false;
                    }, 2000);
                }
            },
            preserveScroll: true,
        },
    );
}
</script>
<template>
    <div class="min-h-[calc(100vh-4.625rem)] bg-[#f3f7fa]">
        <div class="relative h-32 bg-[#FAFAFA]">
            <div
                class="absolute left-[50%] top-[32px] flex translate-x-[-50%] flex-col items-center"
            >
                <div class="mb-3 w-36">
                    <img
                        src="/assets/profile_placeholder.jpg"
                        alt=""
                        class="w-full rounded-full"
                    />
                </div>
            </div>
        </div>
        <div class="bg-white pb-2">
            <div
                class="xs container mx-auto flex flex-col items-center justify-center pt-16 xl:max-w-7xl"
            >
                <p class="mb-2 text-lg">{{ userProp.name }}</p>

                <div class="mb-3">
                    <p
                        @click="isEditJobTitleActive = true"
                        v-if="!isEditJobTitleActive"
                        class="max-w-[600px] cursor-pointer break-words text-center text-[24px] font-bold hover:underline"
                    >
                        {{ workerProfile.job_title }}
                    </p>
                    <div v-if="isEditJobTitleActive">
                        <input
                            type="text"
                            v-model="workerProfile.job_title"
                            class="mr-2 rounded border-none p-1 outline-none ring-green-300 transition-all hover:ring-1 focus:ring-1"
                        />
                        <button
                            @click="
                                isEditJobTitleActive = false;
                                updateJobTitle();
                            "
                            class="rounded bg-green-500 p-1 text-white"
                        >
                            Save
                        </button>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <p class="text-sm font-bold text-gray-600">Verified</p>
                    <i class="bi bi-patch-check-fill text-green-400"></i>
                </div>
            </div>
        </div>
        <div
            class="xs container mx-auto px-[0.5rem] md:px-[3rem] lg:px-[9rem] xl:max-w-7xl"
        >
            <div
                class="mt-8 grid gap-6 md:grid-cols-[1fr,250px] lg:grid-cols-[1fr,300px]"
            >
                <div class="flex flex-col gap-4 text-[16px] text-gray-600">
                    <div class="rounded-lg bg-white p-8 shadow">
                        <p class="mb-3 text-[20px]">Overview</p>
                        <div class="mb-4 flex items-center gap-4">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-200"
                            >
                                <i class="bi bi-bag font-bold"></i>
                            </div>
                            <p
                                v-if="!isEditProfileActive"
                                @click="isEditProfileActive = true"
                                class="max-w-[400px] cursor-pointer hover:border-b-2"
                            >
                                Looking for {{ workerProfile.job_type }} work
                                ({{
                                    workerProfile.work_hour_per_day
                                }}
                                hours/day) at {{ hourPay }}/hour ({{
                                    monthPay
                                }}/month)
                            </p>
                            <WorkDetailsForm
                                v-if="isEditProfileActive"
                                v-model:jobType="workerProfile.job_type"
                                v-model:workHourPerDay="
                                    workerProfile.work_hour_per_day
                                "
                                v-model:hourPay="workerProfile.hour_pay"
                                v-model:monthPay="workerProfile.month_pay"
                            />
                            <button
                                v-if="isEditProfileActive"
                                @click="
                                    isEditProfileActive = false;
                                    updateWorkDetails();
                                "
                                class="rounded bg-green-500 p-1 text-white"
                            >
                                Save
                            </button>
                        </div>
                        <div class="mb-4 flex items-center gap-4">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-200"
                            >
                                <i class="bi bi-mortarboard"></i>
                            </div>
                            <p>
                                {{
                                    workerProfile.highest_educational_attainment
                                }}
                            </p>
                        </div>
                        <div class="mb-4 flex items-center gap-4">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-200"
                            >
                                <i class="bi bi-person"></i>
                            </div>
                            <div>
                                <p>Member Since</p>
                                <p>{{ memberSince }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-lg bg-white p-8 text-gray-600 shadow">
                        <p class="mb-3 text-[20px]">Profile Description</p>
                        <p class="text-[14px]">
                            {{ workerProfile.profile_description }}
                        </p>
                    </div>
                    <div class="rounded-lg bg-white p-8 text-gray-600 shadow">
                        <p class="mb-3 text-[20px]">Top Skills</p>
                        <Skill
                            v-for="(skill, index) in workerSkills"
                            :key="skill.id"
                            :modelValue="{
                                id: skill.id,
                                name: skill.skill_name,
                                experience: skill.experience,
                                star: Number(skill.rating),
                            }"
                            @updateExperience="updateExperience"
                        />
                    </div>
                </div>
                <div
                    class="self-start rounded-lg bg-white p-8 text-gray-600 shadow"
                >
                    <p class="mb-3 text-[20px]">Basic Information</p>
                    <div class="mb-2">
                        <label class="text-sm" for="">Age</label>
                        <p>{{ age }}</p>
                        <!-- <input
                            type="number"
                            value="2025"
                            class="appearance-none outline-none hover:underline"
                        /> -->
                    </div>
                    <div class="mb-2">
                        <label class="text-sm" for="">Gender</label>
                        <p>{{ workerProfile.gender }}</p>
                        <!-- <input
                            type="number"
                            value="2025"
                            class="appearance-none outline-none hover:underline"
                        /> -->
                    </div>
                    <div class="mb-2">
                        <label class="text-sm" for="">Address</label>
                        <p>19</p>
                        <!-- <input
                            type="number"
                            value="2025"
                            class="appearance-none outline-none hover:underline"
                        /> -->
                    </div>
                    <div class="mb-2">
                        <label class="text-sm" s for=""
                            >Website / Account</label
                        >
                        <p>link</p>
                        <!-- <input
                            type="number"
                            value="2025"
                            class="appearance-none outline-none hover:underline"
                        /> -->
                    </div>
                </div>
            </div>
        </div>

        <Teleport to="body" defer>
            <Transition>
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
.v-enter-active,
.v-leave-active {
    transition: opacity 0.5s ease;
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
}
</style>
