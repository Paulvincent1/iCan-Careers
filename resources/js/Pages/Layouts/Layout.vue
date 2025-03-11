<script setup>
import { usePage } from "@inertiajs/vue3";
import Navbar from "../Components/Navbar.vue";
import ProfileSetupMessage from "../Components/ProfileSetupMessage.vue";
import { onUpdated, ref } from "vue";

let page = usePage();
let closeProfileSetupMessageWorker = ref(false);
let closeProfileStupMessageEmployer = ref(false);
</script>

<template>
    <Navbar />
    <main class="mt-[4.625rem] ">
        <slot></slot>
    </main>
    <Teleport defer to="body">
        <ProfileSetupMessage
            @close="closeProfileSetupMessageWorker = true"
            v-if="
                page.props.auth.worker_profile &&
                page.component != 'WorkerAccountSetup/AddSkills' &&
                page.component != 'WorkerAccountSetup/CreateProfile' &&
                !closeProfileSetupMessageWorker
                    ? true
                    : false
            "
            class="fixed top-[80px] flex w-full flex-col gap-2"
            :info="$page.props.auth.worker_profile"
        />
        <ProfileSetupMessage
            @close="closeProfileStupMessageEmployer = true"
            v-if="
                page.props.auth.employer_profile &&
                page.component != 'EmployerAccountSetup/CreateProfile' &&
                !closeProfileStupMessageEmployer
                    ? true
                    : false
            "
            class="fixed top-[80px] flex w-full flex-col gap-2"
            :info="$page.props.auth.employer_profile"
        />
    </Teleport>
</template>
