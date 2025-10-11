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

defineOptions({ layout: [Layout, SetupProfileLayout] });

const props = defineProps({ bussinessProps: Array });

const searchBusiness = ref("");
const isEditingExistingBusiness = ref(false);
const hasSelectedBusinessOption = ref(false);
const businessSelected = ref(null);
const otherIndustry = ref("");
const showInput = ref(false);
const showCompanyForm = ref(false);

const form = useForm({
    full_name: "",
    phone_number: "",
    birth_year: "",
    gender: "",
    employer_type: "",
    business_id: null,
    business_name: "",
    business_logo_url: null,
    industry: "",
    business_description: "",
    business_location: [120.9842, 14.5995],
});

// Search debounce
watch(
    searchBusiness,
    debounce(() => {
        if (searchBusiness.value.trim()) search();
    }, 1000),
);

// Select business
const selectBusiness = (business) => {
    isEditingExistingBusiness.value = true;
    hasSelectedBusinessOption.value = true;
    form.business_id = business.id;
    form.business_name = business.business_name;
    form.industry = business.industry;
    form.business_description = business.business_description;
    form.business_location = business.business_location;
    businessSelected.value = business;
};

// Toggle between creating new and selecting existing
const toggleBusinessOption = () => {
    if (isEditingExistingBusiness.value) createNewBusiness();
    else selectExistingBusiness();
};

const removeSelectedBusiness = () => {
    isEditingExistingBusiness.value = false;
    hasSelectedBusinessOption.value = false;
    businessSelected.value = null;
    form.business_id = null;
    form.business_name = "";
    form.industry = "";
    form.business_description = "";
    form.business_location = [120.9842, 14.5995];
};

const createNewBusiness = () => {
    isEditingExistingBusiness.value = false;
    hasSelectedBusinessOption.value = true;
    businessSelected.value = null;
    form.business_id = null;
    form.business_name = "";
    form.industry = "";
    form.business_description = "";
    form.business_location = [120.9842, 14.5995];
};

const selectExistingBusiness = () => {
    isEditingExistingBusiness.value = true;
    hasSelectedBusinessOption.value = true;
};

const setMarkLocation = (coordinates) => (form.business_location = coordinates);

watch(
    () => form.industry,
    (val) => (showInput.value = val === "Other"),
);

watch(
    () => form.employer_type,
    (val) => {
        showCompanyForm.value = val === "business";
        if (val === "individual") removeSelectedBusiness();
    },
);

const imageAdded = (image) => (form.business_logo_url = image);

const search = () => {
    router.get(
        "/employers/createprofile",
        { business_name: searchBusiness.value },
        { preserveScroll: true, preserveState: true },
    );
};

const submit = () => {
    if (form.industry === "Other") form.industry = otherIndustry.value;
    form.post(route("create.profile.employer.post"));
};
</script>

