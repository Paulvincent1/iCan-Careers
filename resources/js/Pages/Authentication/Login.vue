<script setup>
import { Link, useForm } from "@inertiajs/vue3";
import AuthInput from "../Components/AuthInput.vue";
import Quotes from "../Components/Quotes.vue";
import AuthForm from "../Components/AuthForm.vue";
import InputFlashMessage from "../Components/InputFlashMessage.vue";
import { reactive } from "vue";

defineProps({
    status: null,
});

let form = useForm({
    email: null,
    password: null,
});

const submit = () => {
    form.post(route("login.post"));
};
</script>

<template>
    <div
        class="container mx-auto grid h-[calc(100vh-4.625rem)] items-start justify-center px-[0.5rem] md:max-w-7xl"
    >
        <AuthForm title="Log In">
            <div class="mb-8">
                <p class="mb-[80px]">
                    <Quotes />
                </p>
            </div>
            <InputFlashMessage class="mb-3" type="success" :message="status" />
            <AuthInput
                name="EMAIL"
                :message="form.errors.email"
                type="email"
                v-model="form.email"
                class="mb-3"
            />
            <AuthInput
                name="PASSWORD"
                :message="form.errors.password"
                type="password"
                v-model="form.password"
                class="mb-3"
            />
            <Link class="mb-3 text-blue-500" :href="route('password.request')"
                >Forgot Password</Link
            >

            <button
                class="mb-2 rounded-xl bg-blue-600 p-4 text-white"
                @click="submit"
            >
                Login
            </button>

            <div class="flex items-center justify-center gap-2">
                <div class="h-[1px] w-[40%] border bg-black"></div>
                <div>or</div>
                <div class="h-[1px] w-[40%] border bg-black"></div>
            </div>

            <Link :href="route('register.create')" class="text-center"
                >Sign up</Link
            >
        </AuthForm>
    </div>
</template>
