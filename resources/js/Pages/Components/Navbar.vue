<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { computed, onMounted, ref, useTemplateRef, watch } from "vue";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import { nanoid } from "nanoid";
import { router } from "@inertiajs/vue3";

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

// notifications
let page = usePage();
let notifications = computed(() => page.props.auth?.user.unreadNotifications);
let unreadMessages = computed(() => page.props.auth?.user.unreadMessages || []);

function unshiftLatestNotification(notif) {
    notifications.value.unshift(notif);
}

let channelNotif = null;
watch(
    () => page.props.auth.user.authenticated?.id,
    () => {
        channelNotif = window.Echo.channel(
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
    },
);

onMounted(() => {
    if (page.props.auth.user.authenticated?.id) {
        channelNotif = window.Echo.channel(
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
    }
});

// Compute profile route based on user role
const profileRoute = computed(() => {
    if (page.props.auth.user.role?.name === "Employer") {
        return route("employer.profile");
    } else {
        return route("worker.profile");
    }
});
</script>

<template>
    <div class="fixed top-0 z-50 w-full border-b bg-white shadow">
        <header
            class="xs container mx-auto flex h-[4.625rem] items-center justify-between px-[0.5rem] xl:max-w-7xl"
        >
            <p>
                <Link :href="route('home')">
                    <img
                        src="/assets/2.png"
                        alt=""
                        class="md:h-13 h-10 w-auto object-contain lg:h-[50px]"
                    />
                </Link>
            </p>

            <nav
                :class="[
                    'fixed inset-0 top-[4.626rem] z-[1000] flex h-[calc(100vh-4.625rem)] w-full flex-col items-center overflow-auto bg-white transition-all md:static md:inset-0 md:h-[100%] md:w-auto md:translate-x-0 md:flex-row md:overflow-visible',
                    { 'translate-x-[100%]': !isActive },
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
                    <li
                        v-if="$page.props.auth.user?.role?.name === 'Employer'"
                        class="md:my-auto"
                    >
                        <Link
                            :href="route('create.job')"
                            class="rounded-3xl bg-[#fa8334] px-7 py-2 font-medium text-white"
                            @click="isActive = false"
                        >
                            POST A JOB
                        </Link>
                    </li>
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
                        v-if="!$page.props.auth.user?.authenticated"
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
                        v-show="$page.props.auth.user?.authenticated"
                        class="relative flex items-center justify-center gap-3 hover:cursor-pointer"
                    >
                        <!-- Message Icon -->
                        <div
                            class="group ml-3 flex cursor-pointer flex-col"
                            @click="$inertia.visit(route('messages'))"
                        >
                            <div class="relative">
                                <i class="bi bi-chat-dots text-lg"></i>
                                <!-- Message Count Badge -->
                                <span
                                    v-if="unreadMessages.length"
                                    class="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-xs font-bold text-white"
                                    :class="{
                                        'px-1': unreadMessages.length < 10,
                                        'px-0.5': unreadMessages.length >= 10,
                                    }"
                                >
                                    {{ unreadMessages.length > 99 ? '99+' : unreadMessages.length }}
                                </span>
                                <span
                                    class="absolute z-[9999] left-1/2 top-full mt-2 -translate-x-1/2 whitespace-nowrap rounded-md bg-black px-2 py-1 text-xs text-white opacity-0 transition group-hover:opacity-100"
                                >
                                    Messages
                                </span>
                            </div>
                        </div>

                        <!-- Notification Icon -->
                        <div
                            ref="dropNotification"
                            @click="
                                showNotificationDropDown =
                                    !showNotificationDropDown
                            "
                            class="group flex flex-col"
                        >
                            <div class="relative">
                                <i class="bi bi-bell text-lg"></i>
                                <!-- Notification Count Badge -->
                                <span
                                    v-if="notifications?.length"
                                    class="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-xs font-bold text-white"
                                    :class="{
                                        'px-1': notifications.length < 10,
                                        'px-0.5': notifications.length >= 10,
                                    }"
                                >
                                    {{ notifications.length > 99 ? '99+' : notifications.length }}
                                </span>
                                <span
                                    class="absolute z-[9999] left-1/2 top-full mt-2 -translate-x-1/2 whitespace-nowrap rounded-md bg-black px-2 py-1 text-xs text-white opacity-0 transition group-hover:opacity-100"
                                >
                                    Notifications
                                </span>
                            </div>
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
                                        'flex justify-between',
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
                                    <Link
                                        method="put"
                                        :href="
                                            route(
                                                'user.mark-all-notification-as-read',
                                            )
                                        "
                                        as="button"
                                        >Mark all as read</Link
                                    >
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
                                                :src="
                                                    notification.data.image ||
                                                    '/assets/SHORTS.svg'
                                                "
                                                @error="event => event.target.src = '/assets/SHORTS.svg'"
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

                        <!-- Profile Dropdown -->
                        <div
                            ref="drop"
                            @click="profileDropdown = !profileDropdown"
                            class="flex items-center gap-1"
                        >
                            <img
                                :src="
                                    $page.props.auth.user.authenticated?.profile_img_url ||
                                    '/assets/profile_placeholder.jpg'
                                "
                                alt="User Profile"
                                class="h-8 w-8 rounded-full object-cover"
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
                                    :href="profileRoute"
                                    @click="isActive = false"
                                >
                                    <i class="bi bi-person"></i>
                                    <p>Profile</p>
                                </Link>
                                <Link
                                    class="flex gap-2 p-2"
                                    :href="route('home')"
                                    @click="isActive = false"
                                >
                                    <i class="bi bi-house"></i>
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
                        { 'bi-list': !isActive, 'bi-x-lg': isActive },
                    ]"
                ></i>
            </button>
        </header>
    </div>
</template>
