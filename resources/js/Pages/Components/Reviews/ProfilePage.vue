<script setup>
import { onMounted } from "vue";
import { route } from "../../../../../vendor/tightenco/ziggy/src/js";
import Rating from "../Rating.vue";
import { round } from "lodash";

const props = defineProps({
    visitor: null,
    averageStar: null,
    recentReview: null,
});

let userId = null;

if (props.visitor) {
    userId = route().params.id;
    console.log(userId);
}
</script>
<template>
    <div class="mb-3 rounded-lg bg-white p-8 text-gray-600 shadow">
        <div class="flex justify-between">
            <p class="mb-3 text-[20px] font-bold">Reviews</p>

            <Link
                class="underline"
                :href="
                    visitor
                        ? route('review.view-profile', {
                              id: userId,
                          })
                        : route('review.my-profile')
                "
                >See All
            </Link>
        </div>
        <p class="text-center text-[12px] text-gray-400">Overall Rating</p>
        <p class="text-center text-[20px] font-bold">{{ averageStar }}</p>
        <div class="mb-5 flex flex-col items-center justify-center">
            <Rating
                v-if="recentReview"
                disabled="true"
                :starValue="round(averageStar)"
                :id="recentReview.reviewer.id"
            ></Rating>
        </div>
        <div v-if="recentReview">
            <div>
                <div class="flex flex-col items-start">
                    <p class="font-bold">{{ recentReview.reviewer.name }}</p>
                    <Rating
                        :id="recentReview.id"
                        disabled="true"
                        :starValue="round(recentReview.star)"
                    ></Rating>
                </div>
                <p class="text-sm">
                    {{ recentReview.comment }}
                </p>
            </div>
        </div>

        <p v-if="!recentReview" class="text-center">No reviews found.</p>
    </div>
</template>
