<script setup>
import InputFlashMessage from "./InputFlashMessage.vue";

let model = defineModel({
    type: null,
    required: true,
});

let props = defineProps({
    error: String,
});
</script>

<template>
    <div class="relative">
        <select
            v-model="model"
            class="w-[200px] border px-3 py-2 outline-orange-200"
            :class="{ 'border-red-500': error }"
        >
            <option disabled value="">Select your gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
            <option value="Prefer not to say">Prefer not to say</option>
        </select>

        <InputFlashMessage type="error" :message="error" />
    </div>
</template>