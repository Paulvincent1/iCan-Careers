<script setup>
import { usePage } from "@inertiajs/vue3";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";

// Define layout
defineOptions({
    layout: AdminLayout,
});

// Fetch props from Inertia
const page = usePage();
const worker = page.props.worker;
const verification = page.props.verification;
</script>

<template>
    <Head title="WorkersVerification | iCan Careers" />
    <div class="max-w-4xl mx-auto p-6 bg-white rounded-xl shadow-lg mt-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Worker Verification Details</h1>
        
        <div v-if="verification" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-gray-700"><strong class="text-gray-900">First Name:</strong> {{ verification.first_name }}</p>
                    <p class="text-gray-700"><strong class="text-gray-900">Middle Name:</strong> {{ verification.middle_name ?? 'N/A' }}</p>
                    <p class="text-gray-700"><strong class="text-gray-900">Last Name:</strong> {{ verification.last_name }}</p>
                    <p class="text-gray-700"><strong class="text-gray-900">Suffix:</strong> {{ verification.suffix ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-900">ID Image</h3>
                    <img 
                        v-if="verification.id_image" 
                        :src="verification.id_image" 
                        alt="ID Image" 
                        class="w-full max-w-sm h-auto mx-auto rounded-lg border shadow-md mt-2"
                    />
                </div>
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-900">Selfie Image</h3>
                    <img 
                        v-if="verification.selfie_image" 
                        :src="verification.selfie_image" 
                        alt="Selfie Image" 
                        class="w-full max-w-sm h-auto mx-auto rounded-lg border shadow-md mt-2"
                    />
                </div>
            </div>
        </div>

        <div v-else class="text-center text-red-500 font-semibold mt-4">
            <p>No verification details available.</p>
        </div>

        <div class="mt-6 text-center">
            <Link href="/admin/workers" class="px-6 py-2 text-white bg-[#fa8334]  rounded-lg shadow-md transition">
                Back to Workers
            </Link>
        </div>
    </div>
</template>
