<script setup>
import { Teleport, Transition } from "vue";

let props = defineProps({
    messageShow: false,
    messageProp: null,
});
</script>
<template>
    <Teleport defer to="body">
        <Transition name="message">
            <div v-if="messageShow" class="">
                <div
                    class="fixed left-[50%] top-20 flex translate-x-[-50%] items-center gap-2 rounded bg-green-200 p-4 text-green-600"
                >
                    <i class="bi bi-check-circle-fill"></i>
                    <p class="text-center">{{ props.messageProp }}</p>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
<style scoped>
.message-enter-active,
.message-leave-active {
    transition: all 0.5s ease-in;
}

.message-enter-from,
.message-leave-to {
    opacity: 0;
}
</style>
