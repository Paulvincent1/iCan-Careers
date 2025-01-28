<script setup>
import InputFlashMessage from "./InputFlashMessage.vue";

let model = defineModel({
    type: null,
    required: true,
});

let props = defineProps({
    error: null,
    openToAll: null,
});
</script>
<template>
    <div class="relative">
        <select
            v-model="model"
            name=""
            id=""
            class="w-full appearance-none border px-3 py-2 outline-blue-400"
        >
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
        <i
            class="bi bi-chevron-down absolute right-2 top-[50%] translate-y-[-50%] text-[16px] font-bold text-purple-500"
        ></i>
        <InputFlashMessage type="error" :message="error" />
    </div>
</template>
