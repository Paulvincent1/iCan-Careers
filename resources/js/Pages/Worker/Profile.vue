<script setup>
import { computed, getCurrentInstance, ref, watch, watchEffect } from "vue";
import Skill from "../Components/Skill.vue";
import dayjs from "dayjs";
import WorkDetailsForm from "../Components/WorkDetailsForm.vue";
import { Link, router } from "@inertiajs/vue3";
import AddSkillModal from "../Components/Modal/AddSkillModal.vue";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import InputFlashMessage from "../Components/InputFlashMessage.vue";

let props = defineProps({
    userProp: Object,
    workerSkillsProp: Object,
    workerProfileProp: Object,
    workerBasicInfoProp: null,
    messageProp: String,
    visitor: null,
});

let { appContext } = getCurrentInstance();
let formatCurrency = appContext.config.globalProperties.formatCurrency;

let messageShow = ref(false);

let isEditProfileActive = ref(false);
let isEditJobTitleActive = ref(false);
let isEditDescription = ref(false);
let isOpen = ref(false);

function isOpenUpdateValue(e) {
    isOpen.value = e;
}

let workerSkills = ref(props.workerSkillsProp);
let workerProfile = ref(props.workerProfileProp);

watchEffect(() => {
    workerSkills.value = props.workerSkillsProp;
    showSuccessMessage();
});

function showSuccessMessage() {
    if (props.messageProp) {
        messageShow.value = true;
        setTimeout(() => {
            messageShow.value = false;
        }, 2000);
    }
}

const memberSince = computed(() => {
    return dayjs(props.userProp.created_at).format("MMMM DD, YYYY");
});

const age = dayjs().format("YYYY") - workerProfile.value.birth_year;

let hourPay = ref(formatCurrency(workerProfile.value.hour_pay));
let monthPay = ref(formatCurrency(workerProfile.value.month_pay));

function updateJobTitle() {
    router.put(
        "/jobseekers/myprofile/updateprofile",
        {
            job_title: workerProfile.value.job_title,
        },
        {
            onSuccess: () => {
                showSuccessMessage();
            },
            preserveScroll: true,
        },
    );
}

function updateWorkDetails() {
    router.put(
        "/jobseekers/myprofile/updateprofile",
        {
            job_type: workerProfile.value.job_type,
            work_hour_per_day: workerProfile.value.work_hour_per_day,
            hour_pay: workerProfile.value.hour_pay,
            month_pay: workerProfile.value.month_pay,
        },
        {
            onSuccess: () => {
                showSuccessMessage();
                hourPay.value = formatCurrency(workerProfile.value.hour_pay);
                monthPay.value = formatCurrency(workerProfile.value.month_pay);
            },
            preserveScroll: true,
        },
    );
}

function updateDescription() {
    router.put(
        "/jobseekers/myprofile/updateprofile",
        {
            profile_description: workerProfile.value.profile_description,
        },
        {
            onSuccess: () => {
                showSuccessMessage();
            },
            preserveScroll: true,
        },
    );
}

let profilePreview = ref(props.userProp.profile_img);
function uploadProfileImage(e) {
    profilePreview = URL.createObjectURL(e.target.files[0]);

    console.log(e.target.files[0]);

    router.post(
        "/jobseekers/myprofile/updateprofile",
        {
            _method: "put",
            profile_img: e.target.files[0],
        },
        {
            onSuccess: () => {
                showSuccessMessage();
            },
            preserveScroll: true,
        },
    );
}

// for skills
let disable = ref(false);

function updateSkillName(skillName, skillId) {
    router.put(
        `/jobseekers/myprofile/updateskill/${skillId}`,
        {
            skill_name: skillName,
        },
        {
            onStart: (start) => {
                disable.value = true;
            },
            onSuccess: () => {
                showSuccessMessage();
                workerSkills.value.forEach((e) => {
                    if (e.id === skillId) {
                        e.skill_name = skillName;
                    }
                });
            },
            onFinish: (visit) => {
                disable.value = false;
            },
            preserveScroll: true,
        },
    );
}

function updateExperience(exp, skillId) {
    router.put(
        `/jobseekers/myprofile/updateskill/${skillId}`,
        {
            experience: exp,
        },
        {
            onStart: (start) => {
                disable.value = true;
            },
            onSuccess: () => {
                showSuccessMessage();
                workerSkills.value.forEach((e) => {
                    if (e.id === skillId) {
                        e.experience = exp;
                    }
                });
            },
            onFinish: (visit) => {
                disable.value = false;
            },
            preserveScroll: true,
        },
    );
}

function updateStar(star, skillId) {
    router.put(
        `/jobseekers/myprofile/updateskill/${skillId}`,
        {
            rating: star,
        },
        {
            onStart: (start) => {
                disable.value = true;
            },
            onSuccess: () => {
                showSuccessMessage();
                workerSkills.value.forEach((e) => {
                    if (e.id === skillId) {
                        e.rating = star;
                    }
                });
            },
            onFinish: (visit) => {
                disable.value = false;
            },
            preserveScroll: true,
        },
    );
}

