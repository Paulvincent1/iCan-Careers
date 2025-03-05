<script setup>
import { Link, router } from "@inertiajs/vue3";
import { getCurrentInstance, onMounted, ref } from "vue";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import ReusableModal from "../Components/Modal/ReusableModal.vue";
import InputFlashMessage from "../Components/InputFlashMessage.vue";
import SuccessfulMessage from "../Components/Popup/SuccessfulMessage.vue";
import dayjs from "dayjs";

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    isPending: {
        type: null,
    },
    savedJobsProps: null,
    jobsAppliedProps: null,
    balanceProps: null,
    invoiceTransactionsProps: null,
    invoicesProps: null,
    payoutProps: null,
});

let invoices = ref(props.invoicesProps);
let invoiceTag = ref("PENDING");

onMounted(() => {
    invoices.value = props.invoicesProps.filter((invoice) => {
        return invoice.status === invoiceTag.value;
    });
});

function switchInvoiceTag(tag) {
    invoices.value = props.invoicesProps.filter((invoice) => {
        if (tag === "PAID") {
            return invoice.status === "PAID" || invoice.status === "SETTLED";
        } else {
            return invoice.status === tag;
        }
    });
    invoiceTag.value = tag;
}

console.log(props.balanceProps);
let balance = ref(props.balanceProps);
let invoiceTransactions = ref(props.invoiceTransactionsProps);
let payoutTransactions = ref(props.payoutProps);

let transactionTag = ref("INVOICES");

let showInvoices = ref(true);
let showPayouts = ref(false);

function switchTransactionTag(tag) {
    transactionTag.value = tag;

    if (tag === "INVOICES") {
        showPayouts.value = false;
        showInvoices.value = true;
        return;
    }

    showInvoices.value = false;
    showPayouts.value = true;
}

let openPayoutModal = ref(false);
function openModalPayout() {
    openPayoutModal.value = true;
}

function closeModalPayout() {
    openPayoutModal.value = false;
}

let openCardForm = ref(false);
let openEwalletForm = ref(false);
let openOnlineBanking = ref(false);

let bpiCardNumber = ref("");
let bpiAccountName = ref("");
let bpiAmount = ref("");

let gcashCardNumber = ref("");
let gcashAccountName = ref("");
let gcashAmount = ref("");

let popupErrorMessage = ref(null);
function createPayout(channelCode) {
    if (channelCode === "PH_BPI") {
        if (validatePayoutInput(channelCode)) {
            router.post(
                route("worker.payout"),
                {
                    channelCode: null,
                    accountNumber: bpiCardNumber.value,
                    accountName: bpiAccountName.value,
                    amount: bpiAmount.value,
                },
                {
                    onSuccess: () => {
                        location.reload();
                    },
                    onError: (error) => {
                        closeModalPayout();
                        popupErrorMessage.value = error.error;
                        setTimeout(() => {
                            popupErrorMessage.value = null;
                        }, 2000);
                    },
                },
            );
        }
    }
    if (channelCode === "PH_GCASH") {
        if (validatePayoutInput(channelCode)) {
            router.post(
                route("worker.payout"),
                {
                    channelCode,
                    accountNumber: gcashCardNumber.value,
                    accountName: gcashAccountName.value,
                    amount: gcashAmount.value,
                },
                {
                    onSuccess: () => {
                        location.reload();
                    },
                    onError: (error) => {
                        closeModalPayout();
                        popupErrorMessage.value = error.error;
                        setTimeout(() => {
                            popupErrorMessage.value = null;
                        }, 2000);
                    },
                },
            );
        }
    }
}

