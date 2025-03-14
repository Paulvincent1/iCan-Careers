<script setup>
import { router, useForm } from "@inertiajs/vue3";
import Layout from "../Layouts/Layout.vue";
import SetupProfileLayout from "../Layouts/SetupProfileLayout.vue";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import InputFlashMessage from "../Components/InputFlashMessage.vue";
import dayjs from "dayjs";
import { ref, watch } from "vue";
import SubmitImage from "../Components/SubmitImage.vue";
import Maps from "../Components/Maps.vue";
import { debounce } from "lodash";

defineOptions({
    layout: [Layout, SetupProfileLayout],
});

const props = defineProps({
    bussinessProps: Array,
});

const searchBusiness = ref("");
const isBusinessSelected = ref(false);
const businessSelected = ref(null);

const form = useForm({
    full_name: "",
    phone_number: "",
    birth_year: "",
    gender: "",
    employer_type: "",
    business_id: null,
    business_name: "",
    business_logo: null,
    industry: "",
    business_description: "",
    business_location: [120.9842, 14.5995],
});

watch(
    searchBusiness,
    debounce(() => {
        if (searchBusiness.value.trim()) {
            search();
        }
    }, 1000),
);

const selectBusiness = (business) => {
    isBusinessSelected.value = true;
    form.business_id = business.id;
    form.business_name = business.business_name;
    form.business_logo = business.business_logo;
    businessSelected.value = business;
};

const removeSelectedBusiness = () => {
    isBusinessSelected.value = false;
    form.business_id = null;
    businessSelected.value = null;
};

const setMarkLocation = (coordinates) => {
    form.business_location = coordinates;
};

const otherIndustry = ref("");
const showInput = ref(false);

watch(
    () => form.industry,
    (newValue) => {
        showInput.value = newValue === "Other";
    },
);

const showCompanyForm = ref(false);
watch(
    () => form.employer_type,
    (newValue) => {
        showCompanyForm.value = newValue === "business";
    },
);

const imageAdded = (image) => {
    form.business_logo = image;
};

const search = () => {
    router.get(
        "/employers/createprofile",
        { business_name: searchBusiness.value },
        { preserveScroll: true, preserveState: true },
    );
};

const submit = () => {
    if (form.industry === "Other") {
        form.industry = otherIndustry.value;
    }
    form.post(route("create.profile.employer.post"));
};
</script>

