<script setup>
import { uniqueId } from "lodash";
import { ref } from "vue";

const emit = defineEmits(["addstar"]);

let props = defineProps({
    id: {
        type: null,
        required: true,
    },
    starValue: {
        type: null,
        required: false,
    },
    owner: {
        type: Boolean,
        default: true,
    },
});

console.log(props.starValue + "dasda");

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
            :disabled="owner ? false : true"
            :checked="starValue ? (starValue === 5 ? true : false) : false"
        />

        <label :for="`star5_${id}`" :class="{ 'pointer-events-none': !owner }">
            <i class="bi bi-star-fill"></i>
        </label>

        <input
            @change="addStar($event.target.value)"
            type="radio"
            :name="`star_${id}`"
            :id="`star4_${id}`"
            value="4"
            :disabled="owner ? false : true"
            :checked="starValue ? (starValue === 4 ? true : false) : false"
        />

        <label :for="`star4_${id}`" :class="{ 'pointer-events-none': !owner }">
            <i class="bi bi-star-fill"></i>
        </label>

        <input
            @change="addStar($event.target.value)"
            type="radio"
            :name="`star_${id}`"
            :id="`star3_${id}`"
            value="3"
            :disabled="owner ? false : true"
            :checked="starValue ? (starValue === 3 ? true : false) : false"
        />

        <label :for="`star3_${id}`" :class="{ 'pointer-events-none': !owner }">
            <i class="bi bi-star-fill"></i>
        </label>

        <input
            @change="addStar($event.target.value)"
            type="radio"
            :name="`star_${id}`"
            :id="`star2_${id}`"
            value="2"
            :disabled="owner ? false : true"
            :checked="starValue ? (starValue === 2 ? true : false) : false"
        />

        <label :for="`star2_${id}`" :class="{ 'pointer-events-none': !owner }">
            <i class="bi bi-star-fill"></i>
        </label>

        <input
            @change="addStar($event.target.value)"
            type="radio"
            :name="`star_${id}`"
            :id="`star1_${id}`"
            value="1"
            :disabled="owner ? false : true"
            :checked="starValue ? (starValue === 1 ? true : false) : false"
        />

        <label :for="`star1_${id}`" :class="{ 'pointer-events-none': !owner }">
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
