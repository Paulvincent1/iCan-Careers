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
    <div class="mt-9 border p-7">
        <h2 class="my-3 text-2xl font-semibold">Tell us about your Skills</h2>
        <p>
            Select, rate, and describe your experience for your top skills. You
            can only select up to a maximum of 15 skills.
        </p>

        <AddSkillInput @addSkill="addSkill" />

        <!-- <div class="relative mb-4">
            <div class="relative">
                <input
                    v-model="query"
                    type="text"
                    placeholder="ex. Social Media Manager"
                    class="mt-3 w-full rounded border p-3 outline-blue-400"
                />
                <i
                    @click="addSkill(query)"
                    class="bi bi-plus-lg absolute right-3 top-[50%] translate-y-[-20%] cursor-pointer text-lg font-bold text-blue-500"
                ></i>
            </div>

            <div
                v-if="isOpen"
                :class="[
                    'absolute left-0 h-20 w-full overflow-auto rounded bg-slate-500 p-3 text-white',
                    {
                        'h-fit': !data.length || data.length <= 5,
                    },
                ]"
            >
                <p v-show="!data.length">No Skills found</p>
                <p
                    v-for="(res, index) in data"
                    :key="index"
                    class="cursor-pointer p-1"
                    @click="addSkill(res)"
                >
                    {{ res }}
                </p>
            </div>
        </div> -->

        <div class="mb-7">
            <p class="mb-2">Your Skills:</p>
            <div>
                <div>
                    <Skill
                        class="mb-2"
                        v-for="skill in form.skills"
                        :key="skill.id"
                        :modelValue="skill"
                        @addstar="addStar"
                        @removeSkill="removeSkill"
                        @updateSkillName="updateSkillName"
                        @updateExperience="updateExperience"
                    />
                    <!-- owner="true" -->
                    <!-- starValue="1" -->
                </div>
                <InputFlashMessage
                    class="mt-2"
                    :message="errorMessage"
                    type="error"
                />
            </div>
        </div>
        <hr class="mb-7" />
        <div>
            <button
                @click="submit"
                class="rounded bg-blue-500 px-4 py-2 text-white"
            >
                Save
            </button>
        </div>
    </div>
</template>
