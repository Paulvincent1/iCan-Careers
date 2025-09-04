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
    <Head title="Previous Employers | iCan Careers" />
    <div class="h-[calc(100vh-4.625rem)] bg-[#eff2f6] pt-5 text-[#171816]">
        <div
            class="container mx-auto flex h-[90%] flex-col overflow-auto rounded bg-white p-5 xl:max-w-7xl"
        >
            <h2 class="mb-3 text-[20px]">
                Previous Employers ({{ jobsProps.length }})
            </h2>
            <table class="min-w-[800px] table-fixed md:w-full">
                <thead>
                    <tr class="text-sm text-slate-500">
                        <th class="p-3 text-center font-normal">Image</th>
                        <th class="p-3 text-center font-normal">
                            Employer Name
                        </th>
                        <th class="p-3 text-center font-normal">Job Title</th>

                        <th class="p-3 text-center font-normal">Status</th>
                        <th class="p-3 text-center font-normal">View</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(job, index) in jobsProps"
                        :key="job.id"
                        class="text-sm"
                    >
                        <td class="p-3 text-center">
                            <Link
                                :href="
                                    route('visit.employer.profile', {
                                        id: job.employer.id,
                                    })
                                "
                                ><ProfileHoverCard :user-id="job.employer.id">
                                    <div class="mx-auto h-14 w-14">
                                        <img
                                            class="h-full w-full rounded-full"
                                            :src="
                                                job.employer.profile_img ??
                                                '/assets/profile_placeholder.jpg'
                                            "
                                            alt=""
                                        />
                                    </div> </ProfileHoverCard
                            ></Link>
                        </td>
                        <td class="p-3 text-center">
                            <Link
                                :href="
                                    route('visit.employer.profile', {
                                        id: job.employer.id,
                                    })
                                "
                            >
                                <span
                                    class="cursor-pointer font-bold text-blue-600 hover:underline"
                                >
                                    <ProfileHoverCard
                                        :user-id="job.employer.id"
                                    >
                                        {{ job.employer.name }}
                                    </ProfileHoverCard>
                                </span>
                            </Link>
                        </td>
                        <td class="p-3 text-center">
                            <Link
                                :href="
                                    route('jobsearch.show', {
                                        id: job.id,
                                        slug: job.slug,
                                    })
                                "
                                ><ProfileJobHover :job-id="job.id">
                                    <span
                                        class="cursor-pointer font-bold text-blue-600 hover:underline"
                                    >
                                        {{ job.job_title }}
                                    </span>
                                </ProfileJobHover></Link
                            >
                        </td>

                        <td class="p-3">
                            <div
                                :class="[
                                    'mx-auto w-fit rounded-full px-3 py-2',
                                    {
                                        'bg-green-600': job.pivot.current,
                                        'bg-orange-600': job.pivot.current != 1,
                                    },
                                ]"
                            >
                                <p
                                    v-if="job.pivot.current != 1"
                                    class="font-bold text-white"
                                >
                                    Previous
                                </p>
                                <p v-else class="font-bold text-white">
                                    Current
                                </p>
                            </div>
                        </td>
                        <td class="p-3 text-center">
                            <Link
                                :href="
                                    route('visit.employer.profile', {
                                        id: job.employer.id,
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
