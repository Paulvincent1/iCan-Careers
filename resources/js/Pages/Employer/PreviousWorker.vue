<script setup>
import { Link } from "@inertiajs/vue3";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import ProfileHoverCard from "../Components/ProfileHoverCard.vue";
import ProfileJobHover from "../Components/ProfileJobHover.vue";

let props = defineProps({
    jobsProps: null,
});

console.log(props.jobsProps);
</script>
<template>
    <Head title="Previous Workers | iCan Careers" />
    <div class="h-[calc(100vh-4.625rem)] bg-[#eff2f6] pt-5 text-[#171816]">
        <div
            class="container mx-auto flex h-[90%] flex-col overflow-auto rounded bg-white p-5 xl:max-w-7xl"
        >
            <h2 class="mb-3 text-[20px]">
                Previous Workers ({{ jobsProps.length }})
            </h2>
            <table class="min-w-[800px] table-fixed md:w-full">
                <thead>
                    <tr class="text-sm text-slate-500">
                        <th class="p-3 text-center font-normal">Image</th>
                        <th class="p-3 text-center font-normal">Job Title</th>
                        <th class="p-3 text-center font-normal">Worker Name</th>
                        <th class="p-3 text-center font-normal">Status</th>
                        <th class="p-3 text-center font-normal">
                            View Profile
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(job, index) in jobsProps" :key="index">
                        <td class="p-3">
                            <div class="mx-auto h-14 w-14">
                                <Link
                                    :href="
                                        route('worker.show.profile', {
                                            id: job.job.worker.id,
                                        })
                                    "
                                >
                                    <ProfileHoverCard
                                        :user-id="job.job.worker.id"
                                    >
                                        <img
                                            :src="
                                                job.job.worker.profile_img
                                                    ? job.job.worker.profile_img
                                                    : '/assets/profile_placeholder.jpg'
                                            "
                                            class="h-14 w-14 rounded-full object-cover"
                                            alt="profile"
                                        />
                                    </ProfileHoverCard>
                                </Link>
                            </div>
                        </td>
                        <td class="p-3 text-center">
                            <Link
                                :href="
                                    route('employer.jobpost.show', {
                                        id: job.job.jobDetails.id,
                                    })
                                "
                            >
                                <ProfileJobHover
                                    :job-id="job.job.jobDetails.id"
                                >
                                    <span
                                        class="cursor-pointer font-bold text-blue-600 hover:underline"
                                    >
                                        {{ job.job.jobDetails.job_title }}
                                    </span>
                                </ProfileJobHover>
                            </Link>
                        </td>
                        <td class="p-3 text-center">
                            <Link
                                :href="
                                    route('worker.show.profile', {
                                        id: job.job.worker.id,
                                    })
                                "
                            >
                                <ProfileHoverCard :user-id="job.job.worker.id">
                                    <span
                                        class="cursor-pointer font-bold text-blue-600 hover:underline"
                                    >
                                        {{ job.job.worker.name }}
                                    </span>
                                </ProfileHoverCard>
                            </Link>
                        </td>
                        <td class="p-3">
                            <div
                                :class="[
                                    'mx-auto w-fit rounded-full px-3 py-2',
                                    job.job.worker.pivot?.current === 1
                                        ? 'bg-green-600'
                                        : 'bg-orange-600',
                                ]"
                            >
                                <p class="font-bold text-white">
                                    {{
                                        job.job.worker.pivot.current === 1
                                            ? "Current"
                                            : "Previous"
                                    }}
                                </p>
                            </div>
                        </td>
                        <td class="p-3 text-center">
                            <Link
                                :href="
                                    route('worker.show.profile', {
                                        id: job.job.worker.id,
                                    })
                                "
                            >
                                <i class="bi bi-arrow-right"></i>
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
