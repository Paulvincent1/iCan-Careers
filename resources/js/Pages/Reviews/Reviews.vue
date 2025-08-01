<script setup>
import { ref } from "vue";
import ReusableModal from "../Components/Modal/ReusableModal.vue";
import SuccessfulMessage from "../Components/Popup/SuccessfulMessage.vue";
import Rating from "../Components/Rating.vue";
import { nanoid } from "nanoid";
import { useForm } from "@inertiajs/vue3";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import InputFlashMessage from "../Components/InputFlashMessage.vue";
import { faL } from "@fortawesome/free-solid-svg-icons";
import { uniqueId } from "lodash";
import { round } from "lodash";
import dayjs from "dayjs";

let props = defineProps({
    visitor: null,
    owedReviews: null,
    sortedReviews: null,
    averageStar: 0,
});

let form = useForm({
    star: null,
    comment: null,
});

const userId = route().params.id;
let starError = ref(false);

function submitReview() {
    if (!form.star) {
        starError.value = true;
        return;
    }
    form.post(
        route("review.store", {
            id: userId,
        }),
        {
            preserveScroll: true,
            onSuccess: () => {
                isShowReportModal.value = false;
                form.reset("comment");
                starError.value = false;
                showSuccessMessage();
            },
        },
    );
}

let isShowReportModal = ref(false);
let isShowSuccessfulMessage = ref(false);

function showSuccessMessage() {
    isShowSuccessfulMessage.value = true;
    setTimeout(() => {
        isShowSuccessfulMessage.value = false;
    }, 1000);
}
</script>
<template>
    <div class="xs container mx-auto pt-16 xl:max-w-7xl">
        <div class="flex flex-col items-center">
            <p class="text-center text-sm text-gray-500">Overall Review</p>
            <Rating
                :disabled="true"
                :id="uniqueId()"
                :starValue="round(averageStar)"
            ></Rating>
        </div>
        <div class="align mb-5 flex items-baseline justify-center gap-2">
            <p class="text-center text-[62px] font-bold">{{ averageStar }}</p>

            <button
                v-if="visitor && owedReviews"
                @click="isShowReportModal = true"
                class="flex h-1 w-1 items-center justify-center rounded-full bg-orange-500 p-4"
            >
                <i class="bi bi-plus-lg text-white"></i>
            </button>
        </div>

        <div
            v-if="averageStar"
            class="grid grid-cols-1 gap-5 px-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5"
        >
            <div
                v-for="(review, index) in sortedReviews"
                :key="review.id"
                class="rounded-lg p-3 shadow"
            >
                <div class="mb-3 flex flex-col items-start">
                    <Rating
                        :starSize="12"
                        :disabled="true"
                        :id="review.id"
                        :starValue="review.star"
                    ></Rating>
                    <p class="text-lg font-bold">{{ review.reviewer.name }}</p>
                    <p class="text-[12px] text-gray-500">
                        {{ dayjs(review.created_at).format("MMMM DD, YYYY") }}
                    </p>
                </div>

                <p class="text-[12px]">
                    {{ review.comment }}
                </p>
            </div>
        </div>
        <div v-else>
            <p class="text-center">No Reviews found.</p>
        </div>
    </div>
    <SuccessfulMessage
        :messageShow="isShowSuccessfulMessage"
        messageProp="Review completed!"
    ></SuccessfulMessage>
    <ReusableModal
        v-if="isShowReportModal"
        @closeModal="isShowReportModal = false"
    >
        <div
            class="w-[350px] max-w-[500px] rounded bg-white px-4 py-4 sm:w-[500px]"
        >
            <div class="mb-5 flex justify-between border-b-2 py-3">
                <p class="text-lg font-bold">Add Review</p>
                <button @click="isShowReportModal = false">
                    <i class="bi bi-x-circle text-lg"></i>
                </button>
            </div>
            <form
                @submit.prevent="submitReview"
                class="flex flex-col items-start"
            >
                <Rating :id="nanoid()" @addstar="form.star = $event"></Rating>
                <InputFlashMessage
                    v-if="starError"
                    message="Please select a star rating."
                    type="error"
                    class="mb-2"
                ></InputFlashMessage>
                <textarea
                    v-model="form.comment"
                    name="comment"
                    class="mb-5 self-stretch rounded-md border px-2 pb-6 pt-3 focus:outline-none active:border-orange-300"
                    placeholder="Write a comment"
                ></textarea>
                <div class="flex justify-end self-stretch">
                    <button
                        class="rounded-md bg-orange-500 px-4 py-2 text-white"
                    >
                        Post
                    </button>
                </div>
            </form>
        </div>
    </ReusableModal>
</template>