let errorMessage = ref({
    bpiAmount: null,
    bpiCardNumber: null,

    gcashAmount: null,
    gcashCardNumber: null,
});
function validatePayoutInput(channelCode) {
    if (channelCode === "PH_BPI") {
        errorMessage.value.bpiAmount = null;
        errorMessage.value.bpiCardNumber = null;

        if (
            String(bpiAmount.value).toLowerCase().includes("e") ||
            isNaN(bpiAmount.value)
        ) {
            errorMessage.value.bpiAmount = "BPI Amount must be a number.";
            return false;
        }

        if (bpiAmount.value > balance.value.balance) {
            errorMessage.value.bpiAmount =
                "You do not have enough balance to payout this amount.";
            return false;
        }

        if (bpiAmount.value < 100) {
            errorMessage.value.bpiAmount =
                "Payout amount must be at least ₱100. Please enter a valid amount.";
            return false;
        }
        if (bpiAmount.value > 50000) {
            errorMessage.value.bpiAmount =
                "Payout amount cannot exceed ₱50,000. Please enter a valid amount.";
            return false;
        }

        if (
            String(bpiCardNumber.value).toLowerCase().includes("e") ||
            isNaN(bpiCardNumber.value) ||
            String(bpiCardNumber.value) === ""
        ) {
            errorMessage.value.bpiCardNumber =
                "BPI Card Number must be a number.";
            return false;
        }
    }

    if (channelCode === "PH_GCASH") {
        errorMessage.value.gcashAmount = null;
        errorMessage.value.gcashCardNumber = null;

        if (
            String(gcashAmount.value).toLowerCase().includes("e") ||
            isNaN(gcashAmount.value)
        ) {
            errorMessage.value.gcashAmount = "Gcash Amount must be a number.";
            return false;
        }

        if (gcashAmount.value > balance.value.balance) {
            errorMessage.value.gcashAmount =
                "You do not have enough balance to payout this amount.";
            return false;
        }

        if (gcashAmount.value < 100) {
            errorMessage.value.gcashAmount =
                "Payout amount must be at least ₱100. Please enter a valid amount.";
            return false;
        }
        if (gcashAmount.value > 50000) {
            errorMessage.value.gcashAmount =
                "Payout amount cannot exceed ₱50,000. Please enter a valid amount.";
            return false;
        }

        if (
            String(gcashCardNumber.value).toLowerCase().includes("e") ||
            isNaN(gcashCardNumber.value) ||
            String(gcashCardNumber.value) === ""
        ) {
            errorMessage.value.gcashCardNumber =
                "Gcash Number must be a number.";
            return false;
        }
    }

    return true;
}

const formatCurrency =
    getCurrentInstance().appContext.config.globalProperties.formatCurrency;
