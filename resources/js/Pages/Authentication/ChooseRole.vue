<script setup>
import { useForm } from "@inertiajs/vue3";

let form = useForm({
    role: null,
});

const submit = (isEmployer) => {
    if (isEmployer) {
        form.role = "Employer";
    }

    form.post(route("store.role"));
};
</script>
<template>
    <div
        class="container mx-auto grid h-[calc(100vh-4.625rem)] place-items-center px-[0.5rem] md:max-w-7xl"
    >
        <div class="flex h-full w-full flex-col p-3">
            <h1 class="mb-3 text-2xl">Select role</h1>
            <div class="flex h-full w-full gap-5">
                <div class="flex-1 rounded-lg bg-green-200 p-3">
                    <h1>Worker</h1>
                    <div class="flex gap-2">
                        <input
                            type="radio"
                            value="Senior"
                            id="senior"
                            v-model="form.role"
                        />
                        <label for="senior">Senior Citizen</label>
                    </div>
                    <div class="flex gap-2">
                        <input
                            type="radio"
                            value="PWD"
                            id="pwd"
                            v-model="form.role"
                        />
                        <label for="pwd">PWD</label>
                    </div>
                    <button @click="submit(false)">
                        Continue with worker >
                    </button>
                </div>
                <div class="flex-1 rounded-lg bg-blue-200 p-3">
                    <h1>Employer</h1>
                    <button @click="submit(true)">
                        Continue with employer >
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
