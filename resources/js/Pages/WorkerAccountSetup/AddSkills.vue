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

    <div class="flex justify-center">
        <div class="w-full max-w-5xl bg-white shadow-md rounded-lg p-8 mt-8 border border-gray-300 flex flex-col md:flex-row">
            <!-- Left Side: Skills Form -->
            <div class="w-full md:w-2/3 pr-6">
                <h2 class="text-3xl font-bold text-gray-900 text-center md:text-left">Tell us about your Skills</h2>
                <p class="text-lg text-gray-700 text-center md:text-left mb-6">
                    Select, rate, and describe your experience for your top skills. You can select up to <strong>15 skills</strong>.
                </p>

                <!-- Add Skill Input -->
                <div class="p-6 bg-gray-100 rounded-lg shadow-md mb-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Add a Skill</h3>
                    <AddSkillInput @addSkill="addSkill" />
                </div>

                <!-- Skills List -->
                <div class="p-6 bg-gray-100 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Your Skills</h3>

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

                    <p v-else class="text-gray-500 text-center mt-4">No skills added yet. Add your first skill above.</p>

                    <InputFlashMessage class="mt-2" :message="errorMessage" type="error" />
                </div>

                <hr class="my-7" />

                <!-- Save Button -->
                <div class="flex justify-center">
                    <button
                        @click="submit"
                        class="w-full bg-green-500 text-white font-bold px-6 py-4 text-xl rounded-lg hover:bg-green-600 transition shadow-md disabled:opacity-50"
                    >
                        Save Skills
                    </button>
                </div>
            </div>

            <!-- Right Side: Step-by-Step Instructions -->
            <div class="hidden md:block w-1/3 bg-gray-50 p-6 rounded-lg shadow-md border border-gray-200">
                <h3 class="text-xl font-bold text-gray-900 mb-4">How to Add Skills</h3>
                <ol class="list-decimal pl-5 text-gray-700 space-y-3">
                    <li>Add a skill using the input field above.</li>
                    <li>Click the <strong>+</strong> button to add it to your list.</li>
                    <li>Rate your skill by clicking on the <strong>stars</strong>.</li>
                    <li>Optionally, describe your experience in the provided field.</li>
                    <li>Once done, click the <strong>Save Skills</strong> button to save.</li>
                </ol>
                <p class="text-gray-600 mt-4 text-sm">Need help? Contact support for assistance.</p>
            </div>
        </div>
    </div>
</template>
