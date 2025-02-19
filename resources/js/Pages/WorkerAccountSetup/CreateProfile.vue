<script setup>
import { ref } from "vue";
import Breadcrumbs from "../Components/Breadcrumbs.vue";
import SetupProfileLayout from "../Layouts/SetupProfileLayout.vue";
import Layout from "../Layouts/Layout.vue";
import { useForm } from "@inertiajs/vue3";
import WorkDetailsForm from "../Components/WorkDetailsForm.vue";
import InputFlashMessage from "../Components/InputFlashMessage.vue";
import dayjs from "dayjs";
import EducationalAttainment from "../Components/EducationalAttainment.vue";

defineOptions({
    layout: [Layout, SetupProfileLayout],
});

let form = useForm({
    job_title: null,
    profile_description: null,
    highest_educational_attainment: null,
    job_type: null,
    work_hour_per_day: null,
    hour_pay: null,
    month_pay: null,
    birth_year: null,
    gender: null,
    resume: null,
});

function addResume(e) {
    if (e.target.files[0]) {
        form.resume = e.target.files[0];
    }
}

let isDisabled = ref(false);
const submit = () => {
    form.post(route("create.profile.post"), {
        onStart: () => {
            isDisabled.value = true;
        },
        onFinish: () => {
            isDisabled.value = false;
        },
    });
};
</script>
<template>
    <div class="mt-9 border p-7">
        <h2 class="my-3 text-2xl font-semibold">Tell us about you</h2>
        <p class="text-sm">
            Lorem, ipsum dolor sit amet consectetur adipisicing elit. Reiciendis
            corporis itaque eum eius unde aperiam aliquid vel ratione
            doloremque! Ullam illo pariatur officiis tempore iste totam soluta
            dignissimos libero. Hic?
        </p>

        <form @submit.prevent="submit">
            <div class="flex flex-col">
                <label class="mb-2 mt-4 font-semibold">JOB TITLE</label>
                <input
                    v-model="form.job_title"
                    type="text"
                    class="border px-3 py-2 outline-blue-400"
                    placeholder="ex. Social Media Manager"
                />
                <InputFlashMessage
                    type="error"
                    :message="form.errors.job_title"
                />
            </div>
            <div class="flex flex-col">
                <label class="mb-2 mt-4 font-semibold"
                    >PROFILE DESCRIPTION</label
                >
                <textarea
                    v-model="form.profile_description"
                    type="text"
                    class="border px-3 pb-9 pt-2 outline-blue-400"
                    placeholder="Tell us summary about your skills and how you want to be known as worker."
                ></textarea>
                <InputFlashMessage
                    type="error"
                    :message="form.errors.profile_description"
                />
            </div>
            <div class="flex flex-col">
                <label class="mb-2 mt-4 font-semibold"
                    >HIGHEST EDUCATIONAL ATTAINMENT</label
                >
                <EducationalAttainment
                    v-model="form.highest_educational_attainment"
                    :message="form.errors.highest_educational_attainment"
                ></EducationalAttainment>
            </div>
            <div class="mt-8">
                <div>
                    <WorkDetailsForm
                        v-model:jobType="form.job_type"
                        v-model:workHourPerDay="form.work_hour_per_day"
                        v-model:hourPay="form.hour_pay"
                        v-model:monthPay="form.month_pay"
                    />
                </div>
            </div>
            <div class="mt-4">
                <label class="mb-2 mr-3 mt-4 font-semibold">BIRTH YEAR</label>
                <input
                    v-model="form.birth_year"
                    type="number"
                    min="1900"
                    :max="dayjs().format('YYYY') - 18"
                    class="w-20 border p-3 text-center outline-blue-500"
                    placeholder="YYYY"
                    required
                />
            </div>
            <div class="mt-4">
                <p class="mb-2 mt-4 font-semibold">GENDER</p>
                <div>
                    <label class="mr-2" for="male">MALE</label>
                    <input
                        v-model="form.gender"
                        type="radio"
                        class="text-center"
                        id="male"
                        value="Male"
                    />
                </div>
                <div>
                    <label class="mr-2" for="female">FEMALE</label>
                    <input
                        v-model="form.gender"
                        type="radio"
                        class="text-center"
                        id="female"
                        value="Female"
                    />
                </div>
                <InputFlashMessage type="error" :message="form.errors.gender" />
            </div>
            <div class="mt-4">
                <p class="mb-2 mt-4 font-semibold">
                    Add your resume (Optional)
                </p>

                <div>
                    <!-- <label class="mr-2" for="male">MALE</label> -->
                    <input
                        @change="addResume"
                        type="file"
                        class="appearance-none text-center"
                    />
                </div>
                <InputFlashMessage type="error" :message="form.errors.resume" />
            </div>

            <div class="flex justify-end">
                <button
                    class="rounded border bg-blue-500 px-4 py-2 text-white"
                    :disabled="isDisabled"
                >
                    Save
                </button>
            </div>
        </form>
    </div>
</template>
