<script setup>
import { ref, useTemplateRef, watch } from "vue";
import Breadcrumbs from "../Components/Breadcrumbs.vue";
import SetupProfileLayout from "../Layouts/SetupProfileLayout.vue";
import Layout from "../Layouts/Layout.vue";
import { debounce } from "lodash";
import { useForm, usePage } from "@inertiajs/vue3";
import InputFlashMessage from "../Components/InputFlashMessage.vue";
import Skill from "../Components/Skill.vue";
import AddSkillInput from "../Components/AddSkillInput.vue";
import { nanoid } from "nanoid/non-secure";

defineOptions({
    layout: [Layout, SetupProfileLayout],
});

// const randomId = function (length = 6) {
//     return Math.random()
//         .toString(36)
//         .substring(2, length + 2);
// };

// let skills = ref([]);

function addSkill(name) {
    if (name.length) {
        form.skills.push({
            id: nanoid(),
            name,
            experience: "",
            star: "",
        });
    }
}

function updateSkillName(skillName, skillId) {
    if (skillName.length) {
        form.skills.forEach((object) => {
            if (object.id === skillId) {
                object.name = skillName;
            }
        });
    }
}

function updateExperience(exp, skillId) {
    form.skills.forEach((object) => {
        if (object.id === skillId) {
            object.experience = exp;
        }
    });
}

function addStar(star, skillId) {
    form.skills.forEach((object) => {
        if (object.id === skillId) {
            object.star = star;
        }
    });
    console.log(form.skills);
}

function removeSkill(id) {
    form.skills.forEach((element, index) => {
        if (element.id === id) {
            form.skills.splice(index, 1);
        }
    });
}
let page = usePage();

let form = useForm({
    skills: [],
    currentComponent: page.component,
});

let errorMessage = ref("");
const submit = () => {
    if (form.skills.length) {
        for (let i = 0; i < form.skills.length; i++) {
            if (form.skills[i].star === "") {
                errorMessage.value = "Please rate all your skills";
                return;
            }
        }
    } else {
        errorMessage.value = "Please add a skill";

        return;
    }

    form.post(route("add.skills.post"));
};
</script>
<template>
    <Head title="Skills Add | iCan Careers" />

    <div class="mb-3 flex justify-center">
        <div
            class="mt-5 flex flex-col items-start rounded-lg bg-white p-8 md:flex-row"
        >
            <!-- Left Side: Skills Form -->
            <div class="w-full pr-6 md:w-2/3">
                <h2 class="text-center text-[24px] md:text-left">
                    Tell us about your Skills
                </h2>
                <p class="mb-6 text-center text-lg text-gray-700 md:text-left">
                    Select, rate, and describe your experience for your top
                    skills. You can select up to <strong>15 skills</strong>.
                </p>

                <!-- Add Skill Input -->
                <div class="mb-6 p-6">
                    <h3 class="mb-1 text-xl font-bold text-gray-900">
                        Add a Skill
                    </h3>
                    <AddSkillInput @addSkill="addSkill" />
                </div>

                <!-- Skills List -->
                <div class="p-6">
                    <h3 class="mb-4 text-xl font-bold text-gray-900">
                        Your Skills
                    </h3>

                    <div v-if="form.skills.length" class="space-y-4">
                        <Skill
                            v-for="skill in form.skills"
                            :key="skill.id"
                            :modelValue="skill"
                            @addstar="addStar"
                            @removeSkill="removeSkill"
                            @updateSkillName="updateSkillName"
                            @updateExperience="updateExperience"
                        />
                    </div>

                    <p v-else class="mt-4 text-center text-gray-500">
                        No skills added yet. Add your first skill above.
                    </p>

                    <InputFlashMessage
                        class="mt-2"
                        :message="errorMessage"
                        type="error"
                    />
                </div>

                <hr class="my-7" />

                <!-- Save Button -->
                <div class="mt-4 flex justify-end gap-3">
                    <!-- Submit Button -->
                    <div
                        type="button"
                        @click="$inertia.visit(route('worker.dashboard'))"
                        class="mt-4 flex justify-end"
                    >
                        <button class="cursor-pointer rounded p-2 text-black">
                            <u>Skip for now</u>
                        </button>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4 flex justify-end">
                        <button
                            @click="submit"
                            class="cursor-pointer rounded bg-[#fa8334] p-2 text-white"
                        >
                            Save Skills
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Side: Step-by-Step Instructions -->
            <div
                class="hidden w-1/3 rounded-lg border border-gray-200 bg-gray-50 p-6 shadow-md md:block"
            >
                <h3 class="mb-4 text-xl font-bold text-gray-900">
                    How to Add Skills
                </h3>
                <ol class="list-decimal space-y-3 pl-5 text-gray-700">
                    <li>Add a skill using the input field above.</li>
                    <li>
                        Click the <strong>+</strong> button to add it to your
                        list.
                    </li>
                    <li>
                        Rate your skill by clicking on the
                        <strong>stars</strong>.
                    </li>
                    <li>
                        Optionally, describe your experience in the provided
                        field.
                    </li>
                    <li>
                        Once done, click the <strong>Save Skills</strong> button
                        to save.
                    </li>
                </ol>
                <p class="mt-4 text-sm text-gray-600">
                    Need help? Contact support for assistance.
                </p>
            </div>
        </div>
    </div>
</template>
