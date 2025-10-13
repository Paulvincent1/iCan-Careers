<script setup>
import { Link, router, usePage } from "@inertiajs/vue3";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import {
    onMounted,
    ref,
    computed,
    onBeforeUnmount,
    getCurrentInstance,
} from "vue";
import ReusableModal from "../Components/Modal/ReusableModal.vue";
import SuccessfulMessage from "../Components/Popup/SuccessfulMessage.vue";
import ProfileHoverCard from "../Components/ProfileHoverCard.vue";
import ProfileJobHover from "../Components/ProfileJobHover.vue";
import dayjs from "dayjs";

let props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    jobsProps: null,
    subscriptionProps: {
        type: Object,
        required: false,
    },
    businessProps: {
        type: Object,
        default: () => null,
    },
    currentWorkerProps: null,
    invoiceProps: null,
    successMessage: null,
    chatHeadsProps: null,
});

let messageShow = ref(false);
onMounted(() => {
    if (props.successMessage) {
        messageShow.value = true;
        setTimeout(() => {
            messageShow.value = false;
        }, 3000);
    }
});

let page = usePage();
let jobs = ref(null);

let jobTag = ref(
    page.props.auth.user.employer.subscription.subscription_type === "Free"
        ? "Pending"
        : "Open",
);

onMounted(() => {
    listenChannelChatHeads();
    jobs.value = props.jobsProps.filter((job) => {
        return job.job_status === jobTag.value;
    });
});
const subscriptionDetails = computed(() => {
    if (!props.subscriptionProps) return null;

    const plans = {
        Free: {
            name: "Free Plan",
            icon: "bi-coin",
            color: "gray",
            features: [
                "3 job posting",
                "Basic profile",
                "Limited applications",
            ],
            upgradeTo: "Pro",
        },
        Pro: {
            name: "Pro Plan",
            icon: "bi-star",
            color: "blue",
            features: [
                "5 job postings",
                "Featured listings",
                "Priority support",
            ],
            upgradeTo: "Premium",
        },
        Premium: {
            name: "Premium Plan",
            icon: "bi-award",
            color: "yellow",
            features: ["5 job postings", "Top visibility", "Direct messaging"],
            upgradeTo: null,
        },
    };

    return plans[props.subscriptionProps.subscription_type] || null;
});

// Computed property for dynamic styling
const subscriptionClass = computed(() => {
    if (!props.subscriptionProps) return "";
    switch (props.subscriptionProps.subscription_type) {
        case "Free":
            return "text-gray-500 bg-gray-100 px-2 py-1 rounded";
        case "Pro":
            return "text-blue-500 bg-blue-100 px-2 py-1 rounded";
        case "Premium":
            return "text-yellow-500 bg-yellow-100 px-2 py-1 rounded";
        default:
            return "";
    }
});

// let jobTag = ref(page.props.auth.user.employer.subscription);

function switchJobTag(jobstatus) {
    jobs.value = props.jobsProps.filter((job) => {
        return job.job_status === jobstatus;
    });
    jobTag.value = jobstatus;
}

console.log(props.currentWorkerProps);

let paymentModal = ref(false);
let invoiceUrl = ref(null);
function closePaymentModal() {
    paymentModal.value = false;
    invoiceUrl.value = null;
    // location.reload();
}

function showPaymentModal(paymentUrlParam) {
    paymentModal.value = true;
    invoiceUrl.value = paymentUrlParam;
}

let invoiceTag = ref("PENDING");
let invoices = ref(null);
onMounted(() => {
    invoices.value = props.invoiceProps.filter((invoice) => {
        return invoice.status === "PENDING";
    });
});

function switchInvoiceTag(tag) {
    invoiceTag.value = tag;
    if (tag === "PENDING") {
        invoices.value = props.invoiceProps.filter((invoice) => {
            return invoice.status === "PENDING";
        });
    } else {
        invoices.value = props.invoiceProps.filter((invoice) => {
            return invoice.status != "PENDING";
        });
    }
}

// inbox
let chatHeads = ref(props.chatHeadsProps ?? null);

