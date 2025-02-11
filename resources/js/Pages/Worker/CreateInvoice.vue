<script setup>
import dayjs from "dayjs";
import { uniqueId } from "lodash";
import { ref, useTemplateRef } from "vue";

let props = defineProps({
    employersProps: null,
});

let items = ref([
    {
        id: uniqueId(),
        description: "",
        hours: "",
        rate: "",
    },
]);

function addRow() {
    items.value.push({
        id: uniqueId(),
        description: "",
        hours: 0,
        rate: 0,
    });
}

function removeRow(index) {
    items.value.splice(index, 1);
}

let totalAmount = ref(0);
function calculateTotalAmount() {
    totalAmount.value = 0;
    items.value.forEach((e) => {
        totalAmount.value += e.hours * e.rate;
    });
    console.log(totalAmount.value);
}
</script>
<template>
    <div class="mx-auto px-[0.5rem] pt-3 xl:max-w-7xl">
        <div class="mx-auto w-[90%]">
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
                            type="date"
                            class="border"
                            :min="dayjs().format('YYYY-MM-DD')"
                        />
                    </div>
                </div>
                <div>
                    <div class="mb-3 flex w-40 flex-col">
                        <label for="">Bill to</label>
                        <select name="" id="" class="rounded border">
                            <option value=""></option>
                            <option
                                value=""
                                v-for="(employer, index) in employersProps"
                                :key="index"
                            >
                                {{ employer.email }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <div>
                <h3 class="mb-2 text-[20px]">Service</h3>

                <div class="mb-7">
                    <table class="w-full border-b">
                        <thead class="bg-green-500">
                            <tr class="text-white">
                                <th class="w-[40px] rounded-tl-lg p-2">
                                    <p class=""></p>
                                </th>
                                <th class="w-[30%] p-2 text-start">
                                    Description
                                </th>
                                <th class="w-[15%] p-2 text-start">Hours</th>
                                <th class="w-[15%] p-2 text-start">Rate</th>
                                <th class="rounded-tr-lg p-2 text-end">
                                    Amount
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in items" :key="item.id">
                                <td class="p-2">
                                    <i
                                        @click="removeRow(index)"
                                        class="bi bi-x-circle text-sm"
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
                                        @input="calculateTotalAmount"
                                        type="number"
                                        class="w-full border p-2"
                                    />
                                </td>
                                <td class="p-2">
                                    <input
                                        v-model="item.rate"
                                        @input="calculateTotalAmount"
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
                                        {{ item.hours * item.rate }}
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
                                class="w-full rounded bg-slate-100 p-5 pb-32"
                                name=""
                                id=""
                                placeholder="Note or description to recipient"
                            ></textarea>
                        </div>
                        <div class="basis-[300px]">
                            <h3 class="text-end text-[20px]">Total Amount:</h3>
                            <p class="text-end text-[20px]">
                                PHP {{ totalAmount }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a
                    class="rounded-full border border-blue-500 px-4 py-1 text-blue-500"
                >
                    Preview PDF
                </a>
                <button class="rounded-full bg-green-500 px-4 py-1 text-white">
                    Send Invoice
                </button>
            </div>
        </div>
    </div>
</template>
