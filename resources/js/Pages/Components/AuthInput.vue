<script setup>
import { onMounted, ref, useTemplateRef } from "vue";
import InputFlashMessage from "./InputFlashMessage.vue";

let model = defineModel({
    type: null,
    required: true,
});

let prop = defineProps({
    name: {
        type: null,
        required: true,
    },
    type: {
        type: null,
        required: true,
    },
    message: {
        type: null,
    },
});

let isFocus = ref(false);

let input = useTemplateRef("input");

function focusChecker(event = {}) {
    if (event.type === "focus") {
        isFocus.value = true;
    } else if (event.type === "blur") {
        if (input.value.value.length <= 0) {
            isFocus.value = false;
        }
    }

    if (input.value.value.length) {
        isFocus.value = true;
    }
}
onMounted(() => {
    focusChecker();
});

let id = prop.name + Math.random();
</script>
<template>
    <div>
        <div class="relative mb-2 flex flex-col">
            <label
                :for="id"
                :class="[
                    'absolute left-4 top-[50%] mb-2 translate-y-[-50%] transition-all hover:cursor-text',
                    {
                        'top-0 !translate-y-[-130%] text-[12px]': isFocus,
                    },
                ]"
                >{{ name }}</label
            >
            <input
                :id="id"
                ref="input"
                @focus="focusChecker"
                @blur="focusChecker"
                :type="type"
                :class="[
                    'rounded bg-gray-100 px-4 pt-7 text-lg outline-blue-400',
                    {
                        'border border-red-600': message,
                    },
                ]"
                v-model="model"
            />
        </div>
        <InputFlashMessage type="error" :message="message" />
    </div>
</template>
