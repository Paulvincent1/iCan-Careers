<script setup>
import { onMounted, onUpdated, ref } from "vue";
import Breadcrumbs from "../Components/Breadcrumbs.vue";
import { usePage } from "@inertiajs/vue3";

let page = usePage();
let breadcrumbs = ref([]);

console.log(page.component);

if (
    page.component === "WorkerAccountSetup/CreateProfile" ||
    "WorkerAccountSetup/AddSkills"
) {
    breadcrumbs.value = [
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
    ];
}
if (
    page.component === "EmployerAccountSetup/CreateProfile" ||
    page.component === "WorkerAccountSetup/CompanyInformation"
) {
    breadcrumbs.value = [
        {
            name: "Register an account",
            done: true,
            active: false,
        },
        {
            name: "Create Employer Profile",
            done: false,
            active: true,
        },
    ];
}

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

    // if (page.component === "EmployerAccountSetup/CreateProfile") {
    //     breadcrumbs.value[1].active = true;

    //     breadcrumbs.value[2].active = false;
    // }
    // if (page.component === "WorkerAccountSetup/CompanyInformation") {
    //     breadcrumbs.value[1].active = false;
    //     breadcrumbs.value[1].done = true;

    //     breadcrumbs.value[2].active = true;
    // }
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
