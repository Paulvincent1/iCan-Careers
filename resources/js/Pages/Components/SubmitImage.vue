<script setup>
import { ref, onMounted } from "vue";
import InputFlashMessage from "./InputFlashMessage.vue";

let props = defineProps({
    id: null,
    description: {
        type: String,
        default: "Upload the image here",
    },
    error: {
        type: String,
    },
    initialImage: {
        type: String,
        default: null
    }
});

let emit = defineEmits(["imageAdded"]);
let avatarSelfieID = ref("");

// Set initial image if provided
onMounted(() => {
    if (props.initialImage) {
        avatarSelfieID.value = props.initialImage;
    }
});

function imageChange(e) {
    if (e.target.files[0]) {
        emit("imageAdded", e.target.files[0]);
        avatarSelfieID.value = URL.createObjectURL(e.target.files[0]);
    }
}
</script>

<template>
    <div class="w-full max-w-[400px]">
        <label
            :for="id"
            class="relative mb-3 flex h-[200px] w-full flex-1 cursor-pointer justify-center rounded border-2 border-dashed p-4"
        >
            <img
                v-if="avatarSelfieID"
                :src="avatarSelfieID"
                alt=""
                class="absolute inset-0 h-[100%] w-[100%] object-cover"
            />
            <div v-else class="mt-9 flex flex-col items-center">
                <svg
                    class="mb-3"
                    xmlns="http://www.w3.org/2000/svg"
                    width="48"
                    height="48"
                    viewBox="0 0 48 48"
                >
                    <g fill="#000" fill-rule="nonzero" opacity=".203">
                        <path
                            d="M39.429 4.286H7.714V2.143C7.714.959 8.602 0 9.696 0h27.75c1.095 0 1.983.96 1.983 2.143v2.143zM46 7.714H2c-1.105 0-2 .902-2 2.015v36.257C0 47.098.895 48 2 48h44c1.105 0 2-.902 2-2.014V9.729a2.007 2.007 0 0 0-2-2.015zm-32 8.057c2.21 0 4 1.804 4 4.029 0 2.225-1.79 4.029-4 4.029s-4-1.804-4-4.029c0-2.225 1.79-4.029 4-4.029zM10 37.93L30 19.8l10 18.129H10z"
                        />
                    </g>
                </svg>
                <p class="text-center text-slate-500" v-html="description"></p>
            </div>
            <input
                :id="id"
                @input="imageChange"
                accept="image/*"
                type="file"
                class="hidden"
            />
        </label>
        <InputFlashMessage class="text-center" type="error" :message="error" />
    </div>
</template>