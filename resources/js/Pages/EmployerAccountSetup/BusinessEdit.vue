<script setup>
import { useForm } from "@inertiajs/vue3";
import Layout from "../Layouts/Layout.vue";
import SetupProfileLayout from "../Layouts/SetupProfileLayout.vue";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import InputFlashMessage from "../Components/InputFlashMessage.vue";
import SubmitImage from "../Components/SubmitImage.vue";
import Maps from "../Components/Maps.vue";
import { ref, watch } from "vue";

defineOptions({
  layout: [Layout, SetupProfileLayout],
});

const props = defineProps({
  business: Object, // comes from controller
});

const form = useForm({
  business_name: props.business.business_name || "",
  business_logo: props.business.business_logo_url || null,
  industry: props.business.industry || "",
  business_description: props.business.business_description || "",
  business_location: props.business.business_location || [120.9842, 14.5995],
});

// handle industry "Other"
const otherIndustry = ref("");
const showInput = ref(false);

watch(
  () => form.industry,
  (newValue) => {
    showInput.value = newValue === "Other";
  },
);

// handle logo upload
const imageAdded = (image) => {
  form.business_logo_url = image;
};

// handle map coordinates update
const setMarkLocation = (coordinates) => {
  form.business_location = coordinates;
};

// submit form
const submit = () => {
  if (form.industry === "Other") {
    form.industry = otherIndustry.value;
  }

  form.transform((data) => ({
    ...data,
    // make sure file stays a File (not stringified)
    business_logo_url: data.business_logo_url,
  }))
  .post(route("employer.business.update", props.business.id), {
    forceFormData: true, // ✅ ensures multipart/form-data
  });
};

</script>

<template>
  <Head title="Edit Business Info | iCan Careers" />

  <div class="mb-4">
    <div class="flex justify-center">
      <div class="mt-5 w-full rounded-lg bg-white p-8">
        <h2 class="text-[24px] text-gray-900">Edit Business Information</h2>
        <p class="mb-6 text-lg text-gray-700">
          Update your company details below.
        </p>

        <form @submit.prevent="submit" class="mt-4 space-y-6">
          <!-- Business Name -->
          <div class="flex flex-col">
            <label class="text-lg font-semibold text-gray-800">
              Business / Company Name
            </label>
            <input
              v-model.trim="form.business_name"
              type="text"
              class="mt-2 w-full rounded-lg border px-4 py-2 text-lg outline-orange-200"
              placeholder="e.g. iCan Careers"
              required
            />
            <InputFlashMessage
              type="error"
              :message="form.errors.business_name"
            />
          </div>

          <!-- Business Logo -->
          <div class="my-7 flex flex-col">
            <label class="text-lg font-semibold text-gray-800">
              Business Logo
            </label>
            <SubmitImage
              @imageAdded="imageAdded"
              description="<span class='text-blue-500'><u>Upload</u></span> the business logo here"
              :error="form.errors.business_logo_url"
            />
            <div v-if="form.business_logo_url" class="mt-3">
              <img
                :src="form.business_logo_url"
                alt="Current Logo"
                class="h-16 rounded object-cover"
              />
            </div>

          </div>
          <!-- Industry -->
          <div class="flex w-full flex-col">
            <label class="text-lg font-semibold text-gray-800">
              Industry
            </label>
            <select
              v-model="form.industry"
              class="w-full max-w-md rounded-lg border px-4 py-2 text-lg outline-orange-200"
            >
              <option value="Technology and IT">Technology and IT</option>
              <option value="Remote Customer Support">
                Remote Customer Support
              </option>
              <option value="Creative and Design">Creative and Design</option>
              <option value="Accounting and Financial Services">
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
            <InputFlashMessage type="error" :message="form.errors.industry" />
          </div>

          <!-- Company Description -->
          <div class="my-7">
            <label class="text-lg font-semibold text-gray-800">
              Company Description
            </label>
            <textarea
              v-model.trim="form.business_description"
              class="mt-2 h-[200px] w-full resize-none rounded-lg border px-4 py-2 text-lg outline-orange-200"
              placeholder="Tell us about your company..."
            ></textarea>
            <InputFlashMessage
              type="error"
              :message="form.errors.business_description"
            />
          </div>

          <!-- Company Location -->
          <div>
            <label class="mb-4 block text-lg font-semibold text-gray-800">
              Company Location
            </label>
            <div class="p-1">
              <Maps @update:coordinates="setMarkLocation" />
            </div>
          </div>

          <!-- Buttons -->
          <div class="mt-4 flex justify-end gap-3">
            <button
              type="button"
              @click="$inertia.visit(route('employer.dashboard'))"
              class="cursor-pointer rounded p-2 text-black"
            >
              <u>Cancel</u>
            </button>
            <button
              type="submit"
              class="cursor-pointer rounded bg-[#fa8334] p-2 text-white"
            >
              Update Business
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
