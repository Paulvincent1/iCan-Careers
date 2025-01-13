<script setup>
import { uniqueId } from "lodash";
import { ref } from "vue";

const emit = defineEmits(["addstar"]);

let props = defineProps({
    id: String,
    required: true,
});

function addStar(star) {
    emit("addstar", star);
}
</script>
<template>
    <div class="flex flex-row-reverse text-lg">
        <input
            @change="addStar($event.target.value)"
            type="radio"
            :name="`star_${id}`"
            :id="`star5_${id}`"
            value="5"
        />

        <label :for="`star5_${id}`">
            <i class="bi bi-star-fill"></i>
        </label>

        <input
            @change="addStar($event.target.value)"
            type="radio"
            :name="`star_${id}`"
            :id="`star4_${id}`"
            value="4"
        />

        <label :for="`star4_${id}`">
            <i class="bi bi-star-fill"></i>
        </label>

        <input
            @change="addStar($event.target.value)"
            type="radio"
            :name="`star_${id}`"
            :id="`star3_${id}`"
            value="3"
        />

        <label :for="`star3_${id}`">
            <i class="bi bi-star-fill"></i>
        </label>

        <input
            @change="addStar($event.target.value)"
            type="radio"
            :name="`star_${id}`"
            :id="`star2_${id}`"
            value="2"
        />

        <label :for="`star2_${id}`">
            <i class="bi bi-star-fill"></i>
        </label>

        <input
            @change="addStar($event.target.value)"
            type="radio"
            :name="`star_${id}`"
            :id="`star1_${id}`"
            value="1"
        />

        <label :for="`star1_${id}`">
            <i class="bi bi-star-fill"></i>
        </label>
    </div>
</template>
<style scoped>
input {
    display: none;
}
label {
    color: rgb(218, 216, 216);
}

label:hover ~ label,
label:hover {
    color: rgb(245, 245, 127);
}

input:checked ~ label,
input:checked + label {
    color: yellow;
}
</style>
