<script setup>
import { ref } from "vue";

let isActive = ref(false);
</script>
<template>
    <div class="shadow">
        <header
            class="container mx-auto flex h-[4.625rem] items-center justify-between px-[0.5rem] xl:max-w-7xl"
        >
            <p>iCan Careers</p>
            <nav
                :class="[
                    'fixed inset-0 top-[4.626rem] z-[1000] flex h-[calc(100vh-4.625rem)] w-full flex-col items-center backdrop-blur-lg transition-all md:static md:inset-0 md:h-[100%] md:w-auto md:translate-x-0 md:flex-row',
                    {
                        'translate-x-[100%]': !isActive,
                    },
                ]"
            >
                <ul
                    class="flex h-[100%] flex-col items-center justify-start gap-16 py-3 md:static md:flex-row md:gap-4"
                >
                    <li class="mt-6 md:my-auto md:pr-5">Learning</li>
                    <li class="md:my-auto md:pr-5">Pricing</li>
                    <li class="md:my-auto">
                        <Link
                            class="rounded-3xl bg-[#024570] px-7 py-2 font-medium text-white"
                        >
                            POST A JOB
                        </Link>
                    </li>
                    <li class="flex items-center md:pr-3 lg:border-r-[1px]">
                        <Link
                            class="rounded-3xl bg-[#73b743] px-7 py-2 font-medium text-white"
                        >
                            FIND JOBS
                        </Link>
                    </li>
                    <div class="flex items-center gap-3 font-semibold">
                        <li>Login</li>
                        <li>Signup</li>
                    </div>
                </ul>
            </nav>
            <button class="md:hidden" @click="isActive = !isActive">
                <i
                    :class="[
                        'bi text-2xl',
                        {
                            'bi-list': !isActive,
                            'bi-x-lg': isActive,
                        },
                    ]"
                ></i>
            </button>
        </header>
    </div>
</template>
