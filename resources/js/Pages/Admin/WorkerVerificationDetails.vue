<script setup>
import { ref } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";

defineOptions({ layout: AdminLayout });

const page = usePage();
const worker = ref(page.props.worker);
const verification = ref(page.props.verification);

// Toggle verification function
const toggleVerification = () => {
    router.put(
        `/admin/workers/${worker.value.id}/verify`,
        {},
        {
            onSuccess: () => {
                worker.value.verified = !worker.value.verified;
            },
        },
    );
};

const deleteWorker = () => {
    if (confirm("Are you sure you want to delete this worker?")) {
        router.delete(`/admin/workers/${worker.value.id}/delete`, {
            onSuccess: () => {
                window.location.href = "/admin/workers"; // Force redirect to Workers page
            },
        });
    }
};
</script>

<template>
    <Head title="WorkersVerification | iCan Careers" />
    <div class="mx-auto mt-8 max-w-4xl rounded-xl bg-white p-6 shadow-lg">
        <h1 class="mb-6 text-center text-3xl font-bold text-gray-800">
            Worker Verification Details
        </h1>

        <div v-if="verification" class="space-y-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <p class="text-gray-700">
                        <strong class="text-gray-900">First Name:</strong>
                        {{ verification.first_name }}
                    </p>
                    <p class="text-gray-700">
                        <strong class="text-gray-900">Middle Name:</strong>
                        {{ verification.middle_name ?? "N/A" }}
                    </p>
                    <p class="text-gray-700">
                        <strong class="text-gray-900">Last Name:</strong>
                        {{ verification.last_name }}
                    </p>
                    <p class="text-gray-700">
                        <strong class="text-gray-900">Suffix:</strong>
                        {{ verification.suffix ?? "N/A" }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-900">
                        ID Image
                    </h3>
                    <img
                        v-if="verification.id_image"
                        :src="verification.id_image"
                        alt="ID Image"
                        class="mx-auto mt-2 h-auto w-full max-w-sm rounded-lg border shadow-md"
                    />
                </div>
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Selfie Image
                    </h3>
                    <img
                        v-if="verification.selfie_image"
                        :src="verification.selfie_image"
                        alt="Selfie Image"
                        class="mx-auto mt-2 h-auto w-full max-w-sm rounded-lg border shadow-md"
                    />
                </div>
            </div>

            <!-- Verification Toggle Button -->
            <div class="mt-6 text-center">
                <button
                    @click="toggleVerification"
                    class="rounded-lg px-6 py-2 text-white shadow-md transition"
                    :class="
                        worker.verified
                            ? 'bg-red-500 hover:bg-red-700'
                            : 'bg-blue-500 hover:bg-blue-700'
                    "
                >
                    {{ worker.verified ? "Unverify Worker" : "Verify Worker" }}
                </button>

                <!-- Delete Button -->
                <button
                    @click="deleteWorker"
                    class="rounded-lg bg-red-600 px-6 py-2 text-white shadow-md transition hover:bg-red-800"
                >
                    Delete Worker
                </button>
            </div>
        </div>

        <div v-else class="mt-4 text-center font-semibold text-red-500">
            <p>No verification details available.</p>
        </div>

        <div class="mt-6 text-center">
            <Link
                href="/admin/workers"
                class="rounded-lg bg-[#fa8334] px-6 py-2 text-white shadow-md transition"
            >
                Back to Workers
            </Link>
        </div>
    </div>
</template>
