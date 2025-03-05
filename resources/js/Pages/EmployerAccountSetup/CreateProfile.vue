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
    }, 1000)
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
    }
);

const showCompanyForm = ref(false);
watch(
    () => form.employer_type,
    (newValue) => {
        showCompanyForm.value = newValue === "business";
    }
);

const imageAdded = (image) => {
    form.business_logo = image;
};

const search = () => {
    router.get(
        "/employers/createprofile",
        { business_name: searchBusiness.value },
        { preserveScroll: true, preserveState: true }
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

    <div class="flex justify-center">
        <div class="w-full max-w-3xl bg-white shadow-md rounded-lg p-8 mt-8 border border-gray-300">
            <h2 class="text-3xl font-bold text-gray-900 text-center">Create Your Employer Profile</h2>
            <p class="text-lg text-gray-700 text-center mb-6">
                Please provide your details to create your employer profile.
            </p>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Full Name -->
                <div>
                    <label class="block text-lg font-semibold text-gray-800">Full Name</label>
                    <input
                        v-model.trim="form.full_name"
                        type="text"
                        class="w-full mt-2 border border-gray-400 bg-gray-100 rounded-lg px-4 py-3 text-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="John Doe"
                        required
                    />
                    <InputFlashMessage type="error" :message="form.errors.full_name" />
                </div>

                <!-- Phone Number -->
                <div>
                    <label class="block text-lg font-semibold text-gray-800">Phone Number</label>
                    <input
                        v-model.trim="form.phone_number"
                        type="number"
                        class="w-full mt-2 border border-gray-400 bg-gray-100 rounded-lg px-4 py-3 text-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="09123456789"
                        required
                    />
                    <InputFlashMessage type="error" :message="form.errors.phone_number" />
                </div>

                <!-- Birth Year -->
                <div>
                    <label class="block text-lg font-semibold text-gray-800">Birth Year</label>
                    <input
                        v-model.number="form.birth_year"
                        type="number"
                        min="1900"
                        :max="dayjs().year() - 18"
                        class="w-32 mt-2 border border-gray-400 bg-gray-100 rounded-lg px-4 py-3 text-lg text-center focus:ring-2 focus:ring-blue-500"
                        placeholder="YYYY"
                        required
                    />
                </div>

                <!-- Gender -->
                <div>
                    <p class="text-lg font-semibold text-gray-800">Gender</p>
                    <div class="flex items-center gap-6 mt-2">
                        <label class="flex items-center gap-2 cursor-pointer text-lg">
                            <input v-model="form.gender" type="radio" value="Male" class="w-6 h-6" />
                            <span class="text-gray-700">Male</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-lg">
                            <input v-model="form.gender" type="radio" value="Female" class="w-6 h-6" />
                            <span class="text-gray-700">Female</span>
                        </label>
                    </div>
                    <InputFlashMessage type="error" :message="form.errors.gender" />
                </div>

                <!-- Employer Type -->
                <div>
                    <label class="block text-lg font-semibold text-gray-800">What kind of employer are you?</label>
                    <select
                        v-model="form.employer_type"
                        class="w-full mt-2 border border-gray-400 bg-gray-100 rounded-lg px-4 py-3 text-lg focus:ring-2 focus:ring-blue-500"
                        required
                    >
                        <option value="business">Business / Company Employer</option>
                        <option value="individual">Individual (Freelance)</option>
                    </select>
                </div>

                <!-- Company Form (Conditional) -->
                <Transition name="fade">
                    <div v-if="showCompanyForm" class="space-y-6">
                        <h3 class="text-xl font-bold text-gray-900">Company Details</h3>

                        <!-- Search Existing Company -->
                        <div>
                            <label class="block text-lg font-semibold text-gray-800">Search Existing Company</label>
                            <input
                                v-model.trim="searchBusiness"
                                type="text"
                                class="w-full mt-2 border border-gray-400 bg-gray-100 rounded-lg px-4 py-3 text-lg focus:ring-2 focus:ring-blue-500"
                                placeholder="Search existing company"
                                autocomplete="off"
                            />
                            <div class="mt-2 h-52 w-full overflow-y-auto border border-gray-400 rounded-lg p-2">
                                <div
                                    v-for="business in bussinessProps"
                                    :key="business.id"
                                    @click="selectBusiness(business)"
                                    class="flex cursor-pointer items-center gap-3 p-2 hover:bg-gray-100 rounded-lg"
                                >
                                    <img :src=" business.business_logo " class="w-12 rounded object-cover" alt="Company Logo" />
                                    <p class="text-lg">{{ business.business_name }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Selected Company -->
                        <div v-if="businessSelected" class="flex items-center justify-between p-2 border border-gray-400 rounded-lg">
                            <div class="flex items-center gap-3">
                                <img :src="businessSelected.business_logo" class="w-12 rounded object-cover" alt="Selected Company Logo" />
                                <p class="text-lg">{{ businessSelected.business_name }}</p>
                            </div>
                            <i @click="removeSelectedBusiness" class="bi bi-x text-lg cursor-pointer hover:text-red-500"></i>
                        </div>

                        <!-- Company Information -->
                        <div v-if="!isBusinessSelected">
                            <h3 class="text-xl font-bold text-gray-900">Company Information</h3>

                            <!-- Business Name -->
                            <div>
                                <label class="block text-lg font-semibold text-gray-800">Business / Company Name</label>
                                <input
                                    v-model.trim="form.business_name"
                                    type="text"
                                    class="w-full mt-2 border border-gray-400 bg-gray-100 rounded-lg px-4 py-3 text-lg focus:ring-2 focus:ring-blue-500"
                                    placeholder="e.g. iCan Careers"
                                />
                                <InputFlashMessage type="error" :message="form.errors.business_name" />
                            </div>

                            <!-- Business Logo -->
                            <div>
                                <label class="block text-lg font-semibold text-gray-800">Business Logo</label>
                                <SubmitImage
                                    @imageAdded="imageAdded"
                                    description="Upload the business logo here"
                                    :error="form.errors.business_logo"
                                />
                            </div>

                            <!-- Industry -->
                            <div>
                                <label class="block text-lg font-semibold text-gray-800">Industry</label>
                                <select
                                    v-model="form.industry"
                                    class="w-full mt-2 border border-gray-400 bg-gray-100 rounded-lg px-4 py-3 text-lg focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="Technology and IT">Technology and IT</option>
                                    <option value="Remote Customer Support">Remote Customer Support</option>
                                    <option value="Creative and Design">Creative and Design</option>
                                    <option value="Accounting and Financial Services">Accounting and Financial Services</option>
                                    <option value="Social Media Management">Social Media Management</option>
                                    <option value="Other">Other</option>
                                </select>
                                <input
                                    v-if="showInput"
                                    v-model.trim="otherIndustry"
                                    type="text"
                                    class="w-full mt-2 border border-gray-400 bg-gray-100 rounded-lg px-4 py-3 text-lg focus:ring-2 focus:ring-blue-500"
                                    placeholder="Please Specify"
                                />
                                <InputFlashMessage type="error" :message="form.errors.industry" />
                            </div>

                            <!-- Company Description -->
                            <div>
                                <label class="block text-lg font-semibold text-gray-800">Company Description</label>
                                <textarea
                                    v-model.trim="form.business_description"
                                    class="w-full h-40 mt-2 border border-gray-400 bg-gray-100 rounded-lg px-4 py-3 text-lg focus:ring-2 focus:ring-blue-500"
                                    placeholder="Tell us about your company..."
                                ></textarea>
                                <InputFlashMessage type="error" :message="form.errors.business_description" />
                            </div>

                            <!-- Company Location -->
                            <div>
                                <label class="block text-lg font-semibold text-gray-800">Company Location</label>
                                <Maps @update:coordinates="setMarkLocation" />
                            </div>
                        </div>
                    </div>
                </Transition>

                <!-- Submit Button -->
                <div class="flex justify-center">
                    <button
                        class="w-full bg-green-500 text-white font-bold px-6 py-4 text-xl rounded-lg hover:bg-green-600 transition shadow-md"
                        type="submit"
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