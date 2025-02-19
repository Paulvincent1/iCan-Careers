<script setup>
import { router, useForm } from "@inertiajs/vue3";
import Layout from "../Layouts/Layout.vue";
import SetupProfileLayout from "../Layouts/SetupProfileLayout.vue";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import InputFlashMessage from "../Components/InputFlashMessage.vue";
import dayjs from "dayjs";
import { ref, Transition, watch } from "vue";
import SubmitImage from "../Components/SubmitImage.vue";
import Maps from "../Components/Maps.vue";
import { debounce } from "lodash";

defineOptions({
    layout: [Layout, SetupProfileLayout],
});

let props = defineProps({
    bussinessProps: null,
});

let searchBusiness = ref("");
let isBusinessSelected = ref(false);
let businessSelected = ref(null);

watch(
    searchBusiness,
    debounce(() => {
        search();
    }, 1000),
);

function selectBusiness(business) {
    isBusinessSelected.value = true;
    form.business_id = business.id;
    businessSelected.value = business;
}

function removeSelectedBusiness() {
    isBusinessSelected.value = false;
    form.business_id = null;
    businessSelected.value = null;
}

let form = useForm({
    full_name: null,
    phone_number: null,
    birth_year: null,
    gender: null,
    employer_type: null,
    business_id: null,
    business_name: null,
    business_logo: null,
    industry: null,
    business_description: null,
    business_location: [120.9842, 14.5995],
});

function setMarkLocation(coordinates, newvalue) {
    console.log(newvalue);

    form.business_location = coordinates;
}

let otherIndustry = ref("");
let showInput = ref(false);
watch(
    () => form.industry,
    (e) => {
        if (e === "Other") {
            showInput.value = true;
            // console.log(e);
        } else {
            showInput.value = false;
        }
    },
);

let showCompanyForm = ref(false);
watch(
    () => form.employer_type,
    () => {
        if (form.employer_type === "business") {
            showCompanyForm.value = true;
        }
        if (form.employer_type === "individual") {
            showCompanyForm.value = false;
        }
    },
);

function imageAdded(image) {
    form.business_logo = image;
}

function search() {
    router.get(
        "/employers/createprofile",
        {
            business_name: searchBusiness.value,
        },
        {
            preserveScroll: true,
            preserveState: true,
        },
    );
}

const submit = () => {
    if (form.industry === "Other") {
        form.industry = otherIndustry.value;
    }
    form.post(route("create.profile.employer.post"));
};
</script>

