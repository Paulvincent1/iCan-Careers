<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import InputFlashMessage from "../../Components/InputFlashMessage.vue";
import SubmitImage from "../../Components/SubmitImage.vue";

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
    form.id_image = e;
    avatarGovID.value = URL.createObjectURL(e);
};
const selfieIdChange = (e) => {
    form.selfie_image = e;
    avatarSelfieID.value = URL.createObjectURL(e);
};

const submit = () => {
    form.post(route("account.verifiy.id.post"));
};
</script>
<template>
    <Head title="Verification ID | iCan Careers" />
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
                        <SubmitImage
                            id="govId"
                            @imageAdded="govIdChange"
                            description="Upload your government ID photo here"
                            :error="form.errors.id_image"
                        ></SubmitImage>

                        <SubmitImage
                            id="selfieId"
                            @imageAdded="selfieIdChange"
                            description="Upload your selfie ID photo here"
                            :error="form.errors.selfie_image"
                        ></SubmitImage>
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
