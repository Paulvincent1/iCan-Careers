<script setup>
import { Link, router, useForm, usePage } from "@inertiajs/vue3";
import dayjs from "dayjs";
import { uniqueId } from "lodash";
import { getCurrentInstance, ref, useTemplateRef } from "vue";
import InputFlashMessage from "../Components/InputFlashMessage.vue";

let props = defineProps({
    employersProps: null,
});

let form = useForm({
    dueDate: null,
    billTo: null,
    description: null,
    items: [
        {
            id: uniqueId(),
            description: null,
            hours: 0,
            rate: 0,
            amount: 0,
        },
    ],
    totalAmount: null,
});

// let page = usePage();

// console.log(page.props.csrf_token);

// let items = ref([
//     {
//         id: uniqueId(),
//         description: "",
//         hours: "",
//         rate: "",
//     },
// ]);

function addRow() {
    form.items.push({
        id: uniqueId(),
        description: null,
        hours: 0,
        rate: 0,
        amount: 0,
    });
}

function removeRow(index) {
    form.items.splice(index, 1);
}

let totalAmount = ref(0);
function calculateTotalAmount(item) {
    item.amount = item.hours * item.rate;
    totalAmount.value = 0;
    form.items.forEach((e) => {
        totalAmount.value += e.hours * e.rate;
    });

    form.totalAmount = totalAmount.value;
}

let page = usePage();

let errorMessage = ref({
    dueDate: null,
    billTo: null,
    description: null,
    items: null,
});

function previewPdf() {
    errorMessage.value.dueDate = null;
    errorMessage.value.billTo = null;
    errorMessage.value.description = null;
    errorMessage.value.items = null;

    if (form.dueDate === null) {
        errorMessage.value.dueDate = "Please complete this field";
        return;
    }
    if (form.billTo === null) {
        errorMessage.value.billTo = "Please complete this field";
        return;
    }
    if (form.description === null) {
        errorMessage.value.description = "Please complete this field";
        return;
    }
    if (
        form.items.description === null ||
        form.items.hours === null ||
        form.items.hours === 0 ||
        form.items.rate === null ||
        form.items.rate === 0
    ) {
        errorMessage.value.items = "Please complete this field";
        return;
    }

    createForm();
}

function createForm() {
    const formElement = document.createElement("form");

    formElement.target = "_blank";
    formElement.action = route("worker.preview.invoice");
    formElement.method = "POST";

    formElement.classList.add("hidden");

    const tokenInput = document.createElement("input");
    tokenInput.name = "_token";
    tokenInput.type = "hidden";
    tokenInput.value = page.props.csrf_token;
    formElement.appendChild(tokenInput);

    const dueInput = document.createElement("input");
    const descriptionInput = document.createElement("input");
    const billToInput = document.createElement("input");
    const totalAmountInput = document.createElement("input");

    dueInput.name = "dueDate";
    descriptionInput.name = "description";
    billToInput.name = "billTo";
    totalAmountInput.name = "totalAmount";

    dueInput.type = "text";
    descriptionInput.type = "text";
    billToInput.type = "text";
    totalAmountInput.type = "text";

    dueInput.value = form.dueDate;
    descriptionInput.value = form.description;
    billToInput.value = form.billTo;
    totalAmountInput.value = form.totalAmount;

    formElement.appendChild(dueInput);
    formElement.appendChild(descriptionInput);
    formElement.appendChild(billToInput);
    formElement.appendChild(totalAmountInput);

    const inputItems = document.createElement("input");
    inputItems.name = "items";
    inputItems.type = "text";

    inputItems.value = JSON.stringify(form.items);

    formElement.appendChild(inputItems);
    document.body.appendChild(formElement);

    formElement.submit();
}

function sendInvoice() {}

const { appContext } = getCurrentInstance();
const $formatCurrency = appContext.config.globalProperties.$formatCurrency;

