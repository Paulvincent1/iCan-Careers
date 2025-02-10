<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import { onMounted, ref } from "vue";

let props = defineProps({
    jobsProps: null,
});
let page = usePage();
let jobs = ref(null);

onMounted(() => {
    jobs.value = props.jobsProps.filter((job) => {
        return job.job_status === "Open";
    });
});

let jobTag = ref(
    page.props.auth.user.employer.subscription.subscription_type === "Free"
        ? "Pending"
        : "Open",
);
// let jobTag = ref(page.props.auth.user.employer.subscription);

function switchJobTag(jobstatus) {
    jobs.value = props.jobsProps.filter((job) => {
        return job.job_status === jobstatus;
    });
    jobTag.value = jobstatus;
}

console.log(props.jobsProps);
</script>
<template>
    <div class="xs container mx-auto px-[0.5rem] xl:max-w-7xl">
        <div class="grid gap-0 pt-8 lg:grid-cols-[300px,1fr] lg:gap-10">
            <div>
                <div
                    class="mb-4 flex flex-col items-center justify-center rounded border p-4"
                >
                    <div class="mb-3 mt-4 w-[84px]">
                        <img
                            src="assets/profile_placeholder.jpg"
                            alt=""
                            class="w-full rounded-[500px]"
                        />
                    </div>
                    <p class="font-bol mb-3">{{ page.props.auth.user.name }}</p>
                    <Link
                        :href="route('worker.profile')"
                        as="button"
                        class="mb-3 w-full max-w-[500px] rounded-lg border px-4 py-2 font-bold"
                    >
                        View Profile
                    </Link>
                    <div class="flex flex-col items-center">
                        <p class="mb-3 text-center text-red-500">
                            Your account is currently on free tier, Please
                            upgrade to pro tier to hire workers.
                        </p>
                        <!-- <Link
                            :href="route('account.verify')"
                            as="button"
                            class="w-full rounded-lg border bg-red-500 py-2 text-white"
                        >
                            See Pricing
                        </Link> -->
                    </div>
                </div>

                <div class="border p-4">Inbox</div>
            </div>
            <div
                class="grid grid-cols-1 gap-2 rounded lg:grid-cols-[400px,1fr] xl:grid-cols-[600px,1fr]"
            >
                <div class="col-span-2 rounded border p-3 lg:col-span-1">
                    <div>
                        <div class="flex justify-between">
                            <p class="p-1 font-bold">Job status</p>
                            <Link
                                :href="route('create.job')"
                                class="p-1 text-blue-500"
                                >Post job</Link
                            >
                        </div>

                        <swiper-container
                            class="mb-3 text-[12px]"
                            slides-per-view="auto"
                            :space-between="10"
                        >
                            <swiper-slide
                                v-if="
                                    page.props.auth.user.employer.subscription
                                        .subscription_type === 'Free'
                                "
                                class="w-fit"
                            >
                                <li
                                    @click="switchJobTag('Pending')"
                                    :class="[
                                        'cursor-pointer rounded border border-yellow-500 p-1',
                                        {
                                            'bg-yellow-500 text-white':
                                                jobTag === 'Pending',
                                            'text-yellow-500':
                                                jobTag != 'Pending',
                                        },
                                    ]"
                                >
                                    Pending Jobs
                                </li></swiper-slide
                            >
                            <swiper-slide class="w-fit">
                                <li
                                    @click="switchJobTag('Open')"
                                    :class="[
                                        'cursor-pointer rounded border border-green-400 p-1 text-green-400',
                                        {
                                            'bg-green-400 text-white':
                                                jobTag === 'Open',
                                        },
                                    ]"
                                >
                                    Open Jobs
                                </li></swiper-slide
                            >
                            <swiper-slide class="w-fit">
                                <li
                                    @click="switchJobTag('Closed')"
                                    :class="[
                                        'cursor-pointer rounded border border-red-400 p-1 text-red-400',
                                        {
                                            'bg-red-400 text-white':
                                                jobTag === 'Closed',
                                        },
                                    ]"
                                >
                                    Close Jobs
                                </li></swiper-slide
                            >
                        </swiper-container>
                    </div>

                    <div class="h-[350px] overflow-y-auto">
                        <table class="w-full border-collapse">
                            <thead class="bg-slate-200 text-slate-500">
                                <tr>
                                    <th class="p-2 text-start font-normal">
                                        Job Title
                                    </th>
                                    <th class="p-2 text-start font-normal">
                                        Job Type
                                    </th>
                                    <th class="p-2 text-start font-normal">
                                        Salary
                                    </th>
                                    <th class="p-2 text-start font-normal">
                                        Applications
                                    </th>
                                    <th class="p-2 text-start font-normal">
                                        Details
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(job, index) in jobs" :key="job.id">
                                    <td class="p-2 text-start">
                                        {{ job.job_title }}
                                    </td>
                                    <td class="p-2 text-start">
                                        {{ job.job_type }}
                                    </td>
                                    <td class="p-2 text-start">
                                        {{ job.salary }}
                                    </td>
                                    <td
                                        :class="[
                                            'p-2 text-start text-blue-500 underline',
                                            {
                                                'pointer-events-none text-black no-underline':
                                                    jobTag === 'Pending',
                                            },
                                        ]"
                                    >
                                        <Link
                                            class=""
                                            :href="
                                                route('job.applicants', job.id)
                                            "
                                        >
                                            {{ job.users_who_applied.length }}
                                        </Link>
                                    </td>
                                    <td class="p-2 text-start">
                                        <Link
                                            :href="
                                                route(
                                                    'employer.jobpost.show',
                                                    job.id,
                                                )
                                            "
                                            as="button"
                                            class="rounded bg-green-500 px-2 py-1 text-white"
                                        >
                                            <i
                                                class="bi bi-box-arrow-up-right"
                                            ></i>
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div
                    class="col-span-2 h-[432px] rounded border p-3 lg:col-span-1"
                >
                    <p class="font-bold">Pending Invoices</p>
                </div>
                <div class="col-span-2 h-[300px] rounded border p-3">
                    <p>Currently hired workers</p>
                </div>
            </div>
        </div>
    </div>
</template>
