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
import ProfileHoverCard from "../Components/ProfileHoverCard.vue";

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
const isShowEditModal = ref(false);
const editStarFeedback = computed(() => (editForm.star ? starLabels[editForm.star] : ""));

const MAX_COMMENT_LENGTH = 250; // adjust to match your backend rule

const commentWarning = computed(() => {
    return form.comment.length > MAX_COMMENT_LENGTH
        ? `You have exceeded the ${MAX_COMMENT_LENGTH}-character limit.`
        : "";
});

const editCommentWarning = computed(() => {
    return editForm.comment.length > MAX_COMMENT_LENGTH
        ? `You have exceeded the ${MAX_COMMENT_LENGTH}-character limit.`
        : "";
});




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
    isShowEditModal.value = true; // show modal
}


function cancelEdit() {
    editingReviewId.value = null;
    editForm.reset();
    isShowEditModal.value = false;
}

function updateReview(id) {
    editForm.put(route("review.update", id), {
        preserveScroll: true,
        onSuccess: () => {
            editingReviewId.value = null;
            isShowEditModal.value = false; // close modal
            showSuccessMessage("Review updated successfully!");
        },
    });
}


function deleteReview(id) {
    if (confirm("Are you sure you want to delete this review?")) {
        form.delete(route("review.destroy", id), {
            preserveScroll: true,
            onSuccess: () => {
                showSuccessMessage("Review deleted successfully!");
            },
        });
    }
}
</script>

<template>
    <Head title="Reviews | iCan Careers" />
    <div class="container mx-auto max-w-6xl px-4 pt-20">
        <!-- Overall Rating -->
        <div class="flex flex-col items-center text-center bg-white rounded-xl p-8 shadow-sm">
            <p class="text-gray-500 text-sm">Overall Rating</p>
            <p class="mt-2 text-6xl font-extrabold text-orange-500">
                {{ averageStar.toFixed(1) }}
            </p>
            <Rating
                :disabled="true"
                :id="`rating_${$page.props.auth.user.authenticated.id ?? 0}`"
                :starValue="round(averageStar)"
                class="mt-2"
            />
            <p class="text-gray-600 mt-1 text-sm">out of 5</p>

            <!-- Star Breakdown Bars -->
            <div class="mt-6 w-full max-w-md space-y-3">
                <div
                    v-for="star in [5,4,3,2,1]"
                    :key="star"
                    class="flex items-center gap-3"
                >
                    <span class="w-10 text-sm text-gray-700">{{ star }}★</span>
                    <div class="flex-1 h-2 rounded-full bg-gray-200 overflow-hidden">
                        <div
                            class="h-full bg-orange-500"
                            :style="{ width: (starBreakdown[star] / props.sortedReviews.length * 100 || 0) + '%' }"
                        ></div>
                    </div>
                    <span class="w-6 text-sm text-gray-500">{{ starBreakdown[star] }}</span>
                </div>
            </div>

            <!-- Filter Pills -->
            <div class="mt-6 flex flex-wrap justify-center gap-2">
                <button
                    v-for="star in [5,4,3,2,1]"
                    :key="star"
                    @click="selectedStarFilter = star"
                    class="px-4 py-1.5 rounded-full text-sm font-medium border transition"
                    :class="selectedStarFilter === star
                        ? 'bg-orange-500 text-white border-orange-500'
                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
                >
                    {{ star }}★ ({{ starBreakdown[star] }})
                </button>
                <button
                    v-if="selectedStarFilter"
                    @click="selectedStarFilter = null"
                    class="px-4 py-1.5 rounded-full bg-gray-200 text-sm text-gray-700 hover:bg-gray-300"
                >
                    ✕ Clear
                </button>
            </div>

            <button
                v-if="visitor && owedReviews"
                @click="isShowReportModal = true"
                class="mt-6 px-6 py-2.5 rounded-md bg-orange-500 text-sm font-semibold text-white shadow hover:bg-orange-600"
            >
                + Write a Review
            </button>
        </div>

        <!-- Review List -->
        <div class="mt-12">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Recent Reviews</h2>

            <p v-if="selectedStarFilter" class="text-sm text-gray-600 mb-4">
                Showing only <strong>{{ selectedStarFilter }}-star</strong> reviews
            </p>

            <div v-if="averageStar" class="space-y-6">
                <div
                    v-for="review in sortedReviews"
                    :key="review.id"
                    class="p-5 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition"
                >
                    <!-- Reviewer Info -->
                    <div class="flex items-center gap-3">
                        <ProfileHoverCard :user-id="review.reviewer.id">
                            <img
                                :src="review.reviewer.profile_img ?? '/assets/profile_placeholder.jpg'"
                                alt="Reviewer"
                                class="h-10 w-10 rounded-full object-cover border-2 border-gray-200 shadow-sm cursor-pointer"
                            />
                        </ProfileHoverCard>
                        <div v-if="review.reviewer.id === $page.props.auth.user.id" class="flex gap-3">
                            <button
                                v-if="editingReviewId !== review.id"
                                @click="startEdit(review)"
                                class="text-blue-600 hover:text-blue-800"
                                title="Edit"
                            >
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button
                                @click="deleteReview(review.id)"
                                class="text-red-600 hover:text-red-800"
                                title="Delete"
                            >
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>

                        <div>
                            <ProfileHoverCard   :user-id="review.reviewer.id">
                                <p class="font-semibold text-gray-800 cursor-pointer hover:underline">
                                    {{ review.reviewer.name }}
                                </p>
                            </ProfileHoverCard>
                            <p class="text-xs text-gray-500">
                                {{ dayjs(review.created_at).format("MMM DD, YYYY") }}
                            </p>
                        </div>
                    </div>

                    <!-- Star Rating -->
                    <div class="mt-2">
                        <Rating
                            :starSize="14"
                            :disabled="true"
                            :id="review.id"
                            :starValue="review.star"
                        />
                    </div>

                    <p class="mt-3 text-sm text-gray-700 leading-relaxed break-words max-h-40 overflow-y-auto">
    {{ review.comment }}