function removeSkill(skillId) {
    router.delete(
        `/jobseekers/myprofile/deleteskill/${skillId}`,

        {
            onBefore: () =>
                confirm("Are you sure you want to delete this user?"),
            onStart: (start) => {
                disable.value = true;
            },
            onSuccess: () => {
                showSuccessMessage();
                workerSkills.value.forEach((e, i) => {
                    if (e.id === skillId) {
                        workerSkills.value.splice(i, 1);
                    }
                });
            },
            onFinish: (visit) => {
                disable.value = false;
            },
            preserveScroll: true,
        },
    );
}

// basic info

let websiteLink = ref(props.workerBasicInfoProp?.website_link ?? "N/A");
let address = ref(props.workerBasicInfoProp?.address ?? "N/A");

let showWebsiteLinkSaveButton = ref(false);
let showAddressSaveButton = ref(false);

function updateWebsiteLink() {
    console.log("jfsdfs");

    if (websiteLink.value != "" && websiteLink.value != "N/A") {
        router.put(
            route("update.basicInfo.put"),
            {
                website_link: websiteLink.value,
            },
            {
                onSuccess: () => {
                    showSuccessMessage();
                    showWebsiteLinkSaveButton.value = false;
                },
                preserveState: true,
                preserveScroll: true,
            },
        );
    }
}

function updateAdress() {
    if (address.value != "" && address.value != "N/A") {
        router.put(
            route("update.basicInfo.put"),
            {
                address: address.value,
            },
            {
                onSuccess: () => {
                    showSuccessMessage();
                    showAddressSaveButton.value = false;
                },
                preserveState: true,
                preserveScroll: true,
            },
        );
    }
}

