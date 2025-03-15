<script setup>
import InputFlashMessage from "./InputFlashMessage.vue";

let model = defineModel({
    type: null,
    required: true,
});

let props = defineProps({
    error: String, // Ensure error is passed down
    openToAll: Boolean,
});
</script>

<template>
    <div class="relative">
        <select
            v-model="model"
            class="w-[200px] border px-3 py-2 outline-orange-200"
            :class="{ 'border-red-500': error }"
        >
            <option disabled value="">Select your highest education</option>
            <option value="Primary School Completed">
                Primary School Completed
            </option>
            <option value="Secondary School (High School) Diploma">
                Secondary School (High School) Diploma
            </option>
            <option value="Associate's Degree">Associate's Degree</option>
            <option value="Bachelor's Degree">Bachelor's Degree</option>
            <option value="Master's Degree">Master's Degree</option>
            <option value="Doctoral or PhD Degree">
                Doctoral or PhD Degree
            </option>
            <option v-if="openToAll" value="Open to all">
                Open to All (Any Educational Attainment)
            </option>
        </select>

        <InputFlashMessage type="error" :message="error" />
    </div>
</template>