let channelChatHeads = null;
function listenChannelChatHeads() {
    const channelName = "chathead-" + page.props.auth.user.authenticated.id;

    console.log(window.Echo.connector.channels[channelName]?.subscription);

    if (window.Echo.connector.channels[channelName]?.subscription) {
        if (
            !window.Echo.connector.channels[channelName]?.subscription
                .subscribed
        ) {
            window.Echo.connector.channels[channelName].subscribe();
            return;
        }
    }

    channelChatHeads = window.Echo.channel(channelName).listen(
        ".message.event",
        (e) => {
            unshiftLatestChatHead(e.user.id, e);

            console.log(chatHeads.value);
        },
    );
    console.log(channelChatHeads.subscription);
}

function unshiftLatestChatHead(userId, newChatHead) {
    const index = chatHeads.value.findIndex((ch) => {
        return Number(ch?.user.id) === Number(userId);
    });

    console.log(index);

    if (index) {
        if (index != 0) {
            chatHeads.value.splice(index, 1);
            chatHeads.value.unshift(newChatHead);
        } else {
            chatHeads.value[index] = newChatHead;
        }
    } else {
        chatHeads.value[index] = newChatHead;
    }
}
function formatTime(timestamp) {
    const now = dayjs();
    const messageTime = dayjs(timestamp);

    // If today, show time only
    if (messageTime.isSame(now, 'day')) {
        return messageTime.format('h:mm A');
    }

    // If yesterday, show "Yesterday"
    if (messageTime.isSame(now.subtract(1, 'day'), 'day')) {
        return 'Yesterday';
    }

    // If within the same week, show day name
    if (messageTime.isAfter(now.subtract(7, 'day'))) {
        return messageTime.format('ddd');
    }

    // Otherwise show date
    return messageTime.format('MMM D');
}

function goToChat(userId) {
    console.log("hi");

    router.get("/messages/", {
        user: userId,
    });
}

onBeforeUnmount(() => {
    channelChatHeads.stopListening(".message.event");
});

