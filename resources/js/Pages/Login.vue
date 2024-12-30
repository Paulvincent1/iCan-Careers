<script setup>
import { useForm } from "@inertiajs/vue3";

let form = useForm({
    email: null,
    password: null,
});

const submit = () => {
    form.post(route("login.post"));
};
</script>

<template>
    <div class="container mx-auto px-[0.5rem] md:max-w-7xl">
        <small v-if="form.errors.email"></small>
        <form @submit.prevent>
            <div>
                <label for="">Email</label>
                <input type="text" class="border" v-model="form.email" />
            </div>
            <div>
                <label for="">Password</label>
                <input type="text" class="border" v-model="form.password" />
            </div>
            <button
                class="rounded-xl bg-blue-600 p-4 text-white"
                @click="submit"
            >
                Login
            </button>
        </form>
    </div>
</template>