</p>



                </div>
            </div>

            <div v-else class="text-center text-gray-500 mt-10">
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
    <ReusableModal v-if="isShowReportModal" @closeModal="isShowReportModal = false">
        <div class="mx-auto w-full rounded-xl bg-white p-6 sm:w-[95%] lg:w-[600px]">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="text-lg font-bold text-gray-800">Write a Review</h3>
                <button @click="isShowReportModal = false" class="text-gray-600 hover:text-gray-900">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form @submit.prevent="submitReview" class="mt-5 space-y-5">
                <!-- Star Rating -->
                <div class="flex items-center gap-4">
                    <Rating :id="nanoid()" @addstar="form.star = $event" />
                    <p v-if="form.star" class="text-orange-600 text-sm font-medium">
                        {{ starFeedback }}
                    </p>
                </div>
                <InputFlashMessage
                    v-if="starError"
                    message="Please select a star rating."
                    type="error"
                />

                <!-- Comment -->
                <textarea
                    v-model="form.comment"
                    rows="4"
                    :maxlength="MAX_COMMENT_LENGTH"
                    class="w-full resize-none rounded-md border border-gray-300 p-3 text-sm focus:border-orange-500 focus:ring-orange-500"
                    placeholder="Share details of your experience... (max 250 characters)"
                ></textarea>
                <p class="text-xs text-gray-500 text-right"
                :class="{ 'text-red-500 font-semibold': form.comment.length > MAX_COMMENT_LENGTH }">
                    {{ form.comment.length }}/{{ MAX_COMMENT_LENGTH }}
                </p>
                <p v-if="commentWarning" class="text-xs text-red-500 mt-1">
                    {{ commentWarning }}
                </p>


                <!-- Submit -->
                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="px-6 py-2 rounded-md bg-orange-500 text-sm font-semibold text-white hover:bg-orange-600"
                    >
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </ReusableModal>
    <ReusableModal v-if="isShowEditModal" @closeModal="cancelEdit">
    <div class="mx-auto w-full rounded-xl bg-white p-6 sm:w-[95%] lg:w-[600px]">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="text-lg font-bold text-gray-800">Edit Review</h3>
            <button @click="cancelEdit" class="text-gray-600 hover:text-gray-900">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form @submit.prevent="updateReview(editingReviewId)" class="mt-5 space-y-5">
            <!-- Star Rating -->
            <div class="flex items-center gap-4">
                <Rating
                    :id="`edit_${editingReviewId}`"
                    :starValue="editForm.star"
                    @addstar="editForm.star = $event"
                />
                <p v-if="editForm.star" class="text-orange-600 text-sm font-medium">
                    {{ editStarFeedback }}
                </p>
            </div>


            <!-- Comment -->
            <textarea
                v-model="editForm.comment"
                rows="4"
                :maxlength="MAX_COMMENT_LENGTH"
                class="w-full resize-none rounded-md border border-gray-300 p-3 text-sm focus:border-orange-500 focus:ring-orange-500"
                placeholder="Update your review... (max 250 characters)"
            ></textarea>
            <p class="text-xs text-gray-500 text-right"
            :class="{ 'text-red-500 font-semibold': editForm.comment.length > MAX_COMMENT_LENGTH }">
                {{ editForm.comment.length }}/{{ MAX_COMMENT_LENGTH }}
            </p>
            <p v-if="editCommentWarning" class="text-xs text-red-500 mt-1">
                {{ editCommentWarning }}
            </p>


            <!-- Buttons -->
            <div class="flex justify-end gap-2">
                <button
                    type="button"
                    @click="cancelEdit"
                    class="px-4 py-2 rounded-md bg-gray-300 text-sm"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="px-6 py-2 rounded-md bg-green-500 text-sm font-semibold text-white hover:bg-green-600"
                    :disabled="editForm.processing"
                >
                    Save
                </button>
            </div>
        </form>
    </div>
</ReusableModal>

</template>