</script>
<template>
    <Head title="Profile | iCan Careers" />
    <div class="container mx-auto px-[0.5rem] xl:max-w-7xl">
        <div class="grid gap-0 pt-8 lg:grid-cols-[300px,1fr] lg:gap-10">
            <div>
                <div
                    class="mb-4 flex flex-col items-center justify-start rounded border p-4"
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
                    <p class="font-bol mb-3">{{ user.name }}</p>
                    <Link
                        :href="route('worker.profile')"
                        as="button"
                        class="mb-3 w-full max-w-[500px] rounded-lg border px-4 py-2 font-bold"
                    >
                        View Profile
                    </Link>
                    <div
                        v-if="!$page.props.auth.worker_verified"
                        class="flex flex-col items-center"
                    >
                        <p class="mb-3 text-center text-red-500">
                            Please verify your account to apply for jobs!
                        </p>
                        <Link
                            :href="route('account.verify')"
                            as="button"
                            class="w-full rounded-lg border bg-red-500 py-2 text-white"
                        >
                            Click here to verify!
                        </Link>
                    </div>
                    <div
                        v-if="$page.props.auth.user.authenticated.verified"
                        
                        class="flex items-center gap-1"
                    >
                        <p class="text-sm font-bold text-gray-600">Verified</p>
                        <i class="bi bi-patch-check-fill text-green-400"></i>
                    </div>

                    <div v-if="isPending">
                        <p class="text-yellow-400">{{ isPending }}</p>
                    </div>
                </div>

                <div class="border p-4">
                    <div class="flex justify-between">
                        <p>Inbox</p>
                        <Link :href="route('messages')" class="text-blue-500"
                            >See All</Link
                        >
                    </div>
                </div>
            </div>
            <div>
                <div
                    class="mb-4 grid grid-cols-1 gap-3 rounded lg:grid-cols-[400px,1fr] xl:grid-cols-[600px,1fr]"
                >
                    <div class="col-span-2 rounded border p-3 lg:col-span-1">
                        <div class="mb-4 flex items-center justify-between">
                            <p class="p-1 text-lg font-bold">Invoice Status</p>
                            <Link
                                :href="route('worker.create.invoice')"
                                class="flex items-center gap-2 rounded-lg bg-[#fa8334] px-4 py-2 font-semibold text-white shadow-md transition duration-300 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300"
                                title="Click to Create Invoice"
                            >
                                <i class="bi bi-plus-lg text-xl"></i>
                                <!-- Add Plus Icon -->
                                <span>Create Invoice</span>
                            </Link>
                        </div>
                        <swiper-container
                            class="mb-3 text-[12px]"
                            slides-per-view="auto"
                            :space-between="10"
                        >
                            <swiper-slide class="w-fit">
                                <li
                                    @click="switchInvoiceTag('PENDING')"
                                    :class="[
                                        'cursor-pointer rounded border border-yellow-400 p-1',
                                        {
                                            'bg-yellow-400 text-white':
                                                invoiceTag === 'PENDING',
                                            'text-yellow-400':
                                                invoiceTag != 'PENDING',
                                        },
                                    ]"
                                >
                                    Pending
                                </li></swiper-slide
                            >
                            <swiper-slide class="w-fit">
                                <li
                                    @click="switchInvoiceTag('PAID')"
                                    :class="[
                                        'cursor-pointer rounded border border-green-400 p-1 text-green-400',
                                        {
                                            'bg-green-400 text-white':
                                                invoiceTag === 'PAID',
                                        },
                                    ]"
                                >
                                    Paid
                                </li></swiper-slide
                            >
                            <swiper-slide class="w-fit">
                                <li
                                    @click="switchInvoiceTag('EXPIRED')"
                                    :class="[
                                        'cursor-pointer rounded border border-red-400 p-1 text-red-400',
                                        {
                                            'bg-red-400 text-white':
                                                invoiceTag === 'EXPIRED',
                                        },
                                    ]"
                                >
                                    Expired
                                </li></swiper-slide
                            >
                        </swiper-container>
                        <div class="h-[300px] overflow-auto">
                            <table class="w-full table-fixed">
                                <thead class="bg-slate-300">
                                    <tr>
                                        <th class="font-normal text-slate-500">
                                            External ID
                                        </th>
                                        <th class="font-normal text-slate-500">
                                            Employer
                                        </th>
                                        <th class="font-normal text-slate-500">
                                            Amount
                                        </th>
                                        <th class="font-normal text-slate-500">
                                            Status
                                        </th>
                                        <th class="font-normal text-slate-500">
                                            View
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm">
                                    <tr
                                        v-for="(invoice, index) in invoices"
                                        :key="invoice.id"
                                        class="break-words text-center"
                                    >
                                        <td class="p-1">
                                            {{ invoice.external_id }}
                                        </td>
                                        <td class="p-1">
                                            {{ invoice.employer.email }}
                                        </td>
                                        <td class="p-1">
                                            {{ formatCurrency(invoice.amount) }}
                                        </td>
                                        <td
                                            :class="[
                                                'p-1',
                                                {
                                                    'text-yellow-500':
                                                        invoice.status ===
                                                        'PENDING',
                                                    'text-green-500':
                                                        invoice.status ===
                                                            'PAID' ||
                                                        invoice.status ===
                                                            'SETTLED',
                                                    'text-red-500':
                                                        invoice.status ===
                                                        'EXPIRED',
                                                },
                                            ]"
                                        >
                                            {{ invoice.status }}
                                        </td>
                                        <td class="p-1">
                                            <a
                                                class="rounded bg-blue-500 p-2 text-white"
                                                :href="`/storage/invoices/${invoice.external_id}.pdf`"
                                                target="_blank"
                                            >
                                                <i
                                                    class="bi bi-box-arrow-up-right"
                                                ></i
                                            ></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div
                        class="col-span-2 flex h-[400px] flex-col rounded border p-3 lg:col-span-1"
                    >
                        <p class="font-bold">Balance</p>
                        <div class="mb-2">
                            <div class="mb-1">
                                <h2 class="text-[32px]">
                                    {{ formatCurrency(balance.balance) }}
                                </h2>
                                <div class="flex gap-1 text-[12px]">
                                    <p class="text-slate-600">
                                        {{
                                            formatCurrency(balance.unsettlement)
                                        }}
                                    </p>
                                    <p class="text-yellow-500">
                                        (Waiting for settlement)
                                    </p>
                                </div>
                            </div>
                            <div class="flex justify-start gap-3">
                                <div
                                    @click="openModalPayout"
                                    class="flex cursor-pointer flex-col items-center justify-center"
                                >
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded bg-blue-500 p-2"
                                    >
                                        <i
                                            class="bi bi-send-arrow-up-fill text-white"
                                        ></i>
                                    </div>
                                    <p class="text-[12px]">Payout</p>
                                </div>
                                <div
                                    class="flex flex-col items-center justify-center"
                                >
                                    <div
                                        class="flex h-10 w-10 cursor-pointer items-center justify-center rounded bg-blue-500 p-2"
                                    >
                                        <i
                                            class="bi bi-info-circle-fill text-white"
                                        ></i>
                                    </div>
                                    <p class="text-[12px]">Info</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <p>Transactions</p>
                            <swiper-container
                                class="mb-3 text-[12px]"
                                slides-per-view="auto"
                                :space-between="10"
                            >
                                <swiper-slide class="w-fit">
                                    <li
                                        @click="
                                            switchTransactionTag('INVOICES')
                                        "
                                        :class="[
                                            'cursor-pointer rounded border border-blue-500 p-1',
                                            {
                                                'bg-blue-500 text-white':
                                                    transactionTag ===
                                                    'INVOICES',
                                                'text-blue-500':
                                                    transactionTag !=
                                                    'INVOICES',
                                            },
                                        ]"
                                    >
                                        Invoices
                                    </li></swiper-slide
                                >
                                <swiper-slide class="w-fit">
                                    <li
                                        @click="switchTransactionTag('PAYOUT')"
                                        :class="[
                                            'cursor-pointer rounded border border-blue-500 p-1 text-blue-500',
                                            {
                                                'bg-blue-500 text-white':
                                                    transactionTag === 'PAYOUT',
                                            },
                                        ]"
                                    >
                                        Payout
                                    </li></swiper-slide
                                >
                            </swiper-container>
                        </div>

                        <div class="flex-1 overflow-y-scroll">
                            <div
                                v-if="showInvoices"
                                v-for="transaction in invoiceTransactions"
                                class="mb-3 rounded p-4 shadow"
                            >
                                <div class="flex gap-2">
                                    <div class="h-10 w-10">
                                        <img
                                            src="/assets/profile_placeholder.jpg"
                                            alt=""
                                            class="h-full w-full rounded-full"
                                        />
                                    </div>
                                    <div>
                                        <p class="text-sm">
                                            {{ `${transaction.employer.name}` }}
                                        </p>
                                        <div class="flex gap-1 text-[12px]">
                                            <p>
                                                {{
                                                    `PAID ${formatCurrency(transaction.amount)} `
                                                }}
                                            </p>
                                            <p
                                                :class="{
                                                    'text-yellow-500':
                                                        transaction.status ===
                                                        'PAID',
                                                    'text-green-500':
                                                        transaction.status ===
                                                        'SETTLED',
                                                }"
                                            >
                                                {{ `(${transaction.status})` }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                v-if="showPayouts"
                                v-for="payout in payoutTransactions"
                                class="mb-3 rounded p-4 shadow"
                            >
                                <!-- {{ payout }} -->
                                <div class="flex gap-2">
                                    <div class="flex-1">
                                        <p class="text-sm">
                                            {{ formatCurrency(payout.amount) }}
                                        </p>
                                        <div
                                            class="flex justify-between text-[12px]"
                                        >
                                            <p>{{ payout.reference_id }}</p>
                                            <p
                                                :class="{
                                                    'text-yellow-500':
                                                        payout.status ===
                                                        'PAID',
                                                    'text-green-500':
                                                        payout.status ===
                                                        'SETTLED',
                                                }"
                                            >
                                                {{
                                                    dayjs(
                                                        payout.created_at,
                                                    ).format("MM-DD-YYYY")
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2"
                >
                    <div class="col-span-1 rounded border p-2">
                        <p
                            class="mb-3 border-b-[1px] border-gray-100 p-2 text-[14px]"
                        >
                            Saved Jobs
                        </p>

                        <div class="h-[350px] overflow-y-auto">
                            <table border="1" class="w-full border-collapse">
                                <thead>
                                    <tr class="text-gray-500">
                                        <th class="px-7 py-2 font-normal">
                                            Logo
                                        </th>
                                        <th class="px-7 py-2 font-normal">
                                            Job Title
                                        </th>
                                        <th class="px-7 py-2 font-normal">
                                            Details
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="job in savedJobsProps"
                                        class="text-center"
                                    >
                                        <td class="p-2">
                                            <img
                                                class="mx-auto w-12 object-cover"
                                                :src="
                                                    job.employer
                                                        .employer_profile
                                                        .business_information
                                                        ? job.employer
                                                              .employer_profile
                                                              .business_information
                                                              .business_logo
                                                        : '/assets/images.png'
                                                "
                                                alt=""
                                            />
                                        </td>
                                        <td class="p-2">
                                            Lorem ipsum dolor lorem
                                        </td>
                                        <td class="p-2">
                                            <Link
                                                :href="
                                                    route(
                                                        'jobsearch.show',
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
                                    <!-- <tr class="text-center">
                                    <td class="p-2">
                                        <img
                                            class="mx-auto w-12"
                                            src="/assets/images.png"
                                            alt=""
                                            />
                                        </td>
                                        <td class="p-2">
                                            Lorem ipsum dolor sit, amet consectetur
                                            adipisicing elit. Distinctio quas
                                        </td>
                                        <td class="p-2">Details</td>
                                    </tr> -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-span-1 border p-2">
                        <p
                            class="mb-3 border-b-[1px] border-gray-100 p-2 text-[14px]"
                        >
                            Applied Jobs
                        </p>

                        <div class="h-[350px] overflow-y-auto">
                            <table border="1" class="w-full border-collapse">
                                <thead>
                                    <tr class="text-gray-500">
                                        <th class="px-7 py-2 font-normal">
                                            Logo
                                        </th>
                                        <th class="px-7 py-2 font-normal">
                                            Job Title
                                        </th>
                                        <th class="px-7 py-2 font-normal">
                                            Status
                                        </th>
                                        <th class="px-7 py-2 font-normal">
                                            Details
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="job in jobsAppliedProps"
                                        class="text-center"
                                    >
                                        <td class="p-2">
                                            <img
                                                class="mx-auto w-12 object-cover"
                                                :src="
                                                    job.employer
                                                        .employer_profile
                                                        .business_information
                                                        ? job.employer
                                                              .employer_profile
                                                              .business_information
                                                              .business_logo
                                                        : '/assets/images.png'
                                                "
                                                alt=""
                                            />
                                        </td>
                                        <td class="p-2">
                                            {{ job.job_title }}
                                        </td>
                                        <td class="p-2">
                                            <p
                                                :class="[
                                                    'p-1 text-sm font-bold',
                                                    {
                                                        'rounded-full bg-yellow-400 text-white':
                                                            job.pivot.status ===
                                                            'Pending',
                                                        'under-review rounded-full bg-slate-400 text-white':
                                                            job.pivot.status ===
                                                            'Under Review',
                                                        'rounded-full bg-slate-400 text-white':
                                                            job.pivot.status ===
                                                            'Interview Scheduled',
                                                        'rounded-full bg-green-400 text-white':
                                                            job.pivot.status ===
                                                            'Accepted',
                                                        'rounded-full bg-red-400 text-white':
                                                            job.pivot.status ===
                                                            'Rejected',
                                                    },
                                                ]"
                                            >
                                                {{ job.pivot.status }}
                                            </p>
                                        </td>
                                        <td class="p-2">
                                            <Link
                                                :href="
                                                    route(
                                                        'jobsearch.show',
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
                                    <!-- <tr class="text-center">
                                    <td class="p-2">
                                        <img
                                            class="mx-auto w-12"
                                            src="/assets/images.png"
                                            alt=""
                                            />
                                        </td>
                                        <td class="p-2">
                                            Lorem ipsum dolor sit, amet consectetur
                                            adipisicing elit. Distinctio quas
                                        </td>
                                        <td class="p-2">Details</td>
                                    </tr> -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <ReusableModal @closeModal="closeModalPayout" v-if="openPayoutModal">
        <div class="h-[500px] w-[350px] overflow-auto rounded bg-white p-2">
            <div>
                <h2 class="mb-5 text-xl font-medium">Choose Payout option</h2>
                <div>
                    <!-- <div class="cursor-pointer">
                        <div
                            @click="openCardForm = !openCardForm"
                            :class="[
                                'flex items-center justify-between border p-3',
                                {
                                    'border-b-0': openCardForm,
                                },
                            ]"
                        >
                            <div class="flex items-center gap-2">
                                <i class="bi bi-credit-card-2-front"></i>
                                <p>Credit / Debit Card</p>
                            </div>
                            <i class="bi bi-chevron-down"></i>
                        </div>

                        <div
                            :class="[
                                'card-form overflow-hidden transition-all',
                                {
                                    'h-[400px] border border-t-0': openCardForm,
                                    'h-[0px]': !openCardForm,
                                },
                            ]"
                        >
                            <div class="p-4">
                                <div class="mb-3 flex flex-col">
                                    <label for="" class="mb-1 text-sm"
                                        >Card Number</label
                                    >
                                    <input
                                        type="text"
                                        class="rounded border p-2"
                                    />
                                </div>
                                <div class="mb-3 flex justify-between">
                                    <div class="flex flex-col">
                                        <label for="" class="mb-1 text-sm"
                                            >Valid Thru</label
                                        >
                                        <input
                                            type="text"
                                            class="w-36 rounded border p-2"
                                        />
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="" class="mb-1 text-sm"
                                            >CVN</label
                                        >
                                        <input
                                            type="text"
                                            class="w-36 rounded border p-2"
                                        />
                                    </div>
                                </div>
                                <div class="mb-3 flex justify-between">
                                    <div class="flex flex-col">
                                        <label for="" class="mb-1 text-sm"
                                            >First Name</label
                                        >
                                        <input
                                            type="text"
                                            class="w-36 rounded border p-2"
                                        />
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="" class="mb-1 text-sm"
                                            >Last Name</label
                                        >
                                        <input
                                            type="text"
                                            class="w-36 rounded border p-2"
                                        />
                                    </div>
                                </div>
                                <div class="mb-3 flex flex-col">
                                    <label for="" class="mb-1 text-sm"
                                        >Email Address</label
                                    >
                                    <input
                                        type="text"
                                        class="rounded border p-2"
                                    />
                                </div>
                                <div class="flex justify-center">
                                    <button
                                        class="rounded bg-slate-400 p-2 text-white"
                                    >
                                        Pay Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <div class="cursor-pointer">
                        <div
                            @click="openEwalletForm = !openEwalletForm"
                            :class="[
                                'flex items-center justify-between border p-3',
                                {
                                    'border-b-0': openEwalletForm,
                                },
                            ]"
                        >
                            <div class="flex items-center gap-2">
                                <i class="bi bi-credit-card-2-front"></i>
                                <div class="flex items-center">
                                    <p>E-Wallet</p>
                                    <div class="h-10 w-14">
                                        <img
                                            src="/assets/GCash.png"
                                            alt=""
                                            class="h-full w-full"
                                        />
                                    </div>
                                </div>
                            </div>
                            <i class="bi bi-chevron-down"></i>
                        </div>

                        <div
                            :class="[
                                'card-form overflow-auto transition-all',
                                {
                                    'h-[350px] border border-t-0':
                                        openEwalletForm,
                                    'h-[0px]': !openEwalletForm,
                                },
                            ]"
                        >
                            <div class="p-4">
                                <div class="mb-3 flex flex-col">
                                    <label for="" class="mb-1 text-sm"
                                        >Amount</label
                                    >
                                    <input
                                        type="text"
                                        class="rounded border p-2"
                                        v-model="gcashAmount"
                                        :max="balance.balance"
                                        min="0"
                                    />
                                    <InputFlashMessage
                                        class="text-sm"
                                        :message="errorMessage.gcashAmount"
                                        type="error"
                                    ></InputFlashMessage>
                                    <p class="text-sm">
                                        Available Balance:
                                        {{ formatCurrency(balance.balance) }}
                                    </p>
                                </div>
                                <div class="mb-3 flex flex-col">
                                    <label for="" class="mb-1 text-sm"
                                        >Account Name</label
                                    >
                                    <input
                                        type="text"
                                        v-model="gcashAccountName"
                                        class="rounded border p-2"
                                    />
                                </div>
                                <div class="mb-3 flex flex-col">
                                    <label for="" class="mb-1 text-sm"
                                        >Gcash Number</label
                                    >
                                    <input
                                        type="text"
                                        v-model="gcashCardNumber"
                                        class="rounded border p-2"
                                    />
                                    <InputFlashMessage
                                        class="text-sm"
                                        :message="errorMessage.gcashCardNumber"
                                        type="error"
                                    ></InputFlashMessage>
                                </div>

                                <div class="flex justify-center">
                                    <button
                                        @click="createPayout('PH_GCASH')"
                                        class="rounded bg-slate-400 p-2 text-white"
                                    >
                                        Pay Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cursor-pointer">
                        <div
                            @click="openOnlineBanking = !openOnlineBanking"
                            :class="[
                                'flex items-center justify-between border p-3',
                                {
                                    'border-b-0': openOnlineBanking,
                                },
                            ]"
                        >
                            <div class="flex items-center gap-2">
                                <i class="bi bi-credit-card-2-front"></i>
                                <p>Online Banking</p>
                                <div class="h-10 w-10">
                                    <img
                                        src="/assets/BPI.jpg"
                                        alt=""
                                        class="h-full w-full"
                                    />
                                </div>
                            </div>
                            <i class="bi bi-chevron-down"></i>
                        </div>

                        <div
                            :class="[
                                'card-form overflow-auto transition-all',
                                {
                                    'h-[350px] border border-t-0':
                                        openOnlineBanking,
                                    'h-[0px]': !openOnlineBanking,
                                },
                            ]"
                        >
                            <div class="p-4">
                                <div class="mb-3 flex flex-col">
                                    <label for="" class="mb-1 text-sm"
                                        >Amount</label
                                    >
                                    <input
                                        type="text"
                                        v-model="bpiAmount"
                                        class="rounded border p-2"
                                    />
                                    <InputFlashMessage
                                        class="text-sm"
                                        :message="errorMessage.bpiAmount"
                                        type="error"
                                    ></InputFlashMessage>
                                    <p class="text-sm">
                                        Available Balance:
                                        {{ formatCurrency(balance.balance) }}
                                    </p>
                                </div>
                                <div class="mb-3 flex flex-col">
                                    <label for="" class="mb-1 text-sm"
                                        >Acount Name</label
                                    >
                                    <input
                                        type="text"
                                        v-model="bpiAccountName"
                                        class="rounded border p-2"
                                    />
                                </div>
                                <div class="mb-3 flex flex-col">
                                    <label for="" class="mb-1 text-sm"
                                        >Card Number</label
                                    >
                                    <input
                                        type="text"
                                        v-model="bpiCardNumber"
                                        class="rounded border p-2"
                                    />
                                    <InputFlashMessage
                                        class="text-sm"
                                        :message="errorMessage.bpiCardNumber"
                                        type="error"
                                    ></InputFlashMessage>
                                </div>

                                <div class="flex justify-center">
                                    <button
                                        @click="createPayout('PH_BPI')"
                                        class="rounded bg-slate-400 p-2 text-white"
                                    >
                                        Pay Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ReusableModal>

    <SuccessfulMessage
        :messageShow="popupErrorMessage"
        :messageProp="popupErrorMessage"
        type="Error"
    ></SuccessfulMessage>
</template>

<style scoped>
/* ::-webkit-scrollbar {
    @apply w-1;
}
::-webkit-scrollbar-thumb {
    @apply bg-green-200;
} */

/* @keyframes animation-down {
    0% {
        height: 0;
    }
    100% {
        height: 300px;
    }
}

.card-form {
    animation: animation-down 0.2s ease-in;
} */
</style>