console.log($formatCurrency(1000));
</script>
<template>
    <div class="mx-auto px-[0.5rem] pt-3 xl:max-w-7xl">
        <div class="mx-auto sm:w-[90%]">
            <div class="my-3">
                <h2 class="text-[26px]">Create Invoice</h2>
            </div>
            <div>
                <h3 class="mb-1 text-[20px]">Billing Information</h3>

                <div class="mb-3 flex gap-3">
                    <div class="flex flex-col">
                        <label for="">Invoice date</label>
                        <input
                            type="text"
                            class="border"
                            disabled
                            :value="dayjs().format('YYYY/MM/DD')"
                        />
                    </div>
                    <div class="flex flex-col">
                        <label for="">Due Date</label>
                        <input
                            v-model="form.dueDate"
                            type="date"
                            class="border"
                            :min="dayjs().format('YYYY-MM-DD')"
                        />
                        <InputFlashMessage
                            :message="errorMessage.dueDate"
                            type="error"
                        ></InputFlashMessage>
                    </div>
                </div>
                <div>
                    <div class="mb-3 flex w-40 flex-col">
                        <label for="">Bill to</label>
                        <select
                            v-model="form.billTo"
                            name=""
                            id=""
                            class="rounded border"
                        >
                            <option value=""></option>
                            <option
                                :value="employer.email"
                                v-for="(employer, index) in employersProps"
                                :key="index"
                            >
                                {{ employer.email }}
                            </option>
                        </select>
                        <InputFlashMessage
                            :message="errorMessage.billTo"
                            type="error"
                        ></InputFlashMessage>
                    </div>
                </div>
            </div>
            <div>
                <h3 class="mb-2 text-[20px]">Service</h3>

                <div class="mb-7">
                    <table class="w-full border-b">
                        <thead class="bg-[#024570]">
                            <tr class="">
                                <th class="w-[40px] rounded-tl-lg p-3">
                                    <p class=""></p>
                                </th>
                                <th
                                    class="w-[30%] p-3 text-start font-sans text-white"
                                >
                                    Description
                                </th>
                                <th
                                    class="w-[15%] p-3 text-start font-sans text-white"
                                >
                                    Hours
                                </th>
                                <th
                                    class="w-[15%] p-3 text-start font-sans text-white"
                                >
                                    Rate (PHP)
                                </th>
                                <th
                                    class="rounded-tr-lg p-3 text-end font-sans text-white"
                                >
                                    Total
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(item, index) in form.items"
                                :key="item.id"
                            >
                                <td class="p-2">
                                    <i
                                        @click="removeRow(index)"
                                        class="bi bi-x-circle cursor-pointer text-sm"
                                    ></i>
                                </td>
                                <td class="p-2">
                                    <input
                                        v-model="item.description"
                                        type="text"
                                        class="w-full border p-2"
                                        placeholder="Enter a detailed decription"
                                    />
                                </td>
                                <td class="p-2">
                                    <input
                                        v-model="item.hours"
                                        @input="calculateTotalAmount(item)"
                                        type="number"
                                        class="w-full border p-2"
                                    />
                                </td>
                                <td class="p-2">
                                    <input
                                        v-model="item.rate"
                                        @input="calculateTotalAmount(item)"
                                        type="number"
                                        class="w-full border p-2"
                                        placeholder="0.00"
                                    />
                                </td>
                                <td class="p-2">
                                    <p
                                        :id="`amount-${item.id}`"
                                        class="p-2 text-end"
                                    >
                                        {{
                                            $formatCurrency(
                                                item.hours * item.rate,
                                            )
                                        }}
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <InputFlashMessage
                        :message="errorMessage.items"
                        type="error"
                    ></InputFlashMessage>
                    <div class="mb-3 border-b p-2">
                        <div
                            @click="addRow"
                            class="flex cursor-pointer items-center gap-2 text-blue-500"
                        >
                            <i class="bi bi-plus-circle text-sm"></i>
                            <p>Add another item</p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <div class="flex-1">
                            <p>Description</p>
                            <textarea
                                v-model="form.description"
                                class="w-full rounded bg-slate-100 p-5 pb-32"
                                name=""
                                id=""
                                placeholder="Note or description to recipient"
                            ></textarea>
                            <InputFlashMessage
                                :message="errorMessage.description"
                                type="error"
                            ></InputFlashMessage>
                        </div>
                        <div class="md:basis-[300px]">
                            <h3 class="text-end text-[20px]">Total Amount:</h3>
                            <p class="text-end text-[20px]">
                                {{ $formatCurrency(totalAmount) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3 flex justify-end gap-3">
                <button
                    @click="previewPdf"
                    class="cursor-pointer rounded-full border border-[#024570] px-4 py-1 text-[#024570]"
                >
                    Preview PDF
                </button>
                <button class="rounded-full bg-green-500 px-4 py-1 text-white">
                    Send Invoice
                </button>
            </div>
        </div>
    </div>
</template>