<template>
    <Head title="Create Profile | iCan Careers" />

    <div class="mb-4 flex justify-center">
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
                This information will be displayed publicly so be careful what
                you share.
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
                        :message="form.errors.full_name"
                        type="error"
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
                            :message="form.errors.phone_number"
                            type="error"
                        />
                    </div>
                </div>

                <!-- Gender -->
                <div class="flex flex-col">
                    <p class="text-gray-500">Gender</p>
                    <div class="mt-2 flex items-center gap-6">
                        <label class="flex cursor-pointer items-center gap-2"
                            ><input
                                v-model="form.gender"
                                type="radio"
                                value="Male"
                                class="h-4 w-4"
                            /><span class="text-gray-700">Male</span></label
                        >
                        <label class="flex cursor-pointer items-center gap-2"
                            ><input
                                v-model="form.gender"
                                type="radio"
                                value="Female"
                                class="h-4 w-4"
                            /><span class="text-gray-700">Female</span></label
                        >
                    </div>
                    <InputFlashMessage
                        :message="form.errors.gender"
                        type="error"
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
                        <option value="" disabled selected>
                            Select employer type
                        </option>
                        <option value="business">
                            Business / Company Employer
                        </option>
                        <option value="individual">
                            Individual (Freelance)
                        </option>
                    </select>
                </div>

                <hr class="my-4 border-gray-300" />

                <!-- Company Form -->
                <Transition name="fade">
                    <div v-if="showCompanyForm" class="space-y-6">
                        <h3 class="text-xl font-bold text-gray-900">
                            Company Details
                        </h3>
                        <h3 class="mb-7 text-xs">
                            Your company details help potential job seekers
                            learn more about your business. Ensure the
                            information you provide is accurate and represents
                            your company well.
                        </h3>

                        <!-- Business Setup Options -->
                        <div
                            v-if="!hasSelectedBusinessOption"
                            class="space-y-4"
                        >
                            <p class="text-lg font-semibold text-gray-800">
                                How would you like to set up your business?
                            </p>
                            <div class="mt-2 flex gap-4">
                                <button
                                    type="button"
                                    @click="createNewBusiness"
                                    class="flex-1 rounded-lg border-2 border-orange-500 bg-orange-50 p-4 text-center hover:bg-orange-100"
                                >
                                    <h4 class="font-semibold text-orange-700">
                                        Create New Business
                                    </h4>
                                    <p class="mt-2 text-sm text-gray-600">
                                        Create a new business profile with your
                                        information
                                    </p>
                                </button>
                                <button
                                    type="button"
                                    @click="selectExistingBusiness"
                                    class="flex-1 rounded-lg border-2 border-blue-500 bg-blue-50 p-4 text-center hover:bg-blue-100"
                                >
                                    <h4 class="font-semibold text-blue-700">
                                        Select Existing Business
                                    </h4>
                                    <p class="mt-2 text-sm text-gray-600">
                                        Choose from existing businesses in our
                                        system
                                    </p>
                                </button>
                            </div>
                        </div>

                        <!-- Existing Business Search -->
                        <div
                            v-if="isEditingExistingBusiness"
                            class="relative w-full space-y-2"
                        >
                            <div class="flex items-center justify-between">
                                <p class="text-lg font-semibold text-gray-800">
                                    Look Up an Existing Company
                                </p>
                                <button
                                    type="button"
                                    @click="toggleBusinessOption"
                                    class="text-sm text-blue-600 hover:underline"
                                >
                                    Or create a new business
                                </button>
                            </div>
                            <input
                                v-model.trim="searchBusiness"
                                type="text"
                                placeholder="Enter company name to search..."
                                class="w-full rounded-lg border px-4 py-2 pl-10 text-lg outline-orange-200"
                                autocomplete="off"
                            />
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
                                        :src="business.business_logo_url"
                                        class="w-12 rounded object-cover"
                                        alt="Company Logo"
                                    />
                                    <p class="text-lg">
                                        {{ business.business_name }}
                                    </p>
                                </div>
                            </div>

                            <div
                                v-if="businessSelected"
                                class="flex items-center justify-between rounded-lg border border-gray-400 p-2"
                            >
                                <div class="flex items-center gap-3">
                                    <img
                                        :src="businessSelected.business_logo_url"
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
                        </div>

                        <!-- New Business Form -->
                        <!-- New Business Form -->
                        <div
                            v-if="
                                !isEditingExistingBusiness &&
                                hasSelectedBusinessOption
                            "
                            class="space-y-6"
                        >
                            <!-- Top Toggle Button -->
                            <div class="mt-4 flex items-center justify-between">
                                <p class="text-lg font-semibold text-gray-800">
                                    Business Details
                                </p>
                                <button
                                    type="button"
                                    @click="toggleBusinessOption"
                                    class="text-sm text-blue-600 hover:underline"
                                >
                                    Or choose an existing business
                                </button>
                            </div>

                            <!-- Business Name -->
                            <div class="flex flex-col">
                                <label
                                    class="text-lg font-semibold text-gray-800"
                                    >Business / Company Name</label
                                >
                                <input
                                    v-model.trim="form.business_name"
                                    type="text"
                                    placeholder="e.g. iCan Careers"
                                    class="mt-2 w-full rounded-lg border px-4 py-2 text-lg outline-orange-200"
                                />
                                <InputFlashMessage
                                    :message="form.errors.business_name"
                                    type="error"
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
                                    :error="form.errors.business_logo_url"
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
                                    <option value="" disabled selected>
                                        Select industry
                                    </option>
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
                                    placeholder="Please Specify"
                                    class="mt-2 w-full rounded-lg border px-4 py-2 text-lg outline-orange-200"
                                />
                                <InputFlashMessage
                                    :message="form.errors.industry"
                                    type="error"
                                />
                            </div>

                            <!-- Company Description -->
                            <div class="my-7 flex flex-col">
                                <label
                                    class="text-lg font-semibold text-gray-800"
                                    >Company Description</label
                                >
                                <textarea
                                    v-model.trim="form.business_description"
                                    placeholder="Tell us about your company..."
                                    class="mt-2 h-[200px] w-full resize-none rounded-lg border px-4 py-2 text-lg outline-orange-200"
                                ></textarea>
                                <InputFlashMessage
                                    :message="form.errors.business_description"
                                    type="error"
                                />
                            </div>

                            <!-- Company Location -->
                            <div>
                                <label
                                    class="mb-4 block text-lg font-semibold text-gray-800"
                                    >Company Location</label
                                >
                                <Maps @update:coordinates="setMarkLocation" />
                            </div>
                        </div>
                    </div>
                </Transition>

                <!-- Form Actions -->
                <div class="mt-4 flex justify-end gap-3">
                    <button
                        type="button"
                        @click="$inertia.visit(route('employer.dashboard'))"
                        class="cursor-pointer rounded p-2 text-black"
                    >
                        <u>Skip for now</u>
                    </button>
                    <button
                        type="submit"
                        class="cursor-pointer rounded bg-[#fa8334] p-2 text-white"
                        :disabled="form.processing"
                    >
                        Save Profile
                    </button>
                </div>
            </form>
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
