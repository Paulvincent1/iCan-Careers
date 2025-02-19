<script setup>
let jobType = defineModel("jobType", { type: null, required: true });
let workHourPerDay = defineModel("workHourPerDay", {
    type: null,
    required: true,
});
let hourPay = defineModel("hourPay", { type: null, required: true });
let monthPay = defineModel("monthPay", { type: null, required: true });
</script>
<template>
    <p>
        Looking for

        <select
            v-model="jobType"
            name=""
            id=""
            class="border p-3 outline-blue-400"
            required
        >
            <option value="Part Time">Part Time</option>
            <option value="Full Time">Full Time</option>
        </select>
        work (
        <input
            v-model="workHourPerDay"
            type="number"
            min="1"
            class="w-20 border p-3 text-center"
            required
        />
        hours / day) at ₱
        <input
            v-model="hourPay"
            type="number"
            min="1"
            class="w-28 border p-3 text-center"
            required
        />
        / hour, ₱
        <input
            v-model="monthPay"
            type="number"
            min="1"
            class="w-28 border p-3 text-center"
            required
        />
        / month
    </p>
</template>
