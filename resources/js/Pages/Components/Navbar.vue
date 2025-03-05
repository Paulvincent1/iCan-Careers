<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { onMounted, ref, useTemplateRef, watch } from "vue";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import { nanoid } from "nanoid";

let isActive = ref(false);
let profileDropdown = ref(false);

const dropdownButton = useTemplateRef("drop");
const notficationButton = useTemplateRef("dropNotification");
onMounted(() => {
    document.addEventListener("click", (event) => {
        if (!dropdownButton.value.contains(event.target)) {
            profileDropdown.value = false;
        }
        if (!notficationButton.value.contains(event.target)) {
            showNotificationDropDown.value = false;
        }
    });
});

watch(isActive, () => {
    isActive.value
        ? (document.body.style.overflow = "hidden")
        : (document.body.style.overflow = "auto");
});

let showNotificationDropDown = ref(false);

window.addEventListener("resize", () => {
    if (window.innerWidth >= 768) {
        if (isActive.value === true) {
            document.body.style.overflow = "auto";
        }
    }
    if (window.innerWidth <= 768) {
        if (isActive.value === true) {
            document.body.style.overflow = "hidden";
        }
    }
});

// notfications
let page = usePage();

let notifications = ref(page.props.auth.user.unreadNotifications);

function unshiftLatestNotification(notif) {
    notifications.value.unshift(notif);
}

onMounted(() => {
    window.Echo.channel(
        "notification-" + page.props.auth.user.authenticated?.id,
    ).listen(".notification.event", (notif) => {
        console.log(notif);
        unshiftLatestNotification({
            id: nanoid(),
            data: {
                status: notif.status,
                message: notif.message,
            },
        });
    });
});

console.log(page.props.auth.user.unreadNotifications);
</script>
<template>
    <div class="fixed top-0 z-50 w-full border-b bg-white shadow">
        <header
            class="xs container mx-auto flex h-[4.625rem] items-center justify-between px-[0.5rem] xl:max-w-7xl"
        >
            <p class="">
                <Link :href="route('home')"
                    ><img
                        src="/assets/iCanCareersLogofinal.png"
                        alt=""
                        class="h-10 w-auto object-contain md:h-14 lg:h-16"
                /></Link>
            </p>
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
                        <Link href="/learning" @click="isActive = false"
                            >Learning</Link
                        >
                    </li>
                    <li
                        class="md:my-auto md:pr-5"
                        v-if="
                            $page.props.auth.user.role?.name === 'Employer' ||
                            !$page.props.auth.user.authenticated
                        "
                    >
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
                    <li
                        v-if="
                            !(
                                $page.props.auth.user.role?.name === 'Employer'
                            ) || !$page.props.auth.user.authenticated
                        "
                        class="flex items-center md:pr-3 lg:border-r-[1px]"
                    >
                        <Link
                            :href="route('jobsearch')"
                            class="rounded-3xl bg-[#fa8334] px-7 py-2 font-medium text-white"
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
                        v-show="$page.props.auth.user.authenticated"
                        class="relative flex items-center justify-center gap-3 hover:cursor-pointer"
                    >
                        <div
                            ref="dropNotification"
                            @click="
                                showNotificationDropDown =
                                    !showNotificationDropDown
                            "
                            class="flex flex-col"
                        >
                            <i class="bi bi-bell text-lg"></i>
                            <div
                                v-show="showNotificationDropDown"
                                class="absolute right-[50%] top-10 h-fit max-h-[calc(100vh-4.625rem-3.5rem)] w-80 translate-x-[50%] overflow-y-auto rounded bg-white px-5 py-2 text-sm shadow md:right-0 md:top-14 md:translate-x-0"
                            >
                                <div class="mb-2">
                                    <p class="text-xl font-bold">
                                        Notifications
                                    </p>
                                </div>
                                <div
                                    :class="[
                                        {
                                            'mb-6': notifications?.length,
                                            'mb-3': !notifications?.length,
                                        },
                                    ]"
                                >
                                    <p
                                        class="font-bold underline underline-offset-8"
                                    >
                                        All
                                    </p>
                                </div>
                                <div>
                                    <div
                                        v-for="notification in notifications"
                                        :key="notification.id"
                                        class="mb-3 flex flex-row gap-3"
                                    >
                                        <div class="h-[32px] min-w-8">
                                            <img
                                                class="h-full w-full rounded-full object-cover"
                                                src="assets/images.png"
                                                alt=""
                                            />
                                        </div>
                                        <div>
                                            <p class="font-bold">
                                                {{ notification.data.status }}
                                            </p>
                                            <p class="text-[10px]">
                                                {{ notification.data.message }}
                                            </p>
                                        </div>
                                    </div>
                                    <div v-if="!notifications?.length">
                                        <p class="mb-3 text-center">
                                            No Notifications
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            ref="drop"
                            @click="profileDropdown = !profileDropdown"
                            class="flex items-center gap-1"
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
                                class="absolute right-[50%] top-10 w-40 translate-x-[50%] rounded bg-white px-3 py-2 text-sm shadow md:right-0 md:top-14 md:translate-x-0"
                            >
                                <Link
                                    class="flex gap-2 p-2"
                                    :href="route('home')"
                                    @click="isActive = false"
                                >
                                    <i class="bi bi-person"></i>
                                    <p>Dashboard</p>
                                </Link>
                                <Link
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    class="flex gap-2 p-2"
                                    @click="isActive = false"
                                >
                                    <i class="bi bi-box-arrow-left"></i>
                                    <p>Logout</p>
                                </Link>
                            </div>
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
