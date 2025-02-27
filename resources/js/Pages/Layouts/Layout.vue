<script setup>
import { usePage } from "@inertiajs/vue3";
import Navbar from "../Components/Navbar.vue";
import ProfileSetupMessage from "../Components/ProfileSetupMessage.vue";
import { onUpdated, ref } from "vue";

let page = usePage();
let closeProfileSetupMessage = ref(false);

onUpdated(() => {
    console.log("logging");
});
</script>

<template>
    <Navbar />
    <main class="mt-[4.625rem]">
        <slot></slot>
    </main>
    <Teleport defer to="body">
        <ProfileSetupMessage
            @close="closeProfileSetupMessage = true"
            v-if="
                page.props.auth.worker_profile &&
                page.component != 'WorkerAccountSetup/AddSkills' &&
                page.component != 'WorkerAccountSetup/CreateProfile' &&
                !closeProfileSetupMessage
                    ? true
                    : false
            "
            class="fixed top-[80px] flex w-full flex-col gap-2"
            :info="$page.props.auth.worker_profile"
        />
    </Teleport>
</template>
