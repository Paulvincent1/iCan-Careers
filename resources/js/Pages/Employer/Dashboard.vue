<script setup>
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
</script>
<template>
    <div class="xs container mx-auto px-[0.5rem] xl:max-w-7xl">
        <div class="grid gap-0 pt-8 lg:grid-cols-[300px,1fr] lg:gap-10">
            <div>
                <div
                    class="mb-4 flex flex-col items-center justify-center rounded border p-4"
                >
                    <div class="mb-3 w-[84px]">
                        <img
                            src="assets/profile_placeholder.jpg"
                            alt=""
                            class="w-full rounded-[500px]"
                        />
                    </div>
                    <p class="font-bol mb-3">dasdas</p>
                    <Link
                        :href="route('worker.profile')"
                        as="button"
                        class="mb-3 w-full max-w-[500px] rounded-lg border px-4 py-2 font-bold"
                    >
                        View Profile
                    </Link>
                    <div class="flex flex-col items-center">
                        <p class="mb-3 text-center text-red-500">
                            Your account is currently on free tier, Please
                            upgrade to pro tier to hire workers.
                        </p>
                        <!-- <Link
                            :href="route('account.verify')"
                            as="button"
                            class="w-full rounded-lg border bg-red-500 py-2 text-white"
                        >
                            See Pricing
                        </Link> -->
                    </div>
                </div>

                <div class="border p-4">Inbox</div>
            </div>
            <div
                class="grid grid-cols-1 gap-2 rounded lg:grid-cols-[400px,1fr] xl:grid-cols-[600px,1fr]"
            >
                <div
                    class="col-span-2 h-[318px] rounded border p-3 lg:col-span-1"
                >
                    <div class="flex justify-between">
                        <p class="p-1 font-bold">Job status</p>
                        <Link :href="route('create.job')" class="p-1"
                            >Post job</Link
                        >
                    </div>
                </div>
                <div class="col-span-2 rounded border p-3 lg:col-span-1">
                    <p class="font-bold">Notification</p>
                </div>
                <div class="col-span-2 h-[300px] rounded border p-3">
                    <p></p>
                </div>
            </div>
        </div>
    </div>
</template>