<template>
    <div class="mt-9 border p-7">
        <h2 class="my-3 text-2xl font-semibold">Tell us about you</h2>
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
                    placeholder="John Doe"
                    required
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
                    type="number"
                    class="border px-3 py-2 outline-blue-400"
                    placeholder="951...."
                    required
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
                    <label class="mr-2" for="male">Male</label>
                    <input
                        v-model="form.gender"
                        type="radio"
                        class="text-center"
                        id="male"
                        value="Male"
                    />
                </div>
                <div>
                    <label class="mr-2" for="female">Female</label>
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

            <div class="mt-5 flex flex-col">
                <label class="mb-2 mr-3 mt-4 font-semibold"
                    >What kind of employer are you?</label
                >
                <select
                    name=""
                    id=""
                    class="border p-3"
                    v-model="form.employer_type"
                    required
                >
                    <option value="business">
                        Business / Company Employer
                    </option>
                    <option value="individual" class="">
                        Individual (Freelance)
                    </option>
                </select>
            </div>

            <Transition
                enter-active-class="animate__animated animate__fadeIn"
                leave-active-class="animate__animated animate__fadeOut"
            >
                <div v-if="showCompanyForm" class="mb-3">
                    <div>
                        <h2 class="my-3 text-2xl font-semibold">
                            Search exisiting company
                        </h2>

                        <input
                            v-model="searchBusiness"
                            type="text"
                            class="mb-2 border p-2"
                            placeholder="Search exisitng company"
                        />

                        <div class="mb-2 h-52 w-72 overflow-y-auto border p-2">
                            <div
                                v-for="(business, index) in bussinessProps"
                                :key="business.id"
                                @click="selectBusiness(business)"
                                class="flex cursor-pointer items-center gap-3 p-2 shadow"
                            >
                                <img
                                    src="/assets/images.png"
                                    class="w-12 rounded object-cover"
                                    alt=""
                                />
                                <p class="text-lg">
                                    {{ business.business_name }}
                                </p>
                            </div>
                        </div>
                        <div v-if="businessSelected">
                            <p>Selected Company</p>
                            <div
                                class="flex w-72 cursor-pointer items-center justify-between p-2 shadow"
                            >
                                <div class="flex items-center gap-3">
                                    <div>
                                        <img
                                            src="/assets/images.png"
                                            class="w-12 rounded object-cover"
                                            alt=""
                                        />
                                    </div>
                                    <p class="text-lg">
                                        {{ businessSelected.business_name }}
                                    </p>
                                </div>
                                <i
                                    @click="removeSelectedBusiness"
                                    class="bi bi-x text-lg"
                                ></i>
                            </div>
                        </div>
                    </div>

                    <div v-if="!isBusinessSelected">
                        <h2 class="my-3 text-2xl font-semibold">
                            Company Information
                        </h2>
                        <p class="text-sm">
                            Lorem, ipsum dolor sit amet consectetur adipisicing
                            elit. Reiciendis corporis itaque eum eius unde
                            aperiam aliquid vel ratione doloremque! Ullam illo
                            pariatur officiis tempore iste totam soluta
                            dignissimos libero. Hic?
                        </p>

                        <div class="flex flex-col">
                            <label class="mb-2 mt-4 font-semibold"
                                >Business / Company name</label
                            >
                            <input
                                v-model="form.business_name"
                                type="text"
                                class="border px-3 py-2 outline-blue-400"
                                placeholder="ex. iCan Careers"
                            />
                            <InputFlashMessage
                                type="error"
                                :message="form.errors.business_name"
                            />
                        </div>
                        <div class="flex flex-col">
                            <label class="mb-2 mt-4 font-semibold"
                                >Business logo</label
                            >
                            <SubmitImage
                                @imageAdded="imageAdded"
                                description="Upload the business logo here"
                                :error="form.errors.business_logo"
                            ></SubmitImage>
                        </div>
                        <div class="flex flex-col">
                            <label class="mb-2 mt-4 font-semibold"
                                >Industry</label
                            >
                            <select
                                name=""
                                id=""
                                class="border p-3"
                                v-model="form.industry"
                            >
                                <option value="Technology and IT">
                                    Technology and IT
                                </option>
                                <option
                                    value="Remote Customer Support"
                                    class=""
                                >
                                    Remote Customer Support
                                </option>
                                <option value="Creative and Design" class="">
                                    Creative and Design
                                </option>
                                <option
                                    value="Accounting and Financial Services"
                                    class=""
                                >
                                    Accounting and Financial Services
                                </option>
                                <option
                                    value="Social Media Management"
                                    class=""
                                >
                                    Social Media Management
                                </option>
                                <option value="Other" class="">Other</option>
                            </select>
                            <input
                                v-if="showInput"
                                v-model="otherIndustry"
                                type="text"
                                class="mt-3 border px-3 py-2 outline-blue-400"
                                placeholder="Please Specify"
                            />
                            <InputFlashMessage
                                type="error"
                                :message="form.errors.industry"
                            />
                        </div>
                        <div class="flex flex-col">
                            <label class="mb-2 mt-4 font-semibold"
                                >Company description</label
                            >
                            <textarea
                                v-model="form.business_description"
                                type="text"
                                class="border px-3 pb-9 pt-2 outline-blue-400"
                                placeholder="Tell us summary about your skills and how you want to be known as worker."
                            ></textarea>
                            <InputFlashMessage
                                type="error"
                                :message="form.errors.business_description"
                            />
                        </div>

                        <div class="flex flex-col">
                            <label class="mb-2 mt-4 font-semibold"
                                >Please select your company location</label
                            >
                        </div>
                        <Maps @update:coordinates="setMarkLocation"></Maps>
                    </div>
                </div>
            </Transition>

            <div class="mt-3 flex justify-end">
                <button class="rounded border bg-blue-500 px-4 py-2 text-white">
                    Save
                </button>
            </div>
        </form>
    </div>
</template>
<style></style>
