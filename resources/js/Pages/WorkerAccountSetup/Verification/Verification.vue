<script setup>
import { Link } from "@inertiajs/vue3";

function goBack() {
    console.log(window.history);
    window.history.back();
}
</script>
<template>
    <div
        class="xs container mx-auto flex h-[calc(100vh-4.625rem)] flex-col items-center px-[0.5rem] xl:max-w-7xl"
    >
        <div
            class="mb-12 mt-9 h-fit w-[400px] rounded bg-white px-6 py-9 sm:border sm:shadow-sm"
        >
            <div class="mb-5 flex w-full justify-center">
                <div class="relative">
                    <img
                        class="w-48 object-cover"
                        src="/storage/assets/government_id_verification2.png"
                        alt="government_id"
                    />
                    <img
                        src="/storage/assets/check_image.png"
                        alt=""
                        class="absolute right-3 top-[50%] w-20"
                    />
                </div>
            </div>
            <header class="mb-4 text-center text-lg font-semibold">
                <p>Government ID Verification</p>
                <p>(Required)</p>
            </header>

            <div class="mb-7 px-8 text-center">
                <p>
                    Upload a photo of your valid Government ID and a selfie
                    holding it.
                </p>
            </div>

            <Link
                :href="route('account.verifiy.id')"
                as="button"
                class="w-full rounded bg-blue-500 px-5 py-2 text-white"
            >
                Verify my Government ID</Link
            >
        </div>
        <button @click="goBack" class="px-5 py-2 text-blue-500">
            <i class="bi bi-caret-left"></i> Back to profile
        </button>
    </div>
</template>
