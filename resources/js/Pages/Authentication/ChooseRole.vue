<script setup>
import { useForm } from "@inertiajs/vue3";

let form = useForm({
    role: null,
});

const submit = (isEmployer) => {
    if (isEmployer) {
        form.role = "Employer";
    }
    form.post(route("store.role"));
};
</script>

<template>
    <Head title="Choose Your Role | iCan Careers" />
    <div class="min-h-[calc(100vh-4.625rem)] bg-gradient-to-br from-blue-50 to-green-50 px-4 py-8">
        <div class="container mx-auto max-w-6xl">
            <transition name="fade-slide">
                <div class="flex h-full w-full flex-col items-center justify-center">
                    <!-- Header Section -->
                    <div class="mb-12 text-center">
                        <h1 class="mb-4 text-4xl font-bold text-gray-800 md:text-5xl">
                            Welcome to iCan Careers
                        </h1>
                        <p class="text-xl text-gray-600 md:text-2xl">
                            Please select your role to get started
                        </p>
                    </div>

                    <!-- Cards Container -->
                    <div class="grid w-full max-w-5xl gap-8 md:grid-cols-2">
                        <!-- Worker Card -->
                        <div class="group relative overflow-hidden rounded-3xl bg-white shadow-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
                            <!-- Decorative Background -->
                            <div class="absolute inset-0 bg-gradient-to-br from-green-100 to-emerald-100 opacity-60"></div>
                            
                            <div class="relative p-8 md:p-10">
                                <!-- Icon -->
                                <div class="mb-6 flex justify-center">
                                    <div class="rounded-full bg-green-500 p-4 shadow-lg">
                                        <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                </div>

                                <h2 class="mb-6 text-center text-3xl font-bold text-gray-800">
                                    Looking for Work
                                </h2>

                                <p class="mb-8 text-center text-lg text-gray-600">
                                    Find opportunities that match your skills and experience
                                </p>

                                <!-- Worker Type Selection -->
                                <div class="mb-8 space-y-4">
                                    <p class="text-center text-lg font-medium text-gray-700">
                                        I am a:
                                    </p>
                                    
                                    <div class="space-y-4">
                                        <label class="flex cursor-pointer items-center rounded-xl border-2 border-gray-200 bg-white p-4 transition-all hover:border-green-300 hover:bg-green-50 focus-within:border-green-500 focus-within:ring-2 focus-within:ring-green-200">
                                            <input
                                                type="radio"
                                                value="Senior"
                                                id="senior"
                                                v-model="form.role"
                                                class="h-5 w-5 text-green-600 focus:ring-green-500 focus:ring-2"
                                            />
                                            <div class="ml-4 flex-1">
                                                <span class="text-lg font-medium text-gray-800">Senior Citizen</span>
                                                <p class="text-sm text-gray-600">Age 60 and above</p>
                                            </div>
                                        </label>

                                        <label class="flex cursor-pointer items-center rounded-xl border-2 border-gray-200 bg-white p-4 transition-all hover:border-green-300 hover:bg-green-50 focus-within:border-green-500 focus-within:ring-2 focus-within:ring-green-200">
                                            <input
                                                type="radio"
                                                value="PWD"
                                                id="pwd"
                                                v-model="form.role"
                                                class="h-5 w-5 text-green-600 focus:ring-green-500 focus:ring-2"
                                            />
                                            <div class="ml-4 flex-1">
                                                <span class="text-lg font-medium text-gray-800">Person with Disability (PWD)</span>
                                                <p class="text-sm text-gray-600">All types of disabilities welcome</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <button
                                    @click="submit(false)"
                                    :disabled="!form.role || form.role === 'Employer'"
                                    class="w-full rounded-xl bg-green-600 px-6 py-4 text-xl font-semibold text-white transition-all duration-200 hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300 disabled:cursor-not-allowed disabled:bg-gray-400 disabled:hover:bg-gray-400"
                                >
                                    <span class="flex items-center justify-center gap-2">
                                        Continue as Job Seeker
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- Employer Card -->
                        <div class="group relative overflow-hidden rounded-3xl bg-white shadow-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
                            <!-- Decorative Background -->
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-100 to-indigo-100 opacity-60"></div>
                            
                            <div class="relative p-8 md:p-10">
                                <!-- Icon -->
                                <div class="mb-6 flex justify-center">
                                    <div class="rounded-full bg-blue-600 p-4 shadow-lg">
                                        <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                </div>

                                <h2 class="mb-6 text-center text-3xl font-bold text-gray-800">
                                    Hiring Employees
                                </h2>

                                <p class="mb-8 text-center text-lg text-gray-600">
                                    Connect with talented individuals and build an inclusive workplace
                                </p>

                                <!-- Benefits List -->
                                <div class="mb-8 space-y-3">
                                    <div class="flex items-center gap-3">
                                        <div class="h-2 w-2 rounded-full bg-blue-500"></div>
                                        <span class="text-gray-700">Post job opportunities</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="h-2 w-2 rounded-full bg-blue-500"></div>
                                        <span class="text-gray-700">Access diverse talent pool</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="h-2 w-2 rounded-full bg-blue-500"></div>
                                        <span class="text-gray-700">Build inclusive teams</span>
                                    </div>
                                </div>

                                <button
                                    @click="submit(true)"
                                    class="w-full rounded-xl bg-blue-600 px-6 py-4 text-xl font-semibold text-white transition-all duration-200 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300"
                                >
                                    <span class="flex items-center justify-center gap-2">
                                        Continue as Employer
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Help Section -->
                    <div class="mt-12 rounded-2xl bg-white p-6 shadow-lg">
                        <div class="text-center">
                            <h3 class="mb-2 text-lg font-semibold text-gray-800">Need Help?</h3>
                            <p class="text-gray-600">
                                Contact our support team for assistance with registration or any questions
                            </p>
                            <div class="mt-4 flex flex-wrap justify-center gap-4 text-sm">
                                <span class="flex items-center gap-1 text-blue-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    Call Support
                                </span>
                                <span class="flex items-center gap-1 text-green-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    Email Us
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<style scoped>
.fade-slide-enter-active {
    transition: all 0.8s ease-out;
}
.fade-slide-leave-active {
    transition: all 0.4s ease-in;
}
.fade-slide-enter-from {
    opacity: 0;
    transform: translateY(30px);
}
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(-30px);
}

/* Enhanced focus styles for accessibility */
input[type="radio"]:focus {
    outline: none;
}

/* Custom radio button styling */
input[type="radio"] {
    appearance: none;
    border: 2px solid #d1d5db;
    border-radius: 50%;
    position: relative;
}

input[type="radio"]:checked {
    border-color: #059669;
    background-color: #059669;
}

input[type="radio"]:checked::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: white;
}
</style>