<script setup>
import { Link } from "@inertiajs/vue3";
import { onMounted, ref, useTemplateRef, watch } from "vue";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";

let isActive = ref(false);
let profileDropdown = ref(false);

const dropdownButton = useTemplateRef("drop");
onMounted(() => {
    document.addEventListener("click", (event) => {
        if (!dropdownButton.value.contains(event.target)) {
            profileDropdown.value = false;
        }
    });
});

watch(isActive, () => {
    isActive.value
        ? (document.body.style.overflow = "hidden")
        : (document.body.style.overflow = "static");
});

window.addEventListener("resize", () => {
    if (window.innerWidth >= 768) {
        if (isActive.value === true) {
            document.body.style.position = "static";
        }
    }
    if (window.innerWidth <= 768) {
        if (isActive.value === true) {
            document.body.style.position = "fixed";
        }
    }
});
</script>
<template>
    <div class="fixed top-0 z-50 w-full border-b bg-white shadow">
        <header
            class="xs container mx-auto flex h-[4.625rem] items-center justify-between px-[0.5rem] xl:max-w-7xl"
        >
            <p class=""><Link :href="route('home')">iCan Careers</Link></p>
            <nav
                :class="[
                    'fixed inset-0 top-[4.626rem] z-[1000] flex h-[calc(100vh-4.625rem)] w-full flex-col items-center overflow-auto bg-white transition-all md:static md:inset-0 md:h-[100%] md:w-auto md:translate-x-0 md:flex-row md:overflow-visible',
                    {
                        'translate-x-[100%]': !isActive,
                    },
                ]"
            >
                <ul
                    class="flex h-[100%] flex-col items-center justify-start gap-16 py-3 md:static md:flex-row md:gap-4"
                >
                    <li class="mt-6 md:my-auto md:pr-5">
                        <Link href="/" @click="isActive = false">Learning</Link>
                    </li>
                    <li class="md:my-auto md:pr-5">
                        <Link href="/pricing" @click="isActive = false"
                            >Pricing</Link
                        >
                    </li>
                    <!-- <li class="md:my-auto">
                        <Link
                            href="/"
                            class="rounded-3xl bg-[#024570] px-7 py-2 font-medium text-white"
                            @click="isActive = false"
                        >
                            POST A JOB
                        </Link>
                    </li> -->
                    <li class="flex items-center md:pr-3 lg:border-r-[1px]">
                        <Link
                            :href="route('jobsearch')"
                            class="rounded-3xl bg-green-500 px-7 py-2 font-medium text-white"
                            @click="isActive = false"
                        >
                            FIND JOBS
                        </Link>
                    </li>
                    <div
                        v-if="!$page.props.auth.user.authenticated"
                        class="flex items-center gap-5"
                    >
                        <li>
                            <Link
                                :href="route('login')"
                                class="font-semibold"
                                @click="isActive = false"
                                >LOG IN</Link
                            >
                        </li>
                        <li>
                            <Link
                                :href="route('register.create')"
                                class="font-semibold"
                                @click="isActive = false"
                                >SIGN UP</Link
                            >
                        </li>
                    </div>

                    <li
                        ref="drop"
                        v-show="$page.props.auth.user.authenticated"
                        class="relative flex items-center justify-center gap-1 hover:cursor-pointer"
                        @click="profileDropdown = !profileDropdown"
                    >
                        <img
                            class="w-7 rounded-[400px] object-cover"
                            src="/assets/profile_placeholder.jpg"
                            alt=""
                        />
                        <div class="flex gap-1">
                            <p class="text-[12px]">Me</p>
                            <i class="bi bi-chevron-down text-[12px]"></i>
                        </div>

                        <div
                            v-show="profileDropdown"
                            class="absolute top-10 w-40 rounded bg-white px-3 py-2 text-sm shadow md:right-0 md:top-14"
                        >
                            <Link class="flex gap-2 p-2" href="/">
                                <i class="bi bi-person"></i>
                                <p>My Profile</p>
                            </Link>
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="flex gap-2 p-2"
                            >
                                <i class="bi bi-box-arrow-left"></i>
                                <p>Logout</p>
                            </Link>
                        </div>
                    </li>
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
