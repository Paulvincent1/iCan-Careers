<script setup>
import { onMounted, onUpdated, ref } from "vue";
import BreadcrumbsEdit from "../Components/BreadcrumbsEdit.vue";
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
            name: "Update your Business Profile",
            done: true,
            active: false,
        },
    ];
}




</script>

<template>
    <div class="min-h-[calc(100vh-4.625rem)] bg-[#eff2f6] text-[#171816]">
        <div class="xs container mx-auto px-[0.5rem] xl:max-w-7xl">
            <div class="flow-root">
                <BreadcrumbsEdit :steps="breadcrumbs" class="mt-4" />
                <slot></slot>
            </div>
        </div>
    </div>
</template>