<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref  } from "vue";
import InputFlashMessage from "../../Components/InputFlashMessage.vue";


let form = useForm({
    first_name: null,
    middle_name: null,
    last_name: null,
    suffix: null,
    id_image: null,
    selfie_image: null,
});

let avatarGovID = ref(null);
let avatarSelfieID = ref(null);

const govIdChange = (e) => {
    form.id_image = e.target.files[0];
    avatarGovID.value = URL.createObjectURL(e.target.files[0]);
};
const selfieIdChange = (e) => {
    form.selfie_image = e.target.files[0];
    avatarSelfieID.value = URL.createObjectURL(e.target.files[0]);
};

const submit = () => {
    form.post(route("account.verifiy.id.post"));
};
</script>
<template>
    <div class="xs container mx-auto px-[0.5rem] lg:px-[5rem] xl:max-w-7xl">
        <div class="flex items-center pt-12">
            <div class="">
                <div class="relative">
                    <img
                        class="w-10 object-cover"
                        src="/assets/government_id_verification2.png"
                        alt="government_id"
                    />
                    <img
                        src="/assets/check_image.png"
                        alt=""
                        class="absolute right-0 top-3 w-5"
                    />
                </div>
            </div>
            <h1 class="text-[32px]">Profile and ID verification</h1>
        </div>

        <div class="p-4">
            <div class="border">
                <div class="p-6">
                    <div class="mb-3">
                        <p class="font-bold">Profile Name</p>
                        <p class="text-sm text-slate-500">
                            Your full name, including your middle name, should
                            match the name on your uploaded government ID. No
                            nicknames. Incomplete name will be disapproved.
                        </p>
                    </div>
                    <div
                        class="flex flex-col gap-5 lg:flex-row lg:justify-between"
                    >
                        <div class="flex flex-col">
                            <label for="" class="mb-2 text-sm text-slate-500"
                                >First name* (required)</label
                            >
                            <input
                                v-model="form.first_name"
                                type="text"
                                placeholder="First Name"
                                class="w-full rounded border p-2 outline-none ring-green-200 transition-all focus:shadow focus:shadow-green-200 focus:ring-2"
                            />
                            <InputFlashMessage
                                type="error"
                                :message="form.errors.first_name"
                            />
                        </div>

                        <div class="flex flex-col">
                            <label for="" class="mb-2 text-sm text-slate-500"
                                >Middle name (optional)</label
                            >
                            <input
                                v-model="form.middle_name"
                                type="text"
                                placeholder="Middle name"
                                class="w-full rounded border p-2 outline-none ring-green-200 transition-all focus:shadow focus:shadow-green-200 focus:ring-2"
                            />
                            <InputFlashMessage
                                type="error"
                                :message="form.errors.middle_name"
                            />
                        </div>

                        <div class="flex flex-col">
                            <label for="" class="mb-2 text-sm text-slate-500"
                                >Last name* (required)</label
                            >
                            <input
                                v-model="form.last_name"
                                type="text"
                                placeholder="Last Name"
                                class="w-full rounded border p-2 outline-none ring-green-200 transition-all focus:shadow focus:shadow-green-200 focus:ring-2"
                            />
                            <InputFlashMessage
                                type="error"
                                :message="form.errors.last_name"
                            />
                        </div>

                        <div class="flex flex-col">
                            <label for="" class="mb-2 text-sm text-slate-500"
                                >Suffix (optional)</label
                            >
                            <input
                                v-model="form.suffix"
                                type="text"
                                placeholder="Suffix"
                                class="w-full rounded border p-2 outline-none ring-green-200 transition-all focus:shadow focus:shadow-green-200 focus:ring-2"
                            />
                            <InputFlashMessage
                                type="error"
                                :message="form.errors.suffix"
                            />
                        </div>
                    </div>
                </div>

                <div class="mb-9 p-6">
                    <div class="mb-8">
                        <p class="font-bold">
                            Take a photo of your valid Senior ID
                        </p>
                        <p class="mb-3 text-sm text-slate-500">
                            Please take a clear photo of your valid Senior ID.
                            Ensure that all details, including your name, date
                            of birth, and photo, are visible and legible. The
                            image should be well-lit with minimal glare or
                            shadows to maintain clarity. Avoid cropping or
                            obscuring any part of the ID, and ensure that it’s
                            in focus. If possible, use a neutral background to
                            enhance readability.
                        </p>
                        <p class="text-sm text-slate-500">
                            IDs should be taken using a mobile phone camera.
                            Scanned, digital copies, or photocopied IDs will be
                            disapproved.
                        </p>
                    </div>

                    <div
                        class="flex flex-col justify-center gap-11 sm:flex-row"
                    >
                        <div class="w-full max-w-[400px]">
                            <label
                                for="govID"
                                class="relative mb-3 flex h-[200px] w-full max-w-[400px] flex-1 cursor-pointer justify-center rounded border-2 border-dashed p-4"
                            >
                                <img
                                    v-if="avatarGovID"
                                    :src="avatarGovID"
                                    alt=""
                                    class="absolute inset-0 h-[100%] w-[100%] object-cover"
                                />
                                <div class="mt-9 flex flex-col items-center">
                                    <svg
                                        class="mb-3"
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="48"
                                        height="48"
                                        viewBox="0 0 48 48"
                                    >
                                        <g
                                            fill="#000"
                                            fill-rule="nonzero"
                                            opacity=".203"
                                        >
                                            <path
                                                d="M39.429 4.286H7.714V2.143C7.714.959 8.602 0 9.696 0h27.75c1.095 0 1.983.96 1.983 2.143v2.143zM46 7.714H2c-1.105 0-2 .902-2 2.015v36.257C0 47.098.895 48 2 48h44c1.105 0 2-.902 2-2.014V9.729a2.007 2.007 0 0 0-2-2.015zm-32 8.057c2.21 0 4 1.804 4 4.029 0 2.225-1.79 4.029-4 4.029s-4-1.804-4-4.029c0-2.225 1.79-4.029 4-4.029zM10 37.93L30 19.8l10 18.129H10z"
                                            />
                                        </g>
                                    </svg>
                                    <p class="text-center text-slate-500">
                                        Upload your government ID photo here
                                    </p>
                                </div>
                                <input
                                    @change="govIdChange"
                                    accept="image/*"
                                    ref="govID"
                                    type="file"
                                    class="hidden"
                                    id="govID"
                                />
                            </label>
                            <InputFlashMessage
                                class="text-center"
                                type="error"
                                :message="form.errors.id_image"
                            />
                        </div>
                        <div class="w-full max-w-[400px]">
                            <label
                                for="selfieID"
                                class="relative mb-3 flex h-[200px] w-full flex-1 cursor-pointer justify-center rounded border-2 border-dashed p-4"
                            >
                                <img
                                    v-if="avatarSelfieID"
                                    :src="avatarSelfieID"
                                    alt=""
                                    class="absolute inset-0 h-[100%] w-[100%] object-cover"
                                />
                                <div class="mt-9 flex flex-col items-center">
                                    <svg
                                        class="mb-3"
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="48"
                                        height="48"
                                        viewBox="0 0 48 48"
                                    >
                                        <g
                                            fill="#000"
                                            fill-rule="nonzero"
                                            opacity=".203"
                                        >
                                            <path
                                                d="M39.429 4.286H7.714V2.143C7.714.959 8.602 0 9.696 0h27.75c1.095 0 1.983.96 1.983 2.143v2.143zM46 7.714H2c-1.105 0-2 .902-2 2.015v36.257C0 47.098.895 48 2 48h44c1.105 0 2-.902 2-2.014V9.729a2.007 2.007 0 0 0-2-2.015zm-32 8.057c2.21 0 4 1.804 4 4.029 0 2.225-1.79 4.029-4 4.029s-4-1.804-4-4.029c0-2.225 1.79-4.029 4-4.029zM10 37.93L30 19.8l10 18.129H10z"
                                            />
                                        </g>
                                    </svg>
                                    <p class="text-center text-slate-500">
                                        Upload your selfie photo with ID here
                                    </p>
                                </div>
                                <input
                                    @change="selfieIdChange"
                                    ref="selfieID"
                                    accept="image/*"
                                    type="file"
                                    class="hidden"
                                    id="selfieID"
                                />
                            </label>
                            <InputFlashMessage
                                class="text-center"
                                type="error"
                                :message="form.errors.selfie_image"
                            />
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-center justify-center">
                    <button
                        @click="submit"
                        class="mb-6 rounded bg-slate-500 px-4 py-2 text-sm text-white"
                    >
                        CONFIRM VERIFICATION
                    </button>

                    <p class="mb-5 text-sm">
                        Your application will be verified within 2-3 days.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
