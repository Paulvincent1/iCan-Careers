<script setup>
import { useForm } from "@inertiajs/vue3";
import AddSkillInput from "../AddSkillInput.vue";
import Skill from "../Skill.vue";
import { onMounted, ref, useTemplateRef, watch } from "vue";
import InputFlashMessage from "../InputFlashMessage.vue";
import { uniqueId } from "lodash";

let emit = defineEmits(["updateIsOpen"]);

function updateIsOpenFalse() {
    emit("updateIsOpen", false);
}

// const randomId = function (length = 6) {
//     return Math.random()
//         .toString(36)
//         .substring(2, length + 2);
// };

// let skills = ref([]);

function addSkill(name) {
    if (name.length) {
        form.skills.push({
            id: uniqueId(),
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

let deleteBtnClicked = ref(false);
function removeSkill(id) {
    form.skills.forEach((element, index) => {
        if (element.id === id) {
            form.skills.splice(index, 1);
        }
    });
    deleteBtnClicked.value = true;
}

let form = useForm({
    skills: [],
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

    form.post(route("add.skills.post"), {
        preserveScroll: true,
        onSuccess: () => {
            updateIsOpenFalse();
        },
    });
};

const card = useTemplateRef("card");
const backdrop = useTemplateRef("backdrop");
const closeBtn = useTemplateRef("closeBtn");
const saveBtn = useTemplateRef("saveBtn");
const skill = useTemplateRef("skill");
onMounted(() => {
    backdrop.value.addEventListener("click", (e) => {
        if (saveBtn.value.contains(e.target) || deleteBtnClicked.value) {
            deleteBtnClicked.value = false;
            return;
        }
        if (
            !card.value.contains(e.target) ||
            closeBtn.value.contains(e.target)
        ) {
            console.log(!card.value.contains(e.target));
            updateIsOpenFalse();
        }
    });
});
</script>
<template>
    <div
        ref="backdrop"
        class="fixed inset-0 z-50 flex items-center justify-center backdrop-brightness-75"
    >
        <div ref="card" class="w-[400px] rounded bg-white p-4">
            <div class="flex justify-between">
                <p>Add Skill</p>
                <button ref="closeBtn">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <AddSkillInput @addSkill="addSkill" />
            <InputFlashMessage
                class="mt-2"
                :message="errorMessage"
                type="error"
            />

            <div class="mb-3 max-h-[300px] overflow-y-auto">
                <Skill
                    ref="skill"
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

            <div class="flex justify-end">
                <button
                    :disabled="form.processing"
                    ref="saveBtn"
                    @click="submit"
                    class="rounded bg-green-400 p-2"
                >
                    Save
                </button>
            </div>
        </div>
    </div>
</template>
<style scoped>
.v-enter-active,
.v-leave-active {
    transition: opacity 1s ease;
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
}
</style>
