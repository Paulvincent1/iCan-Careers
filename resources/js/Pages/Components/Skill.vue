<script setup>
import { ref, useTemplateRef } from "vue";
import Rating from "./Rating.vue";

let props = defineProps({
    modelValue: Object,
    owner: {
        type: Boolean,
        default: true,
    },
    starValue: {
        type: null,
        required: false,
    },
});
let emit = defineEmits([
    "addstar",
    "removeskill",
    "updateSkillName",
    "update:modelValue",
]);

let input = useTemplateRef("input");

function updateSkillName() {
    emit("updateSkillName", input.value.value, props.modelValue.id);
}

function addStar(star) {
    console.log(star);
    emit("addstar", star, props.modelValue.id);
}

function removeSkill(id) {
    emit("removeskill", id);
}

let isEditSkill = ref(false);
let onHover = ref(false);
</script>
<template>
    <div class="rounded border p-3">
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
                <i v-if="owner" class="bi bi-pencil-fill text-blue-500"></i>
            </div>
            <div v-show="isEditSkill" class="flex gap-2">
                <input
                    type="text"
                    ref="input"
                    :value="modelValue.name"
                    class="w-28 border p-1 outline-blue-500"
                />
                <button
                    @click="
                        updateSkillName();
                        isEditSkill = false;
                    "
                    class="rounded bg-green-300 p-1 text-white"
                >
                    Save
                </button>
            </div>
            <div class="flex gap-3">
                <button
                    v-if="owner"
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
                    starValue="starValue"
                    :id="modelValue.id"
                    @addstar="addStar"
                />
            </div>
        </div>
        <div class="mt-3 flex items-end gap-2">
            <p class="text-[14px]">Experience:</p>
            <input
                v-if="owner"
                @input="
                    emit(
                        'update:modelValue',
                        $event.target.value,
                        modelValue.id,
                    )
                "
                type="text"
                class="border px-2 outline-blue-500"
                placeholder="Less than 6 months"
            />
            <p v-if="!owner" class="text-[14px]">
                {{ modelValue.experience ? modelValue.experience : "N/A" }}
            </p>
        </div>
    </div>
</template>
