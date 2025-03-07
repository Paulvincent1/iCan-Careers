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
    <div class="bg-[#f4f4f4]">
        <div class="xs container mx-auto px-[0.5rem] xl:max-w-7xl">
            <div class="grid gap-0 pt-8 lg:grid-cols-[300px,1fr] lg:gap-6">
                <div>
                    <div
                        class="mb-6 flex flex-col items-center justify-center rounded-lg bg-white p-4"
                    >
                        <div class="mb-3 mt-4 w-[84px]">
                            <img
                                :src="
                                    user.profile_photo_path
                                        ? user.profile_photo_path
                                        : 'assets/profile_placeholder.jpg'
                                "
                                alt="User Profile"
                                class="w-full rounded-[500px]"
                            />
                        </div>
                        <p class="font-bol mb-3">
                            {{ $page.props.auth.user.authenticated.name }}
                        </p>
                        <Link
                            :href="route('employer.profile')"
                            as="button"
                            class="mb-3 w-full max-w-[500px] rounded-lg border px-4 py-2 font-bold"
                        >
                            View Profile
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
                            <p class="text-lg">Inbox</p>
                            <Link
                                :href="route('messages')"
                                class="text-sm text-blue-500"
                                >See All</Link
                            >
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
                            <div class="mb-4 flex items-center justify-between">
                                <p class="p-1 text-lg font-normal">
                                    Job Status
                                </p>
                                <Link
                                    :href="route('create.job')"
                                    class="flex items-center gap-2 rounded-lg bg-[#fa8334] px-4 py-2 font-semibold text-white shadow-md "
                                    title="Click to post a new job"
                                >
                                    <i class="bi bi-plus-lg text-xl"></i>
                                    <!-- Add Plus Icon -->
                                    <span>Post Job</span>
                                </Link>
                            </div>

                            <swiper-container
                                class="mb-3 text-[12px]"
                                slides-per-view="auto"
                                :space-between="10"
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
                                            'cursor-pointer rounded border border-yellow-500 p-1',
                                            {
                                                'bg-yellow-500 text-white':
                                                    jobTag === 'Pending',
                                                'text-yellow-500':
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
                                            'cursor-pointer rounded border border-green-400 p-1 text-green-400',
                                            {
                                                'bg-green-400 text-white':
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
                                            'cursor-pointer rounded border border-red-400 p-1 text-red-400',
                                            {
                                                'bg-red-400 text-white':
                                                    jobTag === 'Closed',
                                            },
                                        ]"
                                    >
                                        Close Jobs
                                    </li></swiper-slide
                                >
                            </swiper-container>
                        </div>

                        <div class="h-[300px] overflow-y-auto">
                            <table class="w-full min-w-[500px] border-collapse">
                                <thead class="bg-slate-200 text-slate-500">
                                    <tr>
                                        <th class="p-2 text-start font-normal">
                                            Job Title
                                        </th>
                                        <th class="p-2 text-start font-normal">
                                            Job Type
                                        </th>
                                        <th class="p-2 text-start font-normal">
                                            Salary
                                        </th>
                                        <th class="p-2 text-start font-normal">
                                            Applications
                                        </th>
                                        <th class="p-2 text-start font-normal">
                                            Details
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm">
                                    <tr
                                        v-for="(job, index) in jobs"
                                        :key="job.id"
                                    >
                                        <td class="p-2 text-start">
                                            {{ job.job_title }}
                                        </td>
                                        <td class="p-2 text-start">
                                            {{ job.job_type }}
                                        </td>
                                        <td class="p-2 text-start">
                                            {{ job.salary }}
                                        </td>
                                        <td
                                            :class="[
                                                'p-2 text-start underline',
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
                                        <td class="p-2 text-start">
                                            <Link
                                                :href="
                                                    route(
                                                        'employer.jobpost.show',
                                                        job.id,
                                                    )
                                                "
                                                as="button"
                                                class="rounded bg-green-500 px-2 py-1 text-white"
                                            >
                                                <i
                                                    class="bi bi-box-arrow-up-right"
                                                ></i>
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div
                        class="col-span-1 flex h-[424px] flex-col overflow-hidden rounded-lg bg-white p-3"
                    >
                        <div>
                            <p class="p-1 text-lg">Invoices</p>
                            <swiper-container
                                class="mb-3 text-[12px]"
                                slides-per-view="auto"
                                :space-between="10"
                            >
                                <swiper-slide class="w-fit">
                                    <li
                                        @click="switchInvoiceTag('PENDING')"
                                        :class="[
                                            'cursor-pointer rounded border border-green-400 p-1',
                                            {
                                                'bg-green-400 text-white':
                                                    invoiceTag === 'PENDING',
                                                'text-green-400':
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
                                            'cursor-pointer rounded border border-green-400 p-1 text-green-400',
                                            {
                                                'bg-green-400 text-white':
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
                                class="mx-2 mb-2 flex items-center gap-4 rounded-full border p-3 shadow-lg"
                            >
                                <div class="h-11 w-11">
                                    <img
                                        :src="
                                            invoice.worker.profile_img ??
                                            '/assets/profile_placeholder.jpg'
                                        "
                                        alt=""
                                        class="w-full rounded-full"
                                    />
                                </div>
                                <div>
                                    <p class="text-sm">
                                        {{ invoice.worker.name }}
                                    </p>
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
                                        v-else-if="invoice.status === 'PAID'"
                                        :class="[
                                            'text-[12px]',
                                            {
                                                'text-green-500':
                                                    invoice.status === 'PAID',
                                            },
                                        ]"
                                    >
                                        {{ invoice.status }}
                                    </p>
                                    <p
                                        v-else-if="invoice.status === 'SETTLED'"
                                        :class="[
                                            'text-[12px]',
                                            {
                                                'text-green-500':
                                                    invoice.status ===
                                                    'SETTLED',
                                            },
                                        ]"
                                    >
                                        {{ invoice.status }}
                                    </p>
                                    <p
                                        v-else-if="invoice.status === 'EXPIRED'"
                                        :class="[
                                            'text-[12px]',
                                            {
                                                'text-red-500':
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
                    <div
                        class="col-span-2 mb-8 flex h-[400px] flex-col rounded-lg bg-white p-3"
                    >
                        <div>
                            <p class="p-1 text-lg">Currently hired workers</p>
                        </div>
                        <div class="flex-1 overflow-auto">
                            <table class="w-full min-w-[500px] table-fixed">
                                <thead>
                                    <tr>
                                        <th class="p-2 font-normal">Image</th>
                                        <th class="p-2 font-normal">Name</th>
                                        <th class="p-2 font-normal">Email</th>
                                        <th class="p-2 font-normal">Profile</th>
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
                                                class="text-blue-500"
                                            >
                                                View Profile</Link
                                            >
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
