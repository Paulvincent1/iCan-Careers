<script setup>
import Layout from "../Layouts/Layout.vue";
import SetupProfileLayout from "../Layouts/SetupProfileLayout.vue";

defineOptions({
    layout: [Layout, SetupProfileLayout],
});
</script>
<template>
    <div class="mt-9 border p-7">
        <h2 class="my-3 text-2xl font-semibold">
            Company/Business Information
        </h2>
        <p class="text-sm">
            Lorem, ipsum dolor sit amet consectetur adipisicing elit. Reiciendis
            corporis itaque eum eius unde aperiam aliquid vel ratione
            doloremque! Ullam illo pariatur officiis tempore iste totam soluta
            dignissimos libero. Hic?
        </p>

        <form @submit.prevent="submit">
            <div class="flex flex-col">
                <label class="mb-2 mt-4 font-semibold">Full name</label>
                <input
                    v-model="form.full_name"
                    type="text"
                    class="border px-3 py-2 outline-blue-400"
                    placeholder="ex. Social Media Manager"
                />
                <InputFlashMessage
                    type="error"
                    :message="form.errors.full_name"
                />
            </div>
            <div class="flex flex-col">
                <label class="mb-2 mt-4 font-semibold">Phone number</label>
                <input
                    v-model="form.phone_number"
                    type="text"
                    class="border px-3 py-2 outline-blue-400"
                    placeholder="ex. Social Media Manager"
                />
                <InputFlashMessage
                    type="error"
                    :message="form.errors.phone_number"
                />
            </div>

            <div class="mt-4">
                <label class="mb-2 mr-3 mt-4 font-semibold">BIRTH YEAR</label>
                <input
                    v-model="form.birth_year"
                    type="number"
                    min="1900"
                    :max="dayjs().format('YYYY') - 18"
                    class="w-20 border p-3 text-center outline-blue-500"
                    placeholder="YYYY"
                    required
                />
            </div>
            <div class="mt-4">
                <p class="mb-2 mt-4 font-semibold">GENDER</p>
                <div>
                    <label class="mr-2" for="male">MALE</label>
                    <input
                        v-model="form.gender"
                        type="radio"
                        class="text-center"
                        id="male"
                        value="Male"
                    />
                </div>
                <div>
                    <label class="mr-2" for="female">FEMALE</label>
                    <input
                        v-model="form.gender"
                        type="radio"
                        class="text-center"
                        id="female"
                        value="Female"
                    />
                </div>
                <InputFlashMessage type="error" :message="form.errors.gender" />
            </div>

            <div class="flex justify-end">
                <button class="rounded border bg-blue-500 px-4 py-2 text-white">
                    Save
                </button>
            </div>
        </form>
    </div>
</template>
