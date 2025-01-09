<script setup>
import { onMounted, onUpdated, ref } from "vue";
import Breadcrumbs from "../Components/Breadcrumbs.vue";
import { usePage } from "@inertiajs/vue3";

let breadcrumbs = ref([
    {
        name: "Register an account",
        done: true,
        active: false,
    },
    {
        name: "Create profile",
        done: false,
        active: false,
    },
    {
        name: "Rate your skills",
        done: false,
        active: false,
    },
]);
let page = usePage();

onMounted(() => {
    updateBreadcrumbs();
});

onUpdated(() => {
    updateBreadcrumbs();
});

function updateBreadcrumbs() {
    if (page.component === "WorkerAccountSetup/CreateProfile") {
        breadcrumbs.value[1].active = true;

        breadcrumbs.value[2].active = false;
    }

    if (page.component === "WorkerAccountSetup/AddSkills") {
        breadcrumbs.value[1].active = false;
        breadcrumbs.value[1].done = true;

        breadcrumbs.value[2].active = true;
    }
}
</script>

<template>
    <div class="xs container mx-auto px-[0.5rem] xl:max-w-7xl">
        <div class="flow-root">
            <Breadcrumbs :steps="breadcrumbs" class="mt-4" />
            <slot></slot>
        </div>
    </div>
</template>