let inputResumeError = ref("");
function updateResume(e) {
    router.post(
        "/jobseekers/myprofile/updateprofile",
        {
            _method: "put",
            resume: e.target.files[0],
        },
        {
            onError: (e) => {
                inputResumeError.value = e.resume;
            },
            onSuccess: () => {
                workerProfile.value = props.workerProfileProp;
                showSuccessMessage();
            },
            preserveScroll: true,
            preserveState: true,
        },
    );
}
</script>
<template>
    <Head title="Profile | iCan Careers" />
    <div class="min-h-[calc(100vh-4.625rem)] bg-[#f3f7fa]">
        <div class="bg- relative h-32 bg-[#FAFAFA]">
            <label
                for="profileimg"
                :class="[
                    'absolute left-[50%] top-[40px] flex h-36 w-36 translate-x-[-50%] cursor-pointer flex-col items-center',
                    {
                        'pointer-events-none': visitor,
                    },
                ]"
            >
                <div class="mb-3 h-full w-full">
                    <img
                        draggable="false"
                        :src="
                            profilePreview
                                ? profilePreview
                                : '/assets/profile_placeholder.jpg'
                        "
                        alt=""
                        class="h-full w-full rounded-full object-cover"
                    />
                </div>
                <input
                    @change="uploadProfileImage"
                    id="profileimg"
                    type="file"
                    class="hidden"
                />
            </label>
        </div>
        <div class="bg-white pb-2">
            <div
                class="xs container mx-auto flex flex-col items-center justify-center pt-16 xl:max-w-7xl"
            >
                <div class="mb-2 flex items-end gap-3">
                    <p class="text-lg">{{ userProp.name }}</p>
                    <Link
                        v-if="visitor"
                        :href="route('messages')"
                        :data="{ user: userProp.id }"
                        class="bi bi-chat-dots text-lg text-blue-500 hover:cursor-pointer"
                    ></Link>
                </div>
                <div class="mb-3">
                    <p
                        @click="isEditJobTitleActive = true"
                        v-if="!isEditJobTitleActive"
                        :class="[
                            'max-w-[600px] cursor-pointer break-words text-center text-[24px] font-bold hover:underline',
                            {
                                'pointer-events-none': visitor,
                            },
                        ]"
                    >
                        {{ workerProfile.job_title }}
                    </p>
                    <form
                        @submit.prevent="
                            isEditJobTitleActive = false;
                            updateJobTitle();
                        "
                        v-if="isEditJobTitleActive"
                    >
                        <input
                            type="text"
                            v-model="workerProfile.job_title"
                            class="mr-2 rounded border p-1 outline-none ring-green-300 transition-all hover:ring-1 focus:ring-1"
                            required
                        />
                        <button
                            @click=""
                            class="rounded bg-green-500 p-1 text-white"
                        >
                            Save
                        </button>
                    </form>
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
                    <div class="rounded-lg bg-white p-8">
                        <p class="mb-3 text-[20px] font-bold">Overview</p>
                        <div class="mb-4 flex items-center gap-4">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-200"
                            >
                                <i class="bi bi-bag font-bold"></i>
                            </div>
                            <p
                                v-if="!isEditProfileActive"
                                @click="isEditProfileActive = true"
                                :class="[
                                    'max-w-[400px] cursor-pointer hover:border-b-2',
                                    {
                                        'pointer-events-none': visitor,
                                    },
                                ]"
                            >
                                Looking for {{ workerProfile.job_type }} work
                                ({{
                                    workerProfile.work_hour_per_day
                                }}
                                hours/day) at {{ hourPay }}/hour ({{
                                    monthPay
                                }}/month)
                            </p>
                            <form
                                v-if="isEditProfileActive"
                                @submit.prevent="
                                    updateWorkDetails();
                                    isEditProfileActive = false;
                                "
                            >
                                <WorkDetailsForm
                                    v-model:jobType="workerProfile.job_type"
                                    v-model:workHourPerDay="
                                        workerProfile.work_hour_per_day
                                    "
                                    v-model:hourPay="workerProfile.hour_pay"
                                    v-model:monthPay="workerProfile.month_pay"
                                />
                                <button
                                    class="rounded bg-green-500 p-1 text-white"
                                >
                                    Save
                                </button>
                            </form>
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
                    <div
                        class="max-w-2xl rounded-lg bg-white p-8 text-gray-600 shadow"
                    >
                        <p class="mb-3 text-[20px] font-bold">
                            Profile Description
                        </p>
                        <p
                            @click="isEditDescription = true"
                            v-if="!isEditDescription"
                            :class="[
                                'cursor-pointer whitespace-pre-wrap break-words text-[14px] hover:underline',
                                {
                                    'pointer-events-none': visitor,
                                },
                            ]"
                        >
                            {{ workerProfile.profile_description }}
                        </p>
                        <form
                            v-if="isEditDescription"
                            @submit.prevent="
                                updateDescription();
                                isEditDescription = false;
                            "
                        >
                            <textarea
                                class="w-full border p-2 pb-28 outline-green-500"
                                v-model="workerProfile.profile_description"
                                required
                            ></textarea>
                            <button class="rounded bg-green-500 p-1 text-white">
                                Save
                            </button>
                        </form>
                    </div>
                    <div
                        class="mb-8 rounded-lg bg-white p-8 text-gray-600 shadow"
                    >
                        <div class="mb-4 flex justify-between">
                            <p class="text-[20px] font-bold">Top Skills</p>
                            <button
                                v-if="!visitor"
                                @click="isOpenUpdateValue(true)"
                                class="rounded-xl bg-green-400 px-2 text-white"
                            >
                                Add Skill
                            </button>
                        </div>
                        <Skill
                            class="mb-3"
                            v-for="(skill, index) in workerSkills"
                            :key="skill.id"
                            :disabled="disable"
                            :modelValue="{
                                id: skill.id,
                                name: skill.skill_name,
                                experience: skill.experience,
                                star: Number(skill.rating),
                            }"
                            :owner="!visitor"
                            @updateSkillName="updateSkillName"
                            @updateExperience="updateExperience"
                            @removeSkill="removeSkill"
                            @addstar="updateStar"
                        />
                    </div>
                </div>
                <div
                    class="self-start rounded-lg bg-white p-8 text-gray-600 shadow"
                >
                    <p class="mb-3 text-[20px] font-bold">Basic Information</p>
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
                        <input
                            @focus="showAddressSaveButton = true"
                            type="text"
                            v-model="address"
                            class="outline-none hover:underline"
                        />
                        <button
                            v-show="showAddressSaveButton"
                            @click="updateAdress()"
                            class="rounded bg-green-500 p-1 text-white"
                        >
                            Save
                        </button>
                    </div>
                    <div class="mb-2">
                        <label class="text-sm" s for=""
                            >Website / Account</label
                        >
                        <input
                            @focus="showWebsiteLinkSaveButton = true"
                            type="text"
                            v-model="websiteLink"
                            class="outline-none hover:underline"
                        />
                        <button
                            v-show="showWebsiteLinkSaveButton"
                            @click="updateWebsiteLink()"
                            class="rounded bg-green-500 p-1 text-white"
                        >
                            Save
                        </button>
                    </div>
                    <div class="mb-2">
                        <label class="text-sm" for="">Resume</label>
                        <label for="resume" class="cursor-pointer">
                            <p
                                class="hover:underline"
                                v-if="!workerProfile.resume"
                            >
                                N/A
                            </p>
                            <div v-else class="flex items-center gap-2">
                                <p class="hover:underline">
                                    {{ workerProfile.resume }}
                                </p>
                                <a
                                    v-if="!visitor"
                                    target="_blank"
                                    :href="'/' + workerProfile.resume_path"
                                    as="button"
                                    class="rounded bg-green-500 px-2 py-1 text-white"
                                >
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            </div>
                            <input
                                @change="updateResume"
                                type="file"
                                id="resume"
                                class="hidden"
                            />
                            <InputFlashMessage
                                class="mt-3 text-sm"
                                :message="inputResumeError"
                                type="error"
                            ></InputFlashMessage>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <Teleport defer to="body">
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
        <Teleport defer to="body">
            <Transition>
                <AddSkillModal
                    v-if="isOpen"
                    :isOpen="isOpen"
                    @updateIsOpen="isOpenUpdateValue"
                />
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
