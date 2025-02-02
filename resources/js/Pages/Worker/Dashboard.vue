<script setup>
import { Link } from "@inertiajs/vue3";
import { onMounted } from "vue";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    isPending: {
        type: null,
    },
    savedJobsProps: null,
});

console.log(props.savedJobsProps);

onMounted(() => {
    console.log(props.isVerified);
});
</script>
<template>
    <div class="container mx-auto px-[0.5rem] xl:max-w-7xl">
        <div class="grid gap-0 pt-8 lg:grid-cols-[300px,1fr] lg:gap-10">
            <div>
                <div
                    class="mb-4 flex h-[350px] flex-col items-center justify-center rounded border p-4"
                >
                    <div class="mb-3 w-[84px]">
                        <img
                            src="assets/profile_placeholder.jpg"
                            alt=""
                            class="w-full rounded-[500px]"
                        />
                    </div>
                    <p class="font-bol mb-3">{{ user.name }}</p>
                    <Link
                        :href="route('worker.profile')"
                        as="button"
                        class="mb-3 w-full max-w-[500px] rounded-lg border px-4 py-2 font-bold"
                    >
                        View Profile
                    </Link>
                    <div
                        v-if="!$page.props.auth.worker_verified"
                        class="flex flex-col items-center"
                    >
                        <p class="mb-3 text-center text-red-500">
                            Please verify your account to apply for jobs!
                        </p>
                        <Link
                            :href="route('account.verify')"
                            as="button"
                            class="w-full rounded-lg border bg-red-500 py-2 text-white"
                        >
                            Click here to verify!
                        </Link>
                    </div>

                    <div v-if="isPending">
                        <p class="text-yellow-400">{{ isPending }}</p>
                    </div>
                </div>

                <div class="border p-4">Inbox</div>
            </div>
            <div>
                <div
                    class="mb-4 grid grid-cols-1 gap-3 rounded lg:grid-cols-[400px,1fr] xl:grid-cols-[600px,1fr]"
                >
                    <div
                        class="col-span-2 h-[350px] rounded border p-3 lg:col-span-1"
                    >
                        <div class="flex justify-between">
                            <p class="p-1 font-bold">Invoice status</p>
                            <Link href="/" class="p-1">Create invoice</Link>
                        </div>
                    </div>
                    <div class="col-span-2 rounded border p-3 lg:col-span-1">
                        <p class="font-bold">Notification</p>
                    </div>
                </div>
                <div
                    class="grid h-[300px] grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2"
                >
                    <div class="col-span-1 rounded border p-2">
                        <p
                            class="mb-3 border-b-[1px] border-gray-100 p-2 text-[14px]"
                        >
                            Saved Jobs
                        </p>

                        <div class="h-[350px] overflow-y-auto">
                            <table border="1" class="w-full border-collapse">
                                <thead>
                                    <tr class="text-gray-500">
                                        <th class="px-7 py-2 font-normal">
                                            Logo
                                        </th>
                                        <th class="px-7 py-2 font-normal">
                                            Name
                                        </th>
                                        <th class="px-7 py-2 font-normal">
                                            Details
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="job in savedJobsProps"
                                        class="text-center"
                                    >
                                        <td class="p-2">
                                            <img
                                                class="mx-auto w-12 object-cover"
                                                :src="
                                                    job.employer
                                                        .employer_profile
                                                        .business_information
                                                        ? job.employer
                                                              .employer_profile
                                                              .business_information
                                                              .business_logo
                                                        : '/assets/images.png'
                                                "
                                                alt=""
                                            />
                                        </td>
                                        <td class="p-2">
                                            Lorem ipsum dolor lorem
                                        </td>
                                        <td class="p-2">
                                            <Link
                                                :href="
                                                    route(
                                                        'jobsearch.show',
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
                                    <!-- <tr class="text-center">
                                    <td class="p-2">
                                        <img
                                            class="mx-auto w-12"
                                            src="/assets/images.png"
                                            alt=""
                                            />
                                        </td>
                                        <td class="p-2">
                                            Lorem ipsum dolor sit, amet consectetur
                                            adipisicing elit. Distinctio quas
                                        </td>
                                        <td class="p-2">Details</td>
                                    </tr> -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-span-1 border p-2">Jobs applied</div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
::-webkit-scrollbar {
    @apply w-1;
}
::-webkit-scrollbar-thumb {
    /* background-color: green; */
    @apply bg-green-200;
}
</style>
