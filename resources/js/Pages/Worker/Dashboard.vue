<script setup>
import { Link } from '@inertiajs/vue3';

</script>
<template>
    <div class="container mx-auto px-[0.5rem] xl:max-w-7xl">
        <div
            class="grid gap-0 pt-8 lg:grid-cols-[300px,1fr] lg:gap-24 xl:gap-32"
        >
            <div>
                <div
                    class="mb-4 flex flex-col items-center justify-center rounded border p-4"
                >
                    <div class="mb-3 w-[84px]">
                        <img
                            src="/storage/assets/profile_placeholder.jpg"
                            alt=""
                            class="w-full rounded-[500px]"
                        />
                    </div>
                    <p class="font-bol mb-3">Paul S.</p>
                    <button
                        class="mb-3 w-full rounded-lg border px-4 py-2 font-bold"
                    >
                        View Profile
                    </button>
                    <p class="text-red-500">Please Verify your account!</p>
                </div>

                <div class="border p-4">Inbox</div>
            </div>
            <div
                class="grid grid-cols-1 gap-2 rounded xl:grid-cols-[600px,1fr]"
            >
                <div class="h-[318px] rounded border p-3">
                    <div class="flex justify-between">
                        <p class="p-1 font-bold">Invoice status</p>
                        <Link class="p-1">Create invoice</Link>
                    </div>
                </div>
                <div class="rounded border p-3">
                    <p class="font-bold">Notification</p>
                </div>
                <div class="col-span-2 h-[300px] rounded border p-3">
                    <p></p>
                </div>
            </div>
        </div>
    </div>
</template>
