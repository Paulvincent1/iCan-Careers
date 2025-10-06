<script setup>
import { router, useForm } from "@inertiajs/vue3";
import AuthInput from "../Components/AuthInput.vue";
import Quotes from "../Components/Quotes.vue";
import AuthForm from "../Components/AuthForm.vue";
import { onMounted, ref, useTemplateRef } from "vue";
import InputFlashMessage from "../Components/InputFlashMessage.vue";
import ReusableModal from "../Components/Modal/ReusableModal.vue";
import InputCodeModal from "../Components/Modal/InputCodeModal.vue";


let form = useForm({
    name: null,
    email: null,
    password: null,
    password_confirmation: null,
    role: null,
    verification_code: null,
});

const sendCode = () => {
    if (!validateInput()) return; // stop if validation fails

    router.post(
    route("register.send.code"),
    {
        name: form.name,
        role: form.role,
        email: form.email,
    },
    {
        preserveState: true,
        onError: (errors) => {
            console.error(errors);
        },
        onSuccess: () => {
            openModal();
        },
    },
);

};
const submit = () => {
    form.post(route("register.post"), {
        onError: (errors) => {
            closeModal();
        },
        onSuccess: (page) => {
            closeModal();
        },
    });
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

let openSendCodeModal = ref(false);
function openModal() {
    openSendCodeModal.value = true;
}

function closeModal() {
    openSendCodeModal.value = false;
}

let errors = ref({
    name: null,
    email: null,
    password: null,
    password_confirmation: null,
    role: null,
});

const validateInput = () => {
    let isValid = true;

    // Name validation
    if (!form.name) {
        errors.value.name = "Name is required";
        isValid = false;
    } else {
        errors.value.name = null;
    }

    // Email validation
    const emailRegex = /^\S+@\S+\.\S+$/;
    if (!form.email) {
        errors.value.email = "Email is required";
        isValid = false;
    } else if (!emailRegex.test(form.email)) {
        errors.value.email = "Email is invalid";
        isValid = false;
    } else {
        errors.value.email = null;
    }

    // Password validation
    if (!form.password) {
        errors.value.password = "Password is required";
        isValid = false;
    } else if (form.password.length < 6) {
        errors.value.password = "Password must be at least 6 characters";
        isValid = false;
    } else {
        errors.value.password = null;
    }

    // Password confirmation
    if (form.password !== form.password_confirmation) {
        errors.value.password_confirmation = "Passwords do not match";
        isValid = false;
    } else {
        errors.value.password_confirmation = null;
    }

    // Role validation
    if (!form.role) {
        errors.value.role = "Role is required";
        isValid = false;
    } else {
        errors.value.role = null;
    }

    return isValid;
};
</script>

<template>
    <Head title="Register | iCan Careers" />
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
            <InputFlashMessage
                :message="$page.props.errors.message ?? errors.role"
                type="error"
            />
            <AuthInput
                name="NAME"
                :message="form.errors.name ?? errors.name"
                type="text"
                v-model="form.name"
                class="mb-3"
            />
            <AuthInput
                name="EMAIL"
                :message="$page.props.errors.email ?? form.errors.email ?? errors.email"
                type="email"
                v-model="form.email"
                class="mb-3"
            />
            <AuthInput
                name="PASSWORD"
                :message="form.errors.password ?? errors.password"
                type="password"
                v-model="form.password"
                class="mb-3"
            />
            <AuthInput
                name="CONFIRM PASSWORD"
                :message="
                    form.errors.password_confirmation ??
                    errors.password_confirmation
                "
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
                class="mb-3 rounded bg-[#fa8334] p-3 text-white"
                :disabled="form.processing"
                @click="sendCode"
            >
                Register

            </button>

            <a
                :href="route('auth.google')"
                class="mb-3 flex w-full items-center justify-center gap-2 rounded-lg border border-slate-200 px-4 py-3 text-slate-700 transition duration-150 hover:border-slate-400 hover:text-slate-900 hover:shadow dark:border-slate-700 dark:text-slate-200 dark:hover:border-slate-500 dark:hover:text-slate-300"
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
            <Link :href="route('login')" class="text-center">Log in</Link>
        </AuthForm>
    </div>
    <InputCodeModal @closeModal="closeModal" v-if="openSendCodeModal">
        <div class="w-[350px] overflow-auto rounded bg-white p-4 text-[#171816]">
            <p class="text-2xl">Enter the code</p>
            <p class="text-gray-400">We’ve sent the code to your email.</p>

            <input
                class="mb-3 w-full bg-gray-200 p-3 outline-none"
                type="text"
                id="code"
                name="code"
                v-model="form.verification_code"
                placeholder="Enter code here"
            />
            <div>
                <button
                    @click="submit"
                    type="button"
                    class="w-full rounded bg-orange-400 p-3 text-white"
                >
                    submit
                </button>
            </div>
        </div>
    </InputCodeModal>

</template>
