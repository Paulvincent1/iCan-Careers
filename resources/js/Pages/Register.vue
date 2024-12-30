<script setup>
import { useForm } from "@inertiajs/vue3";

let form = useForm({
    name: null,
    email: null,
    password: null,
    password_confirmation: null,
});

const submit = () => {
    form.post(route("register.post"));
};
</script>

<template>
    <div class="container mx-auto px-[0.5rem] md:max-w-7xl">
        <small v-if="form.errors.email" class="text-red-500">{{
            form.errors.email
        }}</small>
        <form
            @submit.prevent
            action="
        "
        >
            <div>
                <label for="">Name</label>
                <input type="text" class="border" v-model="form.name" />
            </div>
            <div>
                <label for="">Email</label>
                <input type="text" class="border" v-model="form.email" />
            </div>
            <div>
                <label for="">Password</label>
                <input type="text" class="border" v-model="form.password" />
            </div>
            <div>
                <label for="">Confirm Password</label>
                <input
                    type="text"
                    class="border"
                    v-model="form.password_confirmation"
                />
            </div>
            <button
                class="rounded-2xl bg-blue-600 p-3 text-white"
                :disabled="form.processing"
                @click="submit"
            >
                Register
            </button>
        </form>
    </div>
</template>
