<script setup>
import { ref } from "vue";
import Breadcrumbs from "../Components/Breadcrumbs.vue";
import SetupProfileLayout from "../Layouts/SetupProfileLayout.vue";
import Layout from "../Layouts/Layout.vue";

defineOptions({
    layout: [Layout, SetupProfileLayout],
});
</script>
<template>
    <!-- <div class="xs container mx-auto flow-root px-[0.5rem] xl:max-w-[1280px]">
        <Breadcrumbs :steps="breadcrumbs" />
    </div>
    <SetupProfileLayout /> -->
</template>