<template>
    <Head title="Create Profile | iCan Careers" />

    <div class="mb-4">
        <div class="flex justify-center">
            <div class="mt-5 w-full rounded-lg bg-white p-8">
                <h2 class="text-[24px] text-gray-900">
                    Create Your Employer Profile
                </h2>
                <p class="mb-6 text-lg text-gray-700">
                    Please provide your details to create your employer profile.
                </p>

                <hr class="my-4 border-gray-300" />
                <h3 class="text-[18px] font-bold text-gray-900">
                    Personal Information
                </h3>
                <h3 class="text-xs">
                    This information will be displayed publicly so be careful
                    what you share.
                </h3>

                <form @submit.prevent="submit" class="mt-4 space-y-6">
                    <!-- Full Name -->
                    <div class="my-7 flex flex-col">
                        <label class="text-gray-500">Full Name</label>
                        <input
                            v-model.trim="form.full_name"
                            type="text"
                            class="mt-2 w-full rounded-lg border px-4 py-2 text-lg outline-orange-200"
                            placeholder="ex: Draven Troy Coloma"
                            required
                        />
                        <InputFlashMessage
                            type="error"
                            :message="form.errors.full_name"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <!-- Birth Year -->
                        <div class="flex flex-col">
                            <label class="text-gray-500">Birth Year</label>
                            <input
                                v-model.number="form.birth_year"
                                type="number"
                                min="1900"
                                :max="dayjs().year() - 18"
                                class="mt-2 w-full rounded-lg border px-4 py-2 text-lg outline-orange-200"
                                placeholder="YYYY"
                                required
                            />
                        </div>
                        <!-- Phone Number -->
                        <div class="flex flex-col">
                            <label class="text-gray-500">Phone Number</label>
                            <input
                                v-model.trim="form.phone_number"
                                type="number"
                                class="mt-2 w-full rounded-lg border px-4 py-2 text-lg outline-orange-200"
                                placeholder="09123456789"
                                required
                            />
                            <InputFlashMessage
                                type="error"
                                :message="form.errors.phone_number"
                            />
                        </div>
                    </div>

                    <!-- Gender -->
                    <div class="flex flex-col">
                        <p class="text-gray-500">Gender</p>
                        <div class="mt-2 flex items-center gap-6">
                            <label
                                class="text-md flex cursor-pointer items-center gap-2"
                            >
                                <input
                                    v-model="form.gender"
                                    type="radio"
                                    value="Male"
                                    class="h-4 w-4"
                                />
                                <span class="text-gray-700">Male</span>
                            </label>
                            <label
                                class="text-md flex cursor-pointer items-center gap-2"
                            >
                                <input
                                    v-model="form.gender"
                                    type="radio"
                                    value="Female"
                                    class="h-4 w-4"
                                />
                                <span class="text-gray-700">Female</span>
                            </label>
                        </div>
                        <InputFlashMessage
                            type="error"
                            :message="form.errors.gender"
                        />
                    </div>

                    <!-- Employer Type -->
                    <div class="flex flex-col">
                        <label class="text-gray-500"
                            >What kind of employer are you?</label
                        >
                        <select
                            v-model="form.employer_type"
                            class="mt-2 w-[300px] rounded-lg border px-4 py-2 text-lg outline-orange-200"
                            required
                        >
                            <option value="business">
                                Business / Company Employer
                            </option>
                            <option value="individual">
                                Individual (Freelance)
                            </option>
                        </select>
                    </div>
                    <hr class="my-4 border-gray-300" />

                    <!-- Company Form (Conditional) -->
                    <Transition name="fade">
                        <div v-if="showCompanyForm" class="space-y-6">
                            <!-- Search Existing Company -->
                            <div class="relative w-full">
                                <h3 class="text-xl font-bold text-gray-900">
                                    Company Details
                                </h3>
                                <h3 class="mb-7 text-xs">
                                    Your company details help potential job
                                    seekers learn more about your business.
                                    Ensure the information you provide is
                                    accurate and represents your company well.
                                </h3>
                                <label
                                    class="mt-7 text-lg font-semibold text-gray-800"
                                    >Look Up an Existing Company</label
                                >
                                <div class="relative">
                                    <input
                                        v-model.trim="searchBusiness"
                                        type="text"
                                        class="w-full rounded-lg border px-4 py-2 pl-10 text-lg outline-orange-200"
                                        placeholder="Enter company name to search..."
                                        autocomplete="off"
                                    />
                                    <svg
                                        class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M21 21l-4.35-4.35M16.65 10.65a6 6 0 1 0-8.49 8.49 6 6 0 0 0 8.49-8.49z"
                                        />
                                    </svg>
                                </div>

                                <div
                                    class="mt-2 h-52 w-full overflow-y-auto rounded-lg border border-gray-400 p-2"
                                >
                                    <div
                                        v-for="business in bussinessProps"
                                        :key="business.id"
                                        @click="selectBusiness(business)"
                                        class="flex cursor-pointer items-center gap-3 rounded-lg p-2 hover:bg-gray-100"
                                    >
                                        <img
                                            :src="business.business_logo"
                                            class="w-12 rounded object-cover"
                                            alt="Company Logo"
                                        />
                                        <p class="text-lg">
                                            {{ business.business_name }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Selected Company -->
                            <div
                                v-if="businessSelected"
                                class="flex items-center justify-between rounded-lg border border-gray-400 p-2"
                            >
                                <div class="flex items-center gap-3">
                                    <img
                                        :src="businessSelected.business_logo"
                                        class="w-12 rounded object-cover"
                                        alt="Selected Company Logo"
                                    />
                                    <p class="text-lg">
                                        {{ businessSelected.business_name }}
                                    </p>
                                </div>
                                <i
                                    @click="removeSelectedBusiness"
                                    class="bi bi-x cursor-pointer text-lg hover:text-red-500"
                                ></i>
                            </div>

                            <!-- Company Information -->
                            <div v-if="!isBusinessSelected">
                                <!-- Business Name -->
                                <div class="flex flex-col">
                                    <label
                                        class="text-lg font-semibold text-gray-800"
                                        >Business / Company Name</label
                                    >
                                    <input
                                        v-model.trim="form.business_name"
                                        type="text"
                                        class="mt-2 w-full rounded-lg border px-4 py-2 text-lg outline-orange-200"
                                        placeholder="e.g. iCan Careers"
                                    />
                                    <InputFlashMessage
                                        type="error"
                                        :message="form.errors.business_name"
                                    />
                                </div>

                                <!-- Business Logo -->
                                <div class="my-7 flex flex-col">
                                    <label
                                        class="text-lg font-semibold text-gray-800"
                                        >Business Logo</label
                                    >
                                    <SubmitImage
                                        @imageAdded="imageAdded"
                                        description="<span class='text-blue-500'><u>Upload</u></span> the business logo here"
                                        :error="form.errors.business_logo"
                                    />
                                </div>

                                <!-- Industry -->
                                <div class="flex w-full flex-col">
                                    <label
                                        class="text-lg font-semibold text-gray-800"
                                        >Industry</label
                                    >
                                    <select
                                        v-model="form.industry"
                                        class="w-full max-w-md rounded-lg border px-4 py-2 text-lg outline-orange-200 md:max-w-sm"
                                    >
                                        <option value="Technology and IT">
                                            Technology and IT
                                        </option>
                                        <option value="Remote Customer Support">
                                            Remote Customer Support
                                        </option>
                                        <option value="Creative and Design">
                                            Creative and Design
                                        </option>
                                        <option
                                            value="Accounting and Financial Services"
                                        >
                                            Accounting and Financial Services
                                        </option>
                                        <option value="Social Media Management">
                                            Social Media Management
                                        </option>
                                        <option value="Other">Other</option>
                                    </select>
                                    <input
                                        v-if="showInput"
                                        v-model.trim="otherIndustry"
                                        type="text"
                                        class="mt-2 w-full rounded-lg border px-4 py-2 text-lg outline-orange-200"
                                        placeholder="Please Specify"
                                    />
                                    <InputFlashMessage
                                        type="error"
                                        :message="form.errors.industry"
                                    />
                                </div>

                                <!-- Company Description -->
                                <div class="my-7">
                                    <label
                                        class="text-lg font-semibold text-gray-800"
                                        >Company Description</label
                                    >
                                    <textarea
                                        v-model.trim="form.business_description"
                                        class="mt-2 h-[200px] w-full resize-none rounded-lg border px-4 py-2 text-lg outline-orange-200"
                                        placeholder="Tell us about your company..."
                                    ></textarea>
                                    <InputFlashMessage
                                        type="error"
                                        :message="
                                            form.errors.business_description
                                        "
                                    />
                                </div>

                                <!-- Company Location -->
                                <div>
                                    <label
                                        class="mb-4 block text-lg font-semibold text-gray-800"
                                        >Company Location</label
                                    >
                                    <div class="p-1">
                                        <Maps
                                            @update:coordinates="
                                                setMarkLocation
                                            "
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Transition>

                    <div class="mt-4 flex justify-end gap-3">
                        <!-- Submit Button -->
                        <div
                            type="button"
                            @click="$inertia.visit(route('employer.dashboard'))"
                            class="mt-4 flex justify-end"
                        >
                            <button
                                class="cursor-pointer rounded p-2 text-black"
                            >
                                <u>Skip for now</u>
                            </button>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-4 flex justify-end">
                            <button
                                class="cursor-pointer rounded bg-[#fa8334] p-2 text-white"
                            >
                                Save Profile
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
