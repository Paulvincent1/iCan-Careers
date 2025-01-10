<script setup>
import { ref, useTemplateRef, watch } from "vue";
import Breadcrumbs from "../Components/Breadcrumbs.vue";
import SetupProfileLayout from "../Layouts/SetupProfileLayout.vue";
import Layout from "../Layouts/Layout.vue";
import { debounce } from "lodash";

defineOptions({
    layout: [Layout, SetupProfileLayout],
});

document.addEventListener("click", (event) => {
    isOpen.value = false;
});

let query = ref("");
let data = ref("");

var myHeaders = new Headers();
myHeaders.append("apikey", "b2T1prX5rbN9RJaBSBBtZtevFlvCwFrs");

var requestOptions = {
    method: "GET",
    redirect: "follow",
    headers: myHeaders,
};

let isOpen = ref(false);
watch(
    query,
    debounce(async (e) => {
        isOpen.value = false;
        try {
            console.log(data.length);
            let response = await fetch(
                `https://api.apilayer.com/skills?q=${e}`,
                requestOptions,
            );
            data = await response.json();

            isOpen.value = true;

            console.log(data);
        } catch (error) {
            console.error(error);
        }
    }, 500),
);

let skills = ref([]);

function addSkill(name) {
    skills.value.push(name);
}
</script>
<template>
    <div class="mt-9 border p-7">
        <h2 class="my-3 text-2xl font-semibold">Tell us about your Skills</h2>
        <p>
            Select, rate, and describe your experience for your top skills. You
            can only select up to a maximum of 15 skills.
        </p>

        <div class="relative mb-4">
            <div class="relative">
                <input
                    v-model="query"
                    type="text"
                    class="mt-3 w-full rounded border p-3 outline-blue-400"
                />
                <i
                    @click="addSkill(query)"
                    class="bi bi-plus-lg absolute right-3 top-[50%] translate-y-[-20%] cursor-pointer text-lg font-bold text-blue-500"
                ></i>
            </div>

            <div
                v-if="isOpen"
                :class="[
                    'absolute left-0 h-20 w-full overflow-auto rounded bg-slate-500 p-3 text-white',
                    {
                        'h-fit': !data.length,
                    },
                ]"
            >
                <p v-show="!data.length">No Skills found</p>
                <p
                    v-for="(res, index) in data"
                    :key="index"
                    class="cursor-pointer p-1"
                    ref="skill"
                    @click="addSkill(res)"
                >
                    {{ res }}
                </p>
            </div>
        </div>

        <div>
            <p class="mb-2">Your Skills:</p>
            <div>
                <div
                    v-for="(skill, index) in skills"
                    :key="index"
                    class="border p-3"
                >
                    {{ skill }}
                </div>
            </div>
        </div>
    </div>
</template>
