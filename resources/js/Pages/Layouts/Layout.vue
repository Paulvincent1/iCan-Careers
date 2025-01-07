<script setup>
import Navbar from "../Components/Navbar.vue";
import PageMessage from "../Components/ProfileSetupMessage.vue";
</script>

<template>
    <Navbar />
    <main class="mt-[4.625rem]">
        <slot></slot>
    </main>
    <Teleport to="body">
        <div class="fixed top-[80px] flex w-full flex-col gap-2">
            <PageMessage message="" />
            <PageMessage />
        </div>
    </Teleport>
</template>