</script>
<template>
    <Head title="Dashboard | iCan Careers" />
    <div class="bg-[#eff2f6] text-[#171816]">
        <div class="xs container mx-auto px-[0.5rem] xl:max-w-7xl">
            <div class="grid gap-0 pt-8 lg:grid-cols-[300px,1fr] lg:gap-6">
                <div>
                    <div
                        class="mb-6 flex flex-col items-center justify-center rounded-lg bg-white p-4"
                    >
                        <Link
                            :href="route('employer.profile')"
                            class="group relative mb-3 flex cursor-pointer flex-col items-center gap-2 p-5 font-bold text-gray-500 hover:underline"
                        >
                            <!-- Profile Picture -->
                            <div class="h-[84px] w-[84px]">
                                <img
                                    :src="
                                        user.profile_photo_path
                                            ? user.profile_photo_path
                                            : 'assets/profile_placeholder.jpg'
                                    "
                                    alt="User Profile"
                                    class="h-full w-full rounded-full object-cover"
                                />
                            </div>

                            <!-- User Name & Arrow -->
                            <div class="flex items-center gap-1">
                                <p>{{ user.name }}</p>
                                <i class="bi bi-arrow-right"></i>
                            </div>

                            <!-- Tooltip -->
                            <span
                                class="absolute left-1/2 top-full z-[999] mt-2 -translate-x-1/2 whitespace-nowrap rounded-md bg-black px-2 py-1 text-xs text-white opacity-0 transition group-hover:opacity-100"
                            >
                                View Profile
                                <!-- Tooltip Arrow -->
                                <span
                                    class="absolute left-1/2 top-0 -translate-x-1/2 -translate-y-full border-4 border-transparent border-b-black"
                                ></span>
                            </span>
                        </Link>
                        <!-- Enhanced Subscription Card -->
                        <div v-if="subscriptionDetails" class="mt-2 w-full">
                            <div
                                :class="[
                                    'relative rounded-xl border-2 p-4 shadow-sm transition-all hover:shadow-md',
                                    {
                                        'border-gray-300 bg-gradient-to-br from-gray-50 to-gray-100':
                                            subscriptionDetails.color ===
                                            'gray',
                                        'border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100':
                                            subscriptionDetails.color ===
                                            'blue',
                                        'border-yellow-200 bg-gradient-to-br from-yellow-50 to-yellow-100':
                                            subscriptionDetails.color ===
                                            'yellow',
                                    },
                                ]"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center">
                                        <div
                                            :class="[
                                                'mr-3 rounded-full p-3',
                                                {
                                                    'bg-gray-200 text-gray-700':
                                                        subscriptionDetails.color ===
                                                        'gray',
                                                    'bg-blue-200 text-blue-700':
                                                        subscriptionDetails.color ===
                                                        'blue',
                                                    'bg-yellow-200 text-yellow-700':
                                                        subscriptionDetails.color ===
                                                        'yellow',
                                                },
                                            ]"
                                        >
                                            <i
                                                class="bi text-xl"
                                                :class="
                                                    subscriptionDetails.icon
                                                "
                                            ></i>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold">
                                                {{ subscriptionDetails.name }}
                                            </h3>
                                            <p class="text-sm text-gray-600">
                                                Active Plan
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        :class="[
                                            'rounded-full px-3 py-1 text-xs font-semibold',
                                            {
                                                'bg-gray-200 text-gray-800':
                                                    subscriptionDetails.color ===
                                                    'gray',
                                                'bg-blue-200 text-blue-800':
                                                    subscriptionDetails.color ===
                                                    'blue',
                                                'bg-yellow-200 text-yellow-800':
                                                    subscriptionDetails.color ===
                                                    'yellow',
                                            },
                                        ]"
                                    >
                                        {{
                                            props.subscriptionProps
                                                .subscription_type
                                        }}
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <h4 class="mb-2 text-sm font-semibold">
                                        Plan Features:
                                    </h4>
                                    <ul class="space-y-1 text-sm">
                                        <li
                                            v-for="(
                                                feature, index
                                            ) in subscriptionDetails.features"
                                            :key="index"
                                            class="flex items-start"
                                        >
                                            <i
                                                class="bi bi-check2 mr-2 mt-0.5"
                                                :class="{
                                                    'text-gray-600':
                                                        subscriptionDetails.color ===
                                                        'gray',
                                                    'text-blue-600':
                                                        subscriptionDetails.color ===
                                                        'blue',
                                                    'text-yellow-600':
                                                        subscriptionDetails.color ===
                                                        'yellow',
                                                }"
                                            ></i>
                                            <span>{{ feature }}</span>
                                        </li>
                                    </ul>
                                </div>

                                <div
                                    v-if="subscriptionDetails.upgradeTo"
                                    class="mt-4 border-t border-gray-200 pt-3"
                                >
                                    <Link
                                        :href="route('pricing')"
                                        :class="[
                                            'block rounded-lg px-4 py-2 text-center text-sm font-semibold transition-colors',
                                            {
                                                'bg-gray-800 text-white hover:bg-gray-900':
                                                    subscriptionDetails.color ===
                                                    'gray',
                                                'bg-blue-600 text-white hover:bg-blue-700':
                                                    subscriptionDetails.color ===
                                                    'blue',
                                                'bg-yellow-500 text-gray-900 hover:bg-yellow-600':
                                                    subscriptionDetails.color ===
                                                    'yellow',
                                            },
                                        ]"
                                    >
                                        Upgrade your Plan to
                                        {{ subscriptionDetails.upgradeTo }}
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <div v-else class="mt-2 w-full">
                            <div
                                class="rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 p-4 text-center"
                            >
                                <div
                                    class="mb-2 inline-block rounded-full bg-gray-200 p-2 text-gray-600"
                                >
                                    <i class="bi bi-exclamation-circle"></i>
                                </div>
                                <h3 class="font-semibold">
                                    No Active Subscription
                                </h3>
                                <p class="mb-3 text-sm text-gray-600">
                                    Get started with a plan to access all
                                    features
                                </p>
                                <Link
                                    :href="route('subscription.plans')"
                                    class="inline-block rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-gray-900"
                                >
                                    View Plans
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6 h-[400px] rounded-lg bg-white p-4">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-lg font-bold text-[#171816]">
                            Inbox
                        </p>
                        <Link
                            :href="route('messages')"
                            class="group relative flex items-center gap-2 rounded-full p-2 text-sm text-[#171816] underline"
                        >
                            <span>See All</span>
                            <i class="bi bi-caret-right"></i>
                            <span
                                class="absolute left-1/2 top-full mt-2 -translate-x-1/2 whitespace-nowrap rounded-md bg-black px-2 py-1 text-xs text-white opacity-0 transition group-hover:opacity-100"
                            >
                                See all messages
                                <span
                                    class="absolute left-1/2 top-0 -translate-x-1/2 -translate-y-full border-4 border-transparent border-b-black"
                                ></span>
                            </span>
                        </Link>
                    </div>

                    <div class="h-[320px] overflow-y-auto">
                        <div class="space-y-2">
                            <div
                                v-for="(chatHead, index) in chatHeads"
                                @click="goToChat(chatHead.user.id)"
                                :key="chatHead.latestMessage?.id"
                                :class="[
                                    'grid grid-cols-[auto,1fr,auto] gap-3 p-3 rounded-lg cursor-pointer transition-all hover:shadow-sm',
                                    {
                                        'bg-blue-50 ring-2 ring-blue-200':
                                            chatHead.user.id === Number(route().params.user),
                                        'bg-gray-50 hover:bg-gray-100':
                                            chatHead.user.id !== Number(route().params.user),
                                    },
                                ]"
                            >
                                <!-- Avatar -->
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img
                                        :src="chatHead.user.profile_img_url ?? '/assets/profile_placeholder.jpg'"
                                        alt="User avatar"
                                        class="h-full w-full rounded-full object-cover"
                                    />
                                </div>

                                <!-- Content -->
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 truncate text-sm">
                                        {{ chatHead.user.email }}
                                    </p>
                                    <p class="text-gray-600 text-xs mt-1 truncate">
                                        {{ chatHead.latestMessage?.message || 'No messages yet' }}
                                    </p>
                                </div>

                                <!-- Right side indicators -->
                                <div class="flex flex-col items-end justify-between text-right">
                                    <!-- Timestamp -->
                                    <p
                                        v-if="chatHead.latestMessage?.created_at"
                                        class="text-xs text-gray-400 whitespace-nowrap"
                                    >
                                        {{ formatTime(chatHead.latestMessage.created_at) }}
                                    </p>

                                    <!-- Unread count -->
                                    <span
                                        v-if="chatHead.unread_count > 0"
                                        class="bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center mt-1"
                                    >
                                        {{ chatHead.unread_count > 9 ? '9+' : chatHead.unread_count }}
                                    </span>
                                </div>
                            </div>

                            <!-- Empty state -->
                            <div
                                v-if="chatHeads.length === 0"
                                class="flex flex-col items-center justify-center h-64 text-gray-500"
                            >
                                <i class="bi bi-chat-dots text-4xl mb-3 text-gray-300"></i>
                                <p class="text-lg font-medium text-gray-400">No conversations</p>
                                <p class="text-sm text-gray-400 mt-1">Your messages will appear here</p>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <div
                    class="grid grid-cols-1 gap-6 rounded lg:grid-cols-[400px,1fr] xl:grid-cols-[600px,1fr]"
                >
                    <div
                        class="col-span-2 h-[424px] rounded-lg bg-white p-3 lg:col-span-1"
                    >
                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <p class="p-1 text-lg font-bold text-[#171816]">
                                    Job Status
                                </p>
                                <!-- <Link
                                    :href="route('create.job')"
                                    class="group relative flex items-center gap-2 rounded-full p-2 text-sm text-[#171816] underline"
                                >
                                    <span>Post Job</span>
                                    <i class="bi bi-plus-lg"></i>


                                    <span
                                        class="absolute left-1/2 top-full mt-2 -translate-x-1/2 whitespace-nowrap rounded-md bg-black px-2 py-1 text-xs text-white opacity-0 transition group-hover:opacity-100"
                                    >
                                        Click to post a job

                                        <span
                                            class="absolute left-1/2 top-0 -translate-x-1/2 -translate-y-full border-4 border-transparent border-b-black"
                                        ></span>
                                    </span>
                                </Link> -->
                            </div>

                            <swiper-container
                                class="mb-3 text-[12px]"
                                slides-per-view="auto"
                                :space-between="5"
                            >
                                <swiper-slide
                                    v-if="
                                        page.props.auth.user.employer
                                            .subscription.subscription_type ===
                                        'Free'
                                    "
                                    class="w-fit"
                                >
                                    <li
                                        @click="switchJobTag('Pending')"
                                        :class="[
                                            'cursor-pointer rounded-full border border-[#F1F1F1] px-2 py-1 text-center',
                                            {
                                                'bg-[#171816] text-white':
                                                    jobTag === 'Pending',
                                                'text-[#9f9f9f]':
                                                    jobTag != 'Pending',
                                            },
                                        ]"
                                    >
                                        Pending Jobs
                                    </li></swiper-slide
                                >
                                <swiper-slide class="w-fit">
                                    <li
                                        @click="switchJobTag('Open')"
                                        :class="[
                                            'cursor-pointer rounded-full border border-[#F1F1F1] px-2 py-1 text-center text-[#9f9f9f]',
                                            {
                                                'bg-[#171816] text-white':
                                                    jobTag === 'Open',
                                            },
                                        ]"
                                    >
                                        Open Jobs
                                    </li></swiper-slide
                                >
                                <swiper-slide class="w-fit">
                                    <li
                                        @click="switchJobTag('Closed')"
                                        :class="[
                                            'cursor-pointer rounded-full border border-[#F1F1F1] px-2 py-1 text-center text-[#9f9f9f]',
                                            {
                                                'bg-[#171816] text-white':
                                                    jobTag === 'Closed',
                                            },
                                        ]"
                                    >
                                        Close Jobs
                                    </li></swiper-slide
                                >
                            </swiper-container>
                        </div>

                        <div class="h-[300px] overflow-auto">
                            <table class="w-full min-w-[500px] table-fixed">
                                <thead class="text-sm text-slate-500">
                                    <tr>
                                        <th class="font-normal">Job Type</th>
                                        <th class="font-normal">Job Title</th>
                                        <th class="font-normal">Salary</th>
                                        <th class="font-normal">
                                            Applications
                                        </th>
                                        <th class="font-normal">Details</th>
                                    </tr>
                                </thead>
                                <tbody class="text-[12px]">
                                    <tr
                                        v-for="(job, index) in jobs"
                                        :key="job.id"
                                    >
                                        <td class="py-5 text-center">
                                            {{ job.job_type }}
                                        </td>
                                        <td class="py-5 text-center">
                                            <Link
                                                as="button"
                                                :href="
                                                    route(
                                                        'employer.jobpost.show',
                                                        job.id,
                                                    )
                                                "
                                                class=""
                                                ><ProfileJobHover
                                                    :job-id="job.id"
                                                    :business-id="
                                                        businessProps
                                                            ? businessProps.id
                                                            : null
                                                    "
                                                >
                                                    <span
                                                        class="cursor-pointer text-blue-600 hover:underline"
                                                    >
                                                        {{ job.job_title }}
                                                    </span>
                                                </ProfileJobHover>
                                            </Link>
                                        </td>
                                        <td class="py-5 text-center">
                                            {{ job.salary }}
                                        </td>
                                        <td
                                            :class="[
                                                'py-5 text-center underline',
                                                {
                                                    'pointer-events-none text-black no-underline':
                                                        jobTag === 'Pending',
                                                    'text-blue-500':
                                                        jobTag != 'Pending',
                                                },
                                            ]"
                                        >
                                            <Link
                                                class=""
                                                :href="
                                                    route(
                                                        'job.applicants',
                                                        job.id,
                                                    )
                                                "
                                            >
                                                {{
                                                    job.users_who_applied.length
                                                }}
                                            </Link>
                                        </td>
                                        <td class="py-5 text-center">
                                            <Link
                                                :href="
                                                    route(
                                                        'employer.jobpost.show',
                                                        job.id,
                                                    )
                                                "
                                                as="button"
                                                class="text-sm text-[#171816]"
                                            >
                                                <i
                                                    class="bi bi-arrow-right"
                                                ></i>
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div
                        class="col-span-2 flex h-[424px] flex-col overflow-hidden rounded-lg bg-white p-3 md:col-span-1"
                    >
                        <div>
                            <p class="mb-2 text-lg font-bold text-[#171816]">
                                Invoices
                            </p>
                            <swiper-container
                                class="mb-3 text-[12px]"
                                slides-per-view="auto"
                                :space-between="5"
                            >
                                <swiper-slide class="w-fit text-[12px]">
                                    <li
                                        @click="switchInvoiceTag('PENDING')"
                                        :class="[
                                            'cursor-pointer rounded-full border border-[#F1F1F1] px-2 py-1',
                                            {
                                                'bg-[#171816] text-white':
                                                    invoiceTag === 'PENDING',
                                                'text-[#9f9f9f]':
                                                    invoiceTag != 'PENDING',
                                            },
                                        ]"
                                    >
                                        Pending
                                    </li>
                                </swiper-slide>
                                <swiper-slide class="w-fit">
                                    <li
                                        @click="switchInvoiceTag('HISTORY')"
                                        :class="[
                                            'cursor-pointer rounded-full border border-[#F1F1F1] px-2 py-1 text-[#9f9f9f]',
                                            {
                                                'bg-[#171816] text-white':
                                                    invoiceTag === 'HISTORY',
                                            },
                                        ]"
                                    >
                                        History
                                    </li>
                                </swiper-slide>
                            </swiper-container>
                        </div>

                        <div class="flex-1 overflow-y-auto">
                            <div
                                v-for="(invoice, index) in invoices"
                                :key="invoice.id"
                                class="mx-2 mb-2 flex items-center gap-2 border-b p-3"
                            >
                                <div class="h-11 w-11 flex-shrink-0">
                                    <img
                                        :src="
                                            invoice.worker.profile_img_url ??
                                            '/assets/profile_placeholder.jpg'
                                        "
                                        alt=""
                                        class="h-full w-full rounded-full object-cover"
                                    />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-bold">
                                        {{ invoice.worker.name }}
                                    </p>
                                    <div class="flex flex-wrap gap-1">
                                        <p class="truncate text-[12px]">
                                            {{ invoice.worker.email }}
                                        </p>
                                        <div
                                            v-if="invoice.status === 'PENDING'"
                                            class="flex gap-2 text-blue-500"
                                        >
                                            <a
                                                :href="
                                                    '/storage/invoices/' +
                                                    invoice.external_id +
                                                    '.pdf'
                                                "
                                                target="_blank"
                                                class="inline-block text-[12px]"
                                                >See details</a
                                            >
                                            <button
                                                @click="
                                                    showPaymentModal(
                                                        invoice.invoice_url,
                                                    )
                                                "
                                                class="text-[12px]"
                                            >
                                                Pay Now
                                                <i
                                                    class="bi bi-arrow-up-right text-[12px]"
                                                ></i>
                                            </button>
                                        </div>
                                        <p
                                            v-else-if="
                                                invoice.status === 'PAID'
                                            "
                                            :class="[
                                                'text-[12px]',
                                                {
                                                    'w-fit rounded-full bg-orange-500 px-2 text-center text-white':
                                                        invoice.status ===
                                                        'PAID',
                                                },
                                            ]"
                                        >
                                            {{ invoice.status }}
                                        </p>
                                        <p
                                            v-else-if="
                                                invoice.status === 'SETTLED'
                                            "
                                            :class="[
                                                'text-[12px]',
                                                {
                                                    'w-fit rounded-full bg-orange-500 px-2 text-center text-white':
                                                        invoice.status ===
                                                        'SETTLED',
                                                },
                                            ]"
                                        >
                                            {{ invoice.status }}
                                        </p>
                                        <p
                                            v-else-if="
                                                invoice.status === 'EXPIRED'
                                            "
                                            :class="[
                                                'text-[12px]',
                                                {
                                                    'w-fit rounded-full bg-red-600 px-2 text-center text-white':
                                                        invoice.status ===
                                                        'EXPIRED',
                                                },
                                            ]"
                                        >
                                            {{ invoice.status }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="col-span-2 mb-8 flex h-[400px] flex-col rounded-lg bg-white p-3"
                    >
                        <div class="flex items-center justify-between">
                            <p class="p-1 text-lg font-bold text-[#171816]">
                                Currently hired workers
                            </p>
                            <Link
                                :href="route('employer.previous.workers')"
                                class="group relative flex items-center gap-2 rounded-full p-2 text-sm text-[#171816] underline"
                            >
                                Previous Worker
                                <i class="bi bi-caret-right"></i>
                                <span
                                    class="absolute left-1/2 top-full mt-2 -translate-x-1/2 whitespace-nowrap rounded-md bg-black px-2 py-1 text-xs text-white opacity-0 transition group-hover:opacity-100"
                                >
                                    Workers you've hired in the past
                                    <!-- Tooltip Arrow -->
                                    <span
                                        class="absolute left-1/2 top-0 -translate-x-1/2 -translate-y-full border-4 border-transparent border-b-black"
                                    ></span>
                                </span>
                            </Link>
                        </div>
                        <div class="flex-1 overflow-auto">
                            <table class="w-full min-w-[500px] table-fixed">
                                <thead class="text-slate-500">
                                    <tr>
                                        <th class="p-2 text-center font-normal">
                                            Image
                                        </th>
                                        <th class="p-2 text-center font-normal">
                                            Name
                                        </th>
                                        <th class="p-2 text-center font-normal">
                                            Email
                                        </th>
                                        <th class="p-2 text-center font-normal">
                                            Profile
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm">
                                    <tr
                                        v-for="(
                                            worker, index
                                        ) in currentWorkerProps"
                                        :key="index"
                                    >
                                        <td class="p-2 text-center">
                                            <Link
                                                as="button"
                                                :href="
                                                    route(
                                                        'worker.show.profile',
                                                        worker.id,
                                                    )
                                                "
                                                class=""
                                            >
                                                <ProfileHoverCard
                                                    :user-id="worker.id"
                                                >
                                                    <img
                                                        class="mx-auto h-12 w-12 rounded-full"
                                                        :src="
                                                            worker.profile_img_url
                                                                ? worker.profile_img_url
                                                                : '/assets/profile_placeholder.jpg'
                                                        "
                                                        alt=""
                                                    />
                                                </ProfileHoverCard>
                                            </Link>
                                        </td>
                                        <td class="p-2 text-center">
                                            <Link
                                                as="button"
                                                :href="
                                                    route(
                                                        'worker.show.profile',
                                                        worker.id,
                                                    )
                                                "
                                                class=""
                                            >
                                                <ProfileHoverCard
                                                    :user-id="worker.id"
                                                >
                                                    <span
                                                        class="cursor-pointer text-blue-600 hover:underline"
                                                    >
                                                        {{ worker.name }}
                                                    </span>
                                                </ProfileHoverCard>
                                            </Link>
                                        </td>

                                        <td class="p-2 text-center">
                                            {{ worker.email }}
                                        </td>
                                        <td class="p-2 text-center">
                                            <Link
                                                as="button"
                                                :href="
                                                    route(
                                                        'worker.show.profile',
                                                        worker.id,
                                                    )
                                                "
                                                class=""
                                            >
                                                <i
                                                    class="bi bi-arrow-right text-lg"
                                                ></i
                                            ></Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <ReusableModal v-if="paymentModal" @closeModal="closePaymentModal">
        <div class="h-[500px] w-[350px] rounded bg-white sm:w-[500px]">
            <iframe
                :src="invoiceUrl"
                title="Xendit Invoice"
                class="h-full w-full"
            >
            </iframe>
        </div>
    </ReusableModal>
    <SuccessfulMessage
        :messageShow="messageShow"
        :messageProp="successMessage"
    ></SuccessfulMessage>
</template>
<style scoped>
 .line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
