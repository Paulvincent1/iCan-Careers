<script setup>
import Skill from "../Components/Skill.vue";
</script>
<template>
    <div class="min-h-[calc(100vh-4.625rem)] bg-[#f3f7fa]">
        <div class="relative h-32 bg-[#FAFAFA]">
            <div
                class="absolute left-[50%] top-[32px] flex translate-x-[-50%] flex-col items-center"
            >
                <div class="mb-3 w-36">
                    <img
                        src="/assets/profile_placeholder.jpg"
                        alt=""
                        class="w-full rounded-full"
                    />
                </div>
            </div>
        </div>
        <div class="bg-white pb-2">
            <div
                class="xs container mx-auto flex flex-col items-center justify-center pt-16 xl:max-w-7xl"
            >
                <p class="mb-2">Name</p>
                <p class="text-[24px]">Job Title</p>
                <div class="flex items-center gap-1">
                    <p class="text-sm font-bold text-gray-600">Verified</p>
                    <i class="bi bi-patch-check-fill text-green-400"></i>
                </div>
            </div>
        </div>
        <div
            class="xs container mx-auto px-[0.5rem] md:px-[3rem] lg:px-[9rem] xl:max-w-7xl"
        >
            <div
                class="mt-8 grid gap-6 md:grid-cols-[1fr,250px] lg:grid-cols-[1fr,300px]"
            >
                <div class="flex flex-col gap-4 text-[16px] text-gray-600">
                    <div class="rounded-lg bg-white p-8 shadow">
                        <p class="mb-3 text-[20px]">Overview</p>
                        <div class="mb-4 flex items-center gap-4">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-200"
                            >
                                <i class="bi bi-bag font-bold"></i>
                            </div>
                            <p>
                                Looking for full-time work (8 hours/day) at
                                P900/hour (P50,000.00/month)
                            </p>
                        </div>
                        <div class="mb-4 flex items-center gap-4">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-200"
                            >
                                <i class="bi bi-mortarboard"></i>
                            </div>
                            <p>High school diploma</p>
                        </div>
                        <div class="mb-4 flex items-center gap-4">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-200"
                            >
                                <i class="bi bi-person"></i>
                            </div>
                            <div>
                                <p>Member Since</p>
                                <p>October 30th, 2023</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-lg bg-white p-8 text-gray-600 shadow">
                        <p class="mb-3 text-[20px]">Profile Description</p>
                        <p class="text-[14px]">
                            Lorem ipsum dolor sit amet consectetur adipisicing
                            elit. Cumque sit ullam pariatur sint, odio, itaque
                            ipsum maiores, eveniet veritatis voluptatibus
                            deleniti! Fugiat, saepe cupiditate? Recusandae
                            delectus nihil animi cupiditate tempora.
                        </p>
                    </div>
                    <div class="rounded-lg bg-white p-8 text-gray-600 shadow">
                        <p class="mb-3 text-[20px]">Top Skills</p>
                        <!-- <Skill /> -->
                    </div>
                </div>
                <div
                    class="self-start rounded-lg bg-white p-8 text-gray-600 shadow"
                >
                    <p class="mb-3 text-[20px]">Basic Information</p>
                    <div class="mb-2">
                        <label class="text-sm" for="">Age</label>
                        <p>19</p>
                        <!-- <input
                            type="number"
                            value="2025"
                            class="appearance-none outline-none hover:underline"
                        /> -->
                    </div>
                    <div class="mb-2">
                        <label class="text-sm" for="">Gender</label>
                        <p>Male</p>
                        <!-- <input
                            type="number"
                            value="2025"
                            class="appearance-none outline-none hover:underline"
                        /> -->
                    </div>
                    <div class="mb-2">
                        <label class="text-sm" for="">Address</label>
                        <p>19</p>
                        <!-- <input
                            type="number"
                            value="2025"
                            class="appearance-none outline-none hover:underline"
                        /> -->
                    </div>
                    <div class="mb-2">
                        <label class="text-sm" s for=""
                            >Website / Account</label
                        >
                        <p>link</p>
                        <!-- <input
                            type="number"
                            value="2025"
                            class="appearance-none outline-none hover:underline"
                        /> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
