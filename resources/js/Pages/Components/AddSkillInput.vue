<script setup>
import { debounce } from "lodash";
import { ref, watch } from "vue";

let query = ref("");
let data = ref("");
const emit = defineEmits(["addSkill"]);

document.addEventListener("click", (event) => {
    isOpen.value = false;
});

var myHeaders = new Headers();
myHeaders.append("apikey", "b2T1prX5rbN9RJaBSBBtZtevFlvCwFrs");

var requestOptions = {
    method: "GET",
    redirect: "follow",
    headers: myHeaders,
};

let isOpen = ref(false);
// watch(
//     query,
//     debounce(async (e) => {
//         isOpen.value = false;
//         if (query.value.length) {
//             try {
//                 console.log(data.length);
//                 let response = await fetch(
//                     `https://api.apilayer.com/skills?q=${e}`,
//                     requestOptions,
//                 );
//                 data = await response.json();

//                 isOpen.value = true;

//                 console.log(data);
//             } catch (error) {
//                 console.error(error);
//             }
//         }
//     }, 500),
// );

function addSkill(name) {
    emit("addSkill", name);
    query.value = "";
}
</script>
<template>
    <div class="relative mb-4">
        <div class="relative">
            <input
                v-model="query"
                type="text"
                placeholder="ex. Social Media Manager"
                class="mt-3 w-full rounded border p-3 outline-blue-400"
            />
            <i
                @click="addSkill(query)"
                class="bi bi-plus-lg absolute right-3 top-[50%] translate-y-[-20%] cursor-pointer text-lg font-bold text-orange-500"
            ></i>
        </div>

        <div
            v-if="isOpen"
            :class="[
                'absolute left-0 h-20 w-full overflow-auto rounded bg-slate-500 p-3 text-white',
                {
                    'h-fit': !data.length || data.length <= 5,
                },
            ]"
        >
            <p v-show="!data.length">No Skills found</p>
            <p
                v-for="(res, index) in data"
                :key="index"
                class="cursor-pointer p-1"
                @click="addSkill(res)"
            >
                {{ res }}
            </p>
        </div>
    </div>
</template>
