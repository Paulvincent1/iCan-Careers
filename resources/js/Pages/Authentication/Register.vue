<script setup>
import { useForm } from "@inertiajs/vue3";
import AuthInput from "../Components/AuthInput.vue";
import Quotes from "../Components/Quotes.vue";
import AuthForm from "../Components/AuthForm.vue";
import { onMounted, ref, useTemplateRef } from "vue";
import InputFlashMessage from "../Components/InputFlashMessage.vue";

let form = useForm({
    name: null,
    email: null,
    password: null,
    password_confirmation: null,
    role: null,
});

const submit = () => {
    form.post(route("register.post"));
};

let userRole = ref("Worker");
let select = useTemplateRef("select");
function selectRole() {
    if (select.value.value === "Worker") {
        userRole.value = "Worker";
        if (form.role === "Employer") {
            form.role = null;
        }
    } else {
        userRole.value = "Employer";
        form.role = "Employer";
    }
    console.log(form.role);
}

onMounted(() => {
    selectRole();
});
</script>

<template>
    <div
        class="container mx-auto grid justify-items-center px-[0.5rem] md:max-w-7xl"
    >
        <AuthForm>
            <div class="mb-8">
                <h1 class="mb-2 text-3xl font-semibold">Sign up</h1>

                <p class="mb-[80px]">
                    <Quotes />
                </p>
            </div>
            <AuthInput
                name="NAME"
                :message="form.errors.name"
                type="text"
                v-model="form.name"
                class="mb-3"
            />
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
            <AuthInput
                name="CONFIRM PASSWORD"
                :message="form.errors.password_confirmation"
                type="password"
                v-model="form.password_confirmation"
                class="mb-3"
            />
            <div class="mb-3 flex flex-col text-sm">
                <div class="flex">
                    <p>I'm a</p>
                    <select name="role" id="" ref="select" @change="selectRole">
                        <option value="Worker">Worker</option>
                        <option value="Employer">Employer</option>
                    </select>
                </div>

                <div v-if="userRole === 'Worker'" class="mb-4">
                    <div class="flex gap-2">
                        <input
                            @change="selectRole"
                            type="radio"
                            value="Senior"
                            id="senior"
                            v-model="form.role"
                        />
                        <label for="senior">Senior Citizen</label>
                    </div>
                    <div class="flex gap-2">
                        <input
                            @change="selectRole"
                            type="radio"
                            value="PWD"
                            id="pwd"
                            v-model="form.role"
                        />
                        <label for="pwd">PWD</label>
                    </div>
                </div>
                <InputFlashMessage :message="form.errors.role" type="error" />
            </div>

            <button
                class="mb-3 rounded bg-blue-600 p-3 text-white"
                :disabled="form.processing"
                @click="submit"
            >
                Register
            </button>
            <div class="flex items-center justify-center gap-2">
                <div class="h-[1px] w-[40%] border bg-black"></div>
                <div>or</div>
                <div class="h-[1px] w-[40%] border bg-black"></div>
            </div>
            <Link :href="route('login')" class="text-center">Log in</Link>
        </AuthForm>
    </div>
</template>
