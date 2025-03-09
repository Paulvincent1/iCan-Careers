<script setup>
import { Link, router, usePage } from "@inertiajs/vue3";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import { onMounted, ref, computed, onBeforeUnmount } from "vue";
import ReusableModal from "../Components/Modal/ReusableModal.vue";
import SuccessfulMessage from "../Components/Popup/SuccessfulMessage.vue";

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
        }, 2000);
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

    if (channelChatHeads) {
        console.log("unsub");

        channelChatHeads.unsubscribe();
    }

    channelChatHeads = window.Echo.channel(channelName).listen(
        ".message.event",
        (e) => {
            unshiftLatestChatHead(e.user.id, e);

            console.log(chatHeads.value);
        },
    );
}

function unshiftLatestChatHead(userId, newChatHead) {
    const index = chatHeads.value.find((ch) => {
        return Number(ch?.user.id) === Number(userId);
    });

    if (index) {
        if (index != 0) {
            chatHeads.value.splice(index, 1);
            chatHeads.value.unshift(newChatHead);
        } else {
            chatHeads.value[index] = newChatHead;
        }
    } else {
        chatHeads.value.unshift(newChatHead);
    }
}

function goToChat(userId) {
    console.log("hi");

    router.get("/messages/", {
        user: userId,
    });
}

onBeforeUnmount(() => {
    if (channelChatHeads) {
        channelChatHeads.unsubscribe();
        // Stop listening to the '.message.event' event on the channel
        channelChatHeads.stopListening(".message.event");
    }
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
                        <div
                            class="mb-3 mt-4 h-[84px] min-h-[84px] w-[84px] min-w-[84px]"
                        >
                            <img
                                :src="
                                    user.profile_photo_path
                                        ? user.profile_photo_path
                                        : 'assets/profile_placeholder.jpg'
                                "
                                alt="User Profile"
                                class="h-full w-full rounded-[500px]"
                            />
                        </div>
                        <Link
                            :href="route('employer.profile')"
                            as="button"
                            class="mb-3 flex cursor-pointer items-center gap-2 font-bold text-gray-500 hover:underline"
                        >
                            <p class="">
                                {{ $page.props.auth.user.authenticated.name }}
                            </p>
                            <i class="bi bi-arrow-right"></i>
                        </Link>
                        <div class="flex flex-col items-center">
                            <div v-if="subscriptionProps">
                                <p class="font-bold">
                                    <span :class="subscriptionClass">{{
                                        subscriptionProps.subscription_type
                                    }}</span>
                                </p>
                            </div>
                            <p v-else>No active subscription.</p>
                        </div>
                    </div>

                    <div class="mb-6 h-[400px] rounded-lg bg-white p-4">
                        <div class="flex items-center justify-between">
                            <p class="text-lg font-bold text-[#171816]">
                                Inbox
                            </p>
                            <Link
                                :href="route('messages')"
                                class="text-sm text-[#171816] underline"
                                >See All
                                <i class="bi bi-caret-right"></i>
                            </Link>
                        </div>
                        <div>
                            <div
                                v-for="(chatHead, index) in chatHeads"
                                @click="goToChat(chatHead.user.id)"
                                :key="chatHead.latestMessage.id"
                                :class="[
                                    'flex cursor-pointer gap-2 p-4',
                                    {
                                        'bg-slate-300':
                                            chatHead.user.id ===
                                            Number(route().params.user),
                                    },
                                ]"
                            >
                                <div class="h-12 w-12">
                                    <img
                                        src="/assets/profile_placeholder.jpg"
                                        alt=""
                                        class="h-full w-full rounded-full"
                                    />
                                </div>
                                <div>
                                    <p>{{ chatHead.user.email }}</p>
                                    <p class="text-sm">
                                        {{ chatHead.latestMessage?.message }}
                                    </p>
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
                                <Link
                                    :href="route('create.job')"
                                    class="flex items-center gap-2 rounded-full p-2 text-sm text-[#171816] underline"
                                >
                                    <span>Post Job</span>
                                    <i class="bi bi-plus-lg"></i>
                                    <!-- Add Plus Icon -->
                                </Link>
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
                                        <th class="font-normal">Job Title</th>
                                        <th class="font-normal">Job Type</th>
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
                                            {{ job.job_title }}
                                        </td>
                                        <td class="py-5 text-center">
                                            {{ job.job_type }}
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
                                                class="text-lg text-[#171816]"
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
                                    </li></swiper-slide
                                >
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
                                    </li></swiper-slide
                                >
                            </swiper-container>
                        </div>

                        <div class="flex-1 overflow-y-auto">
                            <div
                                v-for="(invoice, index) in invoices"
                                :key="invoice.id"
                                class="mx-2 mb-2 flex items-center gap-2 border-b p-3"
                            >
                                <div class="h-11 w-11">
                                    <img
                                        :src="
                                            invoice.worker.profile_img ??
                                            '/assets/profile_placeholder.jpg'
                                        "
                                        alt=""
                                        class="h-full w-full rounded-full"
                                    />
                                </div>
                                <div>
                                    <p class="text-sm font-bold">
                                        {{ invoice.worker.name }}
                                    </p>
                                    <div class="flex gap-1">
                                        <p class="text-[12px]">
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
                        <div>
                            <p class="p-1 text-lg font-bold text-[#171816]">
                                Currently hired workers
                            </p>
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
                                        <td class="p-2">
                                            <img
                                                class="mx-auto h-12 w-12 rounded-full"
                                                :src="
                                                    worker.profile_img
                                                        ? worker.profile_img
                                                        : '/assets/profile_placeholder.jpg'
                                                "
                                                alt=""
                                            />
                                        </td>
                                        <td class="p-2 text-center">
                                            {{ worker.name }}
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
