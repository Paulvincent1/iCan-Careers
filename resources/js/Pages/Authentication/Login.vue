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
    <Head title="Login | iCan Careers" />
    <div
        class="container mx-auto grid h-[calc(100vh-4.625rem)] items-start justify-items-center px-[0.5rem] md:max-w-7xl"
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
                class="mb-2 rounded-xl bg-[#fa8334] p-4 text-white"
                @click="submit"
            >
                Login
            </button>
            <a
                :href="route('auth.google')"
                class="mb-3 flex w-full items-center justify-center gap-2 rounded-lg border border-slate-200 bg-gray-800 px-4 py-3 text-slate-700 transition duration-150 hover:border-slate-400 hover:text-slate-900 hover:shadow dark:border-slate-700 dark:text-slate-200 dark:hover:border-slate-500 dark:hover:text-slate-300"
            >
                <img
                    class="h-6 w-6"
                    src="https://www.svgrepo.com/show/475656/google-color.svg"
                    loading="lazy"
                    alt="google logo"
                />
                <span>Login with Google</span>
            </a>

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
