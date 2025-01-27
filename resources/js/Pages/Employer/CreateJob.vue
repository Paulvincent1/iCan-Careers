<script setup>
import { useForm } from "@inertiajs/vue3";
import InputFlashMessage from "../Components/InputFlashMessage.vue";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import EducationalAttainment from "../Components/EducationalAttainment.vue";

let form = useForm({
    job_title: null,
    job_type: null,
    work_arrangement: null,
    hour_per_day: null,
    hourly_rate: null,
    salary: null,
    description: null,
    preferred_educational_attainment: null,
    preferred_worker_type: null,
});

const submit = () => {
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
                    <option value="Project based">Project based</option>
                </select>
                <InputFlashMessage
                    type="error"
                    :message="form.errors.job_type"
                />
            </div>

            <div class="flex flex-col">
                <p class="mb-2 mt-4 font-semibold">Work arrangement</p>
                <select name="" id="" v-model="form.work_arrangement">
                    <option value="Work from home">Work from home (WFH)</option>
                    <option value="On site">On site</option>
                    <option value="Hybrid">Hybrid</option>
                </select>
                <InputFlashMessage
                    type="error"
                    :message="form.errors.work_arrangement"
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
                    :error="form.preferred_educational_attainment"
                    :openToAll="true"
                ></EducationalAttainment>
            </div>
            <div class="flex flex-col">
                <label class="mb-2 mt-4 font-semibold"
                    >Preferred worker type</label
                >
                <input
                    v-model="form.preferred_worker_type"
                    type="text"
                    class="border px-3 py-2 outline-blue-400"
                    placeholder="John Doe"
                    required
                />
                <InputFlashMessage
                    type="error"
                    :message="form.errors.preferred_worker_type"
                />
            </div>

            <button class="mt-3 rounded bg-blue-500 p-2 text-white">
                Post
            </button>
        </form>
    </div>
</template>
<style></style>
