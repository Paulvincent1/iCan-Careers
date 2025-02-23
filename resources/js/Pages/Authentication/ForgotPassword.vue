<script setup>
import { useForm } from "@inertiajs/vue3";
import AuthForm from "../Components/AuthForm.vue";
import AuthInput from "../Components/AuthInput.vue";
import InputFlashMessage from "../Components/InputFlashMessage.vue";

defineProps({
    status: null,
});
let form = useForm({
    email: null,
});

const submit = () => {
    form.post(route("password.email"));
};
</script>
<template>
    <Head title="Forgot Password | iCan Careers" />
    <div
        class="container mx-auto grid h-[100vh-64px] justify-items-center px-[05.rem] xl:max-w-7xl"
    >
        <AuthForm title="Forgot Password">
            <p class="mb-5">
                Enter your email to reset it and regain access to your account
            </p>
            <InputFlashMessage class="mb-3" type="success" :message="status" />
            <AuthInput
                class="mb-3"
                name="Enter your email"
                type="email"
                v-model="form.email"
                :message="form.errors.email"
            ></AuthInput>
            <button
                @click="submit"
                class="rounded bg-[#fa8334] px-2 py-3 font-semibold text-white"
            >
                Send Verification Code
            </button>
        </AuthForm>
    </div>
</template>
