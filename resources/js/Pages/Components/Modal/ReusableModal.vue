<script setup>
import { onMounted, useSlots, useTemplateRef } from "vue";

let emit = defineEmits(["closeModal"]);

const backdrop = useTemplateRef("backdrop");
// const backdrop = useTemplateRef("backdrop");
let slots = useSlots();

onMounted(() => {
    backdrop.value.addEventListener("click", (e) => {
        if (!slots.default()[0].el.contains(e.target)) {
            console.log(e.target);
        }
    });

    console.log(slots.default()[0].el);
});
</script>
<template>
    <div
        ref="backdrop"
        class="fixed inset-0 z-50 grid place-items-center backdrop-brightness-75"
    >
        <div ref="sl">
            <slot></slot>
        </div>
    </div>
</template>
