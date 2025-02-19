<script setup>
import { onMounted, useSlots, useTemplateRef } from "vue";

let emit = defineEmits(["closeModal"]);

const backdrop = useTemplateRef("backdrop");
const modalContent = useTemplateRef("modalContent");

// let slots = useSlots();

onMounted(() => {
    backdrop.value.addEventListener("click", (e) => {
        if (modalContent.value) {
            if (!modalContent.value.contains(e.target)) {
                emit("closeModal");
            }
        }
    });
});
</script>
<template>
    <div
        ref="backdrop"
        class="fixed inset-0 z-50 grid place-items-center backdrop-brightness-50"
    >
        <div ref="modalContent">
            <slot></slot>
        </div>
    </div>
</template>
