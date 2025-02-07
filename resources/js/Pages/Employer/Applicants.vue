<script setup>
import { Link, router, useForm } from "@inertiajs/vue3";
import { onMounted, ref, watch } from "vue";

let props = defineProps({
    applicantsProps: null,
    statusCountProps: null,
});

console.log(props.statusCountProps);
console.log(props.applicantsProps);

let applicants = ref(props.applicantsProps);

let pendingCount = ref(props.statusCountProps.Pending);
let underReviewCount = ref(props.statusCountProps["Under Review"]);
let acceptedCount = ref(props.statusCountProps.Pending);
let rejectedCount = ref(props.statusCountProps.Pending);

onMounted(() => {
    // props.applicantsProps.forEach((job) => {
    //     job;
    // });
});

// let updateStatus = ref(null);

// watch(updateStatus, (value) => {
//     if (value === "under review" || value === "rejected") {
//         console.log("hi");
//     }
// });

function updateStatus(applicationId, e) {
    if (confirm("Are you sure you want to update the status?")) {
        console.log(e.target.value);

        router.post(
            route("job.applicants.update.status", { pivotId: applicationId }),
            {
                _method: "put",
                status: e.target.value,
            },
        );
    }
}
</script>
<template>
    <div class="container mx-auto px-[0.5rem] pt-3 xl:max-w-7xl">
        <div class="mb-2">
            <h2 class="text-[32px] font-bold">Social Media Manager</h2>
        </div>
        <div class="mb-3 flex justify-between">
            <div>
                <swiper-container slides-per-view="auto" :space-between="10">
                    <swiper-slide class="w-fit">
                        <li
                            class="rounded border border-yellow-400 bg-yellow-400 p-1 text-white"
                        >
                            Pending ({{ pendingCount ?? 0 }})
                        </li></swiper-slide
                    >
                    <swiper-slide class="w-fit">
                        <li
                            class="rounded border border-slate-400 p-1 text-slate-400"
                        >
                            Under Review ({{ underReviewCount ?? 0 }})
                        </li></swiper-slide
                    >
                    <swiper-slide class="w-fit">
                        <li
                            class="rounded border border-green-400 p-1 text-green-400"
                        >
                            Accepted ({{ props.statusCountProps.Pending ?? 0 }})
                        </li></swiper-slide
                    >
                    <swiper-slide class="w-fit">
                        <li
                            class="rounded border border-red-400 p-1 text-red-400"
                        >
                            Rejected ({{ props.statusCountProps.Pending ?? 0 }})
                        </li></swiper-slide
                    >
                </swiper-container>
            </div>

            <input class="border" type="text" placeholder="Search" />
        </div>

        <div>
            <table class="w-full table-fixed">
                <thead class="">
                    <tr class="border-b">
                        <th class="p-3 text-start">Image</th>
                        <th class="p-3 text-start">Name</th>
                        <th class="p-3 text-start">Resume</th>
                        <th class="p-3 text-start">Profile</th>
                        <th class="p-3 text-start">
                            Update Application Status
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        class="border-b"
                        v-for="(applicant, index) in applicants"
                        :key="applicant.id"
                    >
                        <th class="p-3 text-start">
                            <div class="h-20 w-20">
                                <img
                                    class="w-full rounded-full object-cover"
                                    src="/assets/profile_placeholder.jpg"
                                    alt=""
                                />
                            </div>
                        </th>
                        <th class="p-3 text-start font-normal">
                            {{ applicant.name }}
                        </th>
                        <th class="p-3 text-start font-normal">
                            <a
                                class="text-blue-500 underline"
                                :href="
                                    route('show.resume', {
                                        path: applicant.worker_profile
                                            .resume_path,
                                        workerId: applicant.id,
                                    })
                                "
                                target="_blank"
                                >{{ applicant.worker_profile.resume }}</a
                            >
                        </th>
                        <th class="p-3 text-start font-normal">
                            <Link class="text-blue-500" href="/"
                                >View Profile</Link
                            >
                        </th>
                        <th class="p-3 text-start font-normal">
                            <select
                                name=""
                                id=""
                                @change="
                                    updateStatus(applicant.pivot.id, $event)
                                "
                            >
                                <option value="">Update</option>
                                <option value="under review">
                                    Under Review
                                </option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
