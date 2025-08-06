<script setup>
import { ref, computed, onMounted, watch } from "vue";
import ReusableModal from "../Components/Modal/ReusableModal.vue";
import SuccessfulMessage from "../Components/Popup/SuccessfulMessage.vue";
import Rating from "../Components/Rating.vue";
import { nanoid } from "nanoid";
import { useForm } from "@inertiajs/vue3";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import InputFlashMessage from "../Components/InputFlashMessage.vue";
import { uniqueId, round, countBy } from "lodash";
import dayjs from "dayjs";

// Props from Laravel backend
const props = defineProps({
    visitor: null,
    owedReviews: null,
    sortedReviews: null,
    averageStar: 0,
});

// Reactive form object
const form = useForm({ star: null, comment: "" });

const starError = ref(false);
const isShowReportModal = ref(false);
const isShowSuccessfulMessage = ref(false);

const selectedSort = ref("highest");
const selectedStarFilter = ref(null);
const SORT_KEY = "review_sort";

// Label text for stars
const starLabels = {
    1: "Terrible",
    2: "Poor",
    3: "Fair",
    4: "Good",
    5: "Amazing",
};

const starFeedback = computed(() => (form.star ? starLabels[form.star] : ""));

// Star breakdown count
const starBreakdown = computed(() => {
    if (!props.sortedReviews) return {};
    const count = countBy(props.sortedReviews, "star");
    return {
        5: count[5] || 0,
        4: count[4] || 0,
        3: count[3] || 0,
        2: count[2] || 0,
        1: count[1] || 0,
    };
});

// Sorting reviews
const sortedReviews = computed(() => {
    if (!props.sortedReviews) return [];
    let reviews = [...props.sortedReviews];

    if (selectedStarFilter.value) {
        reviews = reviews.filter(
            (review) => review.star === selectedStarFilter.value,
        );
    }

    switch (selectedSort.value) {
        case "highest":
            return reviews.sort((a, b) => b.star - a.star);
        case "lowest":
            return reviews.sort((a, b) => a.star - b.star);
        case "newest":
            return reviews.sort(
                (a, b) => new Date(b.created_at) - new Date(a.created_at),
            );
        case "oldest":
            return reviews.sort(
                (a, b) => new Date(a.created_at) - new Date(b.created_at),
            );
        default:
            return reviews;
    }
});

const userId = route().params.id;

function submitReview() {
    if (!form.star) {
        starError.value = true;
        return;
    }
    form.post(route("review.store", { id: userId }), {
        preserveScroll: true,
        onSuccess: () => {
            isShowReportModal.value = false;
            form.reset("comment");
            form.star = null;
            starError.value = false;
            showSuccessMessage();
        },
    });
}

function showSuccessMessage() {
    isShowSuccessfulMessage.value = true;
    setTimeout(() => {
        isShowSuccessfulMessage.value = false;
    }, 1500);
}

onMounted(() => {
    const saved = localStorage.getItem(SORT_KEY);
    if (saved) selectedSort.value = saved;
});

watch(selectedSort, (val) => {
    localStorage.setItem(SORT_KEY, val);
});

const editingReviewId = ref(null);
const editForm = useForm({ star: null, comment: "" });

function startEdit(review) {
    editingReviewId.value = review.id;
    editForm.star = review.star;
    editForm.comment = review.comment;
}

function cancelEdit() {
    editingReviewId.value = null;
    editForm.reset();
}

function updateReview(id) {
    editForm.put(route("review.update", id), {
        preserveScroll: true,
        onSuccess: () => {
            editingReviewId.value = null;
        },
    });
}

function deleteReview(id) {
    if (confirm("Are you sure you want to delete this review?")) {
        form.delete(route("review.destroy", id), {
            preserveScroll: true,
        });
    }
}
</script>

