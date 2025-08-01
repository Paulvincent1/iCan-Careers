<script setup>
import { route } from "../../../../vendor/tightenco/ziggy/src/js";

let props = defineProps({
    jobsProps: null,
});

console.log(props.jobsProps);
</script>
<template>
    <div class="h-[calc(100vh-4.625rem)] bg-[#eff2f6] pt-5 text-[#171816]">
        <div
            class="container mx-auto flex h-[90%] flex-col rounded bg-white p-5 xl:max-w-7xl"
        >
            <h2 class="mb-3 text-[20px]">
                Previous Workers ({{ jobsProps.length }})
            </h2>
            <table class="min-w-[800px] table-fixed md:w-full">
                <thead>
                    <tr class="text-sm text-slate-500">
                        <th class="p-3 text-center font-normal">Image</th>
                        <th class="p-3 text-center font-normal">Worker Name</th>
                        <th class="p-3 text-center font-normal">Status</th>
                        <th class="p-3 text-center font-normal">
                            View Profile
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(worker, index) in jobsProps" :key="index">
                        <td class="p-3">
                            <div class="mx-auto h-14 w-14">
                                <img
                                    class="h-full w-full rounded-full"
                                    :src="
                                        worker.profile_img ??
                                        '/assets/profile_placeholder.jpg'
                                    "
                                    alt="Profile"
                                />
                            </div>
                        </td>
                        <td class="p-3 text-center">{{ worker.name }}</td>
                        <td class="p-3">
                            <div
                                :class="[
                                    'mx-auto w-fit rounded-full px-3 py-2',
                                    worker.pivot?.current === 1
                                        ? 'bg-green-600'
                                        : 'bg-orange-600',
                                ]"
                            >
                                <p class="font-bold text-white">
                                    {{
                                        worker.pivot?.current === 1
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
                                        id: worker.id,
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
