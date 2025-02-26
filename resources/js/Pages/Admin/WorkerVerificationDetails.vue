<script setup>
import { usePage, router } from "@inertiajs/vue3";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import { CheckIcon, XCircleIcon } from "@heroicons/vue/24/outline";

defineOptions({ layout: AdminLayout });

const page = usePage();
const worker = page.props.worker;
const verification = page.props.verification;

// Toggle verification function
const toggleVerification = () => {
    router.put(`/admin/workers/${worker.id}/verify`, {}, {
        onSuccess: () => {
            worker.verified = !worker.verified;
        },
    });
};
</script>

<template>
    <Head :title="`Worker Details - ${worker.username}`" />
    
    <div class="mx-auto max-w-4xl rounded-lg bg-white p-6 shadow-md">
        <!-- Worker Name & Verification Status -->
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-800">
                {{ worker.username }}
            </h1>
            <span
                class="rounded-full px-3 py-1 text-sm font-semibold"
                :class="{
                    'bg-green-100 text-green-600': worker.verified,
                    'bg-red-100 text-red-600': !worker.verified,
                }"
            >
                {{ worker.verified ? "Verified" : "Not Verified" }}
            </span>
        </div>

        <!-- Worker Details Section -->
        <div class="space-y-6">
            <h2 class="border-b pb-2 text-lg font-semibold text-gray-800">Worker Information</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <p class="text-gray-700"><strong class="font-semibold">First Name:</strong> {{ verification.first_name }}</p>
                <p class="text-gray-700"><strong class="font-semibold">Middle Name:</strong> {{ verification.middle_name ?? 'N/A' }}</p>
                <p class="text-gray-700"><strong class="font-semibold">Last Name:</strong> {{ verification.last_name }}</p>
                <p class="text-gray-700"><strong class="font-semibold">Suffix:</strong> {{ verification.suffix ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Verification Images -->
        <div class="mt-6">
            <h2 class="border-b pb-2 text-lg font-semibold text-gray-800">Verification Images</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4">
                <div class="text-center">
                    <h3 class="text-md font-semibold text-gray-900">ID Image</h3>
                    <img 
                        v-if="verification.id_image" 
                        :src="verification.id_image" 
                        alt="ID Image" 
                        class="w-full max-w-sm h-auto mx-auto rounded-lg border shadow-md mt-2"
                    />
                </div>
                <div class="text-center">
                    <h3 class="text-md font-semibold text-gray-900">Selfie Image</h3>
                    <img 
                        v-if="verification.selfie_image" 
                        :src="verification.selfie_image" 
                        alt="Selfie Image" 
                        class="w-full max-w-sm h-auto mx-auto rounded-lg border shadow-md mt-2"
                    />
                </div>
            </div>
        </div>

        <!-- Verification Toggle Button -->
        <div class="mt-6 text-center">
            <button
                @click="toggleVerification"
                class="flex items-center justify-center gap-2 px-6 py-2 text-white rounded-lg shadow-md transition"
                :class="worker.verified ? 'bg-red-500 hover:bg-red-700' : 'bg-blue-500 hover:bg-blue-700'"
            >
                <CheckIcon v-if="!worker.verified" class="h-5 w-5 text-white" />
                <XCircleIcon v-else class="h-5 w-5 text-white" />
                {{ worker.verified ? "Unverify Worker" : "Verify Worker" }}
            </button>
        </div>

        <!-- Back Button -->
        <div class="mt-8">
            <a
                href="/admin/workers"
                class="flex items-center font-semibold text-blue-600 hover:text-blue-800"
            >
                ← Back to Workers
            </a>
        </div>
    </div>
</template>
