<script setup>
import { useForm } from "@inertiajs/vue3";
import AuthForm from "../Components/AuthForm.vue";
import AuthInput from "../Components/AuthInput.vue";
import InputFlashMessage from "../Components/InputFlashMessage.vue";

const props = defineProps({
    token: null,
    email: null,
    status: null,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: null,
    password_confirmation: null,
});
const submit = () => {
    form.post(route("password.update"));
};
</script>
<template>
    <div class="container mx-auto grid justify-center px-[0.5rem] xl:max-w-7xl">
        <AuthForm title="Reset Password">
            <p class="mb-5">Enter your new password</p>
            <InputFlashMessage :message="status" type="success" />
            <AuthInput
                name="EMAIL"
                type="email"
                :message="form.errors.email"
                v-model="form.email"
            />
            <AuthInput
                name="PASSWORD"
                type="password"
                :message="form.errors.password"
                v-model="form.password"
            />
            <AuthInput
                name="PASSWORD CONFIRMATION"
                type="password"
                :message="form.errors.password_confirmation"
                v-model="form.password_confirmation"
                class="mb-4"
            />
            <button
                class="rounded bg-slate-700 py-3 text-white"
                @click="submit"
            >
                Reset Password
            </button>
        </AuthForm>
    </div>
</template>
