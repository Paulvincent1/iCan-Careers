<script setup>
import { ref, computed } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";
import {
    UserIcon, IdentificationIcon, CameraIcon, CheckIcon, XCircleIcon, TrashIcon
} from "@heroicons/vue/24/outline";

defineOptions({ layout: AdminLayout });

const page = usePage();
const worker = ref(page.props.worker || {});
const verification = ref(page.props.verification || {});

// Computed property for verification status
const isVerified = computed(() => worker.value?.verified ?? false);

// Toggle verification function
const toggleVerification = () => {
    if (!worker.value?.id) return; // Prevent errors if worker data is missing

    router.put(`/admin/workers/${worker.value.id}/verify`, {}, {
        onSuccess: () => {
            worker.value.verified = !worker.value.verified;
        },
    });
};

const deleteWorker = () => {
    if (!worker.value?.id) {
        alert("Nothing to delete.");
        return;
    }

    if (confirm("Are you sure you want to delete this worker?")) {
        router.delete(`/admin/workers/${worker.value.id}/delete`, {
            onSuccess: () => {
                alert("Worker deleted successfully.");
                window.location.href = "/admin/workers";
            },
            onError: (errors) => {
                alert(errors.error || "Failed to delete worker.");
            },
        });
    }
};
</script>

<template>
    <Head title="Worker Verification | iCan Careers" />

    <div class="mx-auto max-w-4xl rounded-lg bg-white p-6 shadow-md">
        <!-- Title & Status -->
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-800">
                Worker Verification
            </h1>
            <span
                class="rounded-full px-3 py-1 text-sm font-semibold"
                :class="{
                    'bg-green-100 text-green-600': isVerified,
                    'bg-yellow-100 text-yellow-600': !isVerified,
                }"
            >
                {{ isVerified ? "Verified" : "Pending Verification" }}
            </span>
        </div>

        <!-- Handling when there's no worker data -->
        <div v-if="!worker.id" class="text-center text-red-500 font-semibold">
            <p>No worker data available.</p>
        </div>

        <!-- Worker Details Section -->
        <div v-else class="space-y-6">
            <h2 class="border-b pb-2 text-lg font-semibold text-gray-800">
                Worker Information
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="flex items-center">
                    <UserIcon class="mr-2 h-6 w-6 text-blue-500" />
                    <p class="text-gray-700">
                        <span class="font-semibold">Full Name:</span>
                        {{ verification.first_name }}
                        {{ verification.middle_name ? verification.middle_name + " " : "" }}
                        {{ verification.last_name }}
                        {{ verification.suffix ? verification.suffix : "" }}
                    </p>
                </div>

                <div class="flex items-center">
                    <IdentificationIcon class="mr-2 h-6 w-6 text-green-500" />
                    <p class="text-gray-700">
                        <span class="font-semibold">Worker ID:</span>
                        {{ worker.id }}
                    </p>
                </div>
            </div>

            <!-- Images Section -->
            <h2 class="border-b pb-2 text-lg font-semibold text-gray-800">
                Uploaded Documents
            </h2>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center justify-center gap-2">
                        <IdentificationIcon class="h-5 w-5 text-gray-700" />
                        ID Image
                    </h3>
                    <img
                        v-if="verification.id_image"
                        :src="verification.id_image"
                        alt="ID Image"
                        class="mx-auto mt-2 h-auto w-full max-w-sm rounded-lg border shadow-md"
                    />
                    <p v-else class="text-gray-500">No ID uploaded</p>
                </div>

                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center justify-center gap-2">
                        <CameraIcon class="h-5 w-5 text-gray-700" />
                        Selfie Image
                    </h3>
                    <img
                        v-if="verification.selfie_image"
                        :src="verification.selfie_image"
                        alt="Selfie Image"
                        class="mx-auto mt-2 h-auto w-full max-w-sm rounded-lg border shadow-md"
                    />
                    <p v-else class="text-gray-500">No selfie uploaded</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div v-if="worker.id" class="mt-8 flex justify-between items-center">
            <Link
                href="/admin/workers"
                class="flex items-center font-semibold text-blue-600 hover:text-blue-800"
            >
                ← Back to Workers
            </Link>

            <div class="flex gap-3">
                <!-- Verify / Unverify Button -->
                <button
                    @click="toggleVerification"
                    class="flex items-center gap-2 rounded px-4 py-2 text-white transition"
                    :class="isVerified ? 'bg-red-500 hover:bg-red-600' : 'bg-blue-500 hover:bg-blue-600'"
                >
                    <CheckIcon v-if="!isVerified" class="h-5 w-5" />
                    <XCircleIcon v-else class="h-5 w-5" />
                    {{ isVerified ? "Unverify Worker" : "Verify Worker" }}
                </button>

               <!-- Delete Button (Hidden when there's no worker data) -->
<button
    v-if="worker && worker.id"
    @click="deleteWorker"
    class="flex items-center gap-2 rounded bg-red-600 px-4 py-2 text-white transition hover:bg-red-800"
>
    <TrashIcon class="h-5 w-5" />
    Delete Worker
</button>
            </div>
        </div>
    </div>
</template>
