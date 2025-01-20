<script setup>
import { usePage } from "@inertiajs/vue3";
import Navbar from "../Components/Navbar.vue";
import ProfileSetupMessage from "../Components/ProfileSetupMessage.vue";

let page = usePage();
</script>

<template>
    <Navbar />
    <main class="mt-[4.625rem]">
        <slot></slot>
    </main>
    <Teleport defer to="body">
        <div
            v-if="
                $page.props.auth.worker_profile &&
                $page.component != 'WorkerAccountSetup/AddSkills' &&
                $page.component != 'WorkerAccountSetup/CreateProfile'
                    ? true
                    : false
            "
            class="fixed top-[80px] flex w-full flex-col gap-2"
        >
            <ProfileSetupMessage :info="$page.props.auth.worker_profile" />
        </div>
    </Teleport>
</template>
