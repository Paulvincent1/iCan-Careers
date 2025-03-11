<script setup>
import { ref, useTemplateRef } from "vue";
import Rating from "./Rating.vue";

let props = defineProps({
    modelValue: Object,
    owner: {
        type: Boolean,
        default: true,
    },
    disabled: null,
});

let emit = defineEmits([
    "addstar",
    "removeSkill",
    "updateSkillName",
    "updateExperience",
]);

let experienceActive = ref(false);

let input = useTemplateRef("input");
let experienceInput = useTemplateRef("experienceInput");

function updateSkillName() {
    emit("updateSkillName", input.value.value, props.modelValue.id);
}

function updateExperience() {
    emit("updateExperience", experienceInput.value.value, props.modelValue.id);
}

function addStar(star) {
    emit("addstar", star, props.modelValue.id);
}

function removeSkill(id) {
    emit("removeSkill", id);
}

let isEditSkill = ref(false);
let onHover = ref(false);
</script>
<template>
    <div class="rounded-lg border p-3">
        <div class="flex justify-between">
            <div
                @click="isEditSkill = true"
                v-show="!isEditSkill"
                :class="[
                    'flex cursor-pointer gap-2',
                    { 'pointer-events-none': !owner },
                ]"
            >
                <p class="text-lg">
                    {{ modelValue.name }}
                </p>
                <i v-show="owner" class="bi bi-pencil-fill text-blue-500"></i>
            </div>
            <form
                @submit.prevent="
                    updateSkillName();
                    isEditSkill = false;
                "
                v-show="isEditSkill"
                class="flex gap-2"
            >
                <input
                    type="text"
                    ref="input"
                    :value="modelValue.name"
                    class="w-28 border p-1 outline-blue-500"
                    required
                />
                <button
                    :disabled="disabled"
                    class="rounded bg-[#fa8334] p-1 text-white"
                >
                    Save
                </button>
            </form>
            <div class="flex gap-3">
                <button
                    :disabled="disabled"
                    v-show="owner"
                    @click="removeSkill(modelValue.id)"
                    @mouseover="onHover = true"
                    @mouseout="onHover = false"
                >
                    <i
                        :class="[
                            'bi bi-trash text-red-500',
                            {
                                'bi-trash3-fill text-lg': onHover,
                            },
                        ]"
                    ></i>
                </button>
                <Rating
                    :owner="owner"
                    :starValue="modelValue.star"
                    :id="modelValue.id"
                    :disabled="disabled"
                    @addstar="addStar"
                />
            </div>
        </div>
        <div class="mt-3 flex items-end gap-2">
            <div>
                <p class="text-[14px]">Experience:</p>
                <input
                    ref="experienceInput"
                    v-show="owner"
                    @input="experienceActive = true"
                    type="text"
                    class="mr-2 rounded border p-1 outline-blue-500"
                    placeholder="Less than 6 months"
                    :value="modelValue.experience"
                />
                <button
                    :disabled="disabled"
                    v-show="experienceActive"
                    @click="
                        updateExperience();
                        experienceActive = false;
                    "
                    class="rounded bg-[#fa8334] p-1 text-white"
                >
                    Save Experience
                </button>
            </div>
            <p v-show="!owner" class="text-[14px]">
                {{ modelValue.experience ? modelValue.experience : "N/A" }}
            </p>
        </div>
    </div>
</template>