<template>
    <div class="container mx-auto max-w-7xl px-4 pt-20">
        <!-- Header with Rating -->
        <div class="flex flex-col items-center text-center">
            <p class="text-sm font-medium text-gray-500">Overall Rating</p>
            <p class="mt-1 text-6xl font-extrabold text-orange-500">
                {{ averageStar.toFixed(1) }} out of 5
            </p>
            <Rating
                :disabled="true"
                :id="uniqueId()"
                :starValue="round(averageStar)"
                class="mt-2"
            />
            <!-- Star Breakdown Filter -->
            <div
                v-if="Object.keys(starBreakdown).length"
                class="mt-10 flex flex-wrap justify-center gap-3"
            >
                <button
                    v-for="star in [5, 4, 3, 2, 1]"
                    :key="star"
                    @click="selectedStarFilter = star"
                    class="flex items-center gap-2 rounded-full border px-4 py-1.5 text-sm font-medium shadow transition"
                    :class="
                        selectedStarFilter === star
                            ? 'border-orange-500 bg-orange-500 text-white'
                            : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-100'
                    "
                >
                    {{ star }}★ — {{ starBreakdown[star] }}
                </button>

                <button
                    v-if="selectedStarFilter"
                    @click="selectedStarFilter = null"
                    class="flex items-center gap-2 rounded-full bg-gray-200 px-4 py-1.5 text-sm text-gray-800 hover:bg-gray-300"
                >
                    ✕ Clear Filter
                </button>
            </div>

            <button
                v-if="visitor && owedReviews"
                @click="isShowReportModal = true"
                class="mt-6 flex items-center gap-2 rounded-md bg-orange-500 px-6 py-2.5 text-sm font-medium text-white shadow-lg transition hover:bg-orange-600"
            >
                <i class="bi bi-plus-lg"></i>
                Add Review
            </button>
        </div>

        <!-- Reviews Section -->
        <div class="mt-12">
            <h2 class="mb-6 text-xl font-semibold text-gray-800">
                Recent Reviews
            </h2>

            <p
                class="mt-4 text-center text-sm text-gray-600"
                v-if="selectedStarFilter"
            >
                Showing only
                <strong>{{ selectedStarFilter }}-star</strong> reviews
            </p>

            <div
                v-if="averageStar"
                class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <div
                    v-for="review in sortedReviews"
                    :key="review.id"
                    class="rounded-xl border border-gray-100 bg-white p-5 shadow-md transition hover:shadow-lg"
                >
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0">
                            <div
                                class="h-10 w-10 overflow-hidden rounded-full bg-gray-200"
                            >
                                <img
                                    v-if="review.reviewer.profile_img"
                                    :src="review.reviewer.profile_img"
                                    alt="Reviewer photo"
                                    class="h-full w-full object-cover"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center bg-orange-500 font-bold text-white"
                                >
                                    {{ review.reviewer.name.charAt(0) }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <p class="text-base font-bold text-gray-900">
                                {{ review.reviewer.name }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{
                                    dayjs(review.created_at).format(
                                        "MMMM DD, YYYY",
                                    )
                                }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <Rating
                            :starSize="14"
                            :disabled="true"
                            :id="review.id"
                            :starValue="review.star"
                        />
                    </div>

                    <p class="mt-3 text-sm leading-relaxed text-gray-700">
                        {{ review.comment }}
                    </p>
                </div>
            </div>

            <div v-else class="mt-10 text-center text-gray-500">
                <p>No reviews yet. Be the first to leave one!</p>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    <SuccessfulMessage
        :messageShow="isShowSuccessfulMessage"
        messageProp="Review completed!"
    />

    <!-- Modal -->
    <ReusableModal
        v-if="isShowReportModal"
        @closeModal="isShowReportModal = false"
    >
        <div
            class="mx-auto w-full rounded-2xl bg-white p-8 shadow-2xl transition-all sm:w-[95%] lg:w-[800px]"
        >
            <!-- Header -->
            <div class="mb-6 flex items-center justify-between border-b pb-4">
                <h3 class="text-2xl font-bold text-gray-800">Leave a Review</h3>
                <button @click="isShowReportModal = false">
                    <i
                        class="bi bi-x-lg text-lg text-gray-600 hover:text-gray-900"
                    ></i>
                </button>
            </div>

            <!-- Form -->
            <form @submit.prevent="submitReview" class="space-y-6">
                <!-- Star Rating + Label -->
                <div class="flex items-center gap-4">
                    <Rating :id="nanoid()" @addstar="form.star = $event" />
                    <p
                        v-if="form.star"
                        class="whitespace-nowrap text-sm font-medium text-orange-600"
                    >
                        {{ starFeedback }}
                    </p>
                </div>

                <InputFlashMessage
                    v-if="starError"
                    message="Please select a star rating."
                    type="error"
                    class="-mt-2"
                />

                <!-- Comment -->
                <div>
                    <textarea
                        v-model="form.comment"
                        name="comment"
                        rows="5"
                        class="w-full resize-none rounded-md border border-gray-300 p-4 text-sm placeholder-gray-400 focus:border-orange-500 focus:outline-none focus:ring-orange-500"
                        placeholder="Write your thoughts about this experience..."
                    ></textarea>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="rounded-md bg-orange-500 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-orange-600"
                    >
                        Submit Review
                    </button>
                </div>
            </form>
        </div>
    </ReusableModal>
</template>

<style scoped>
textarea::placeholder {
    color: #a0aec0;
}
</style>
