<script setup>
import { Link, router } from "@inertiajs/vue3";
import { forEach } from "lodash";
import { FreeMode } from "swiper/modules";
import { onMounted, ref, useTemplateRef } from "vue";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";

let swiperContainer = useTemplateRef("swiper-container");
// let next = useTemplateRef("next");
// let prev = useTemplateRef("prev");

// onMounted(() => {
//     next.value.addEventListener("click", () => {
//         swiperContainer.value.swiper.slideNext();
//     });
// });
let checkboxs;
onMounted(() => {
    swiperContainer.value.swiper.update();
    checkboxs = document.querySelectorAll("input[type='checkbox']");
});

let workingSched = ref([]);
function updateWorkingSched() {
    checkboxs.forEach((e) => {
        if (e.classList.contains("workingsched")) {
            if (!e.checked) {
                const index = workingSched.value.indexOf(e.value);
                if (index !== -1) {
                    workingSched.value.splice(index, 1);
                }
            }

            if (e.checked) {
                workingSched.value.push(e.value);
            }
        }
    });
    console.log(workingSched.value);
    submit();
}

const submit = () => {
    router.get(
        route("jobsearch"),
        {
            job_type: workingSched.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};
</script>
<template>
    <div class="container mx-auto px-[0.5rem] xl:max-w-7xl">
        <div class="grid grid-cols-[300px,1fr] pt-4">
            <div class="border p-3">
                <p>Filters</p>
                <div>
                    <p>Working Schedule</p>

                    <div>
                        <label for="full">Full time</label>
                        <input
                            @change="updateWorkingSched"
                            id="full"
                            type="checkbox"
                            value="full time"
                            class="workingsched"
                        />
                    </div>
                    <div>
                        <label for="part">Part time</label>
                        <input
                            id="part"
                            type="checkbox"
                            class="workingsched"
                            value="part time"
                        />
                    </div>
                    <div>
                        <label for="contract">Contract</label>
                        <input
                            id="contract"
                            type="checkbox"
                            class="workingsched"
                            value="contract"
                        />
                    </div>
                </div>
                <div>
                    <p>Working Modes</p>

                    <div>
                        <label for="remote">Remote</label>
                        <input id="remote" type="checkbox" />
                    </div>
                    <div>
                        <label for="hybrid">Hybrid</label>
                        <input id="hybrid" type="checkbox" />
                    </div>
                    <div>
                        <label for="office">Office</label>
                        <input id="office" type="checkbox" />
                    </div>
                </div>
                <div>
                    <p>Experience</p>

                    <div>
                        <label for="fresher">Fresher</label>
                        <input id="fresher" type="checkbox" />
                    </div>
                    <div>
                        <label for="year0-2">0-2 years</label>
                        <input id="year0-2" type="checkbox" />
                    </div>
                    <div>
                        <label for="year2-4">2-4 years</label>
                        <input id="year2-4" type="checkbox" />
                    </div>
                    <div>
                        <label for="year5">5+ years</label>
                        <input id="year5" type="checkbox" />
                    </div>
                </div>
            </div>
            <div class="h-[1000px] overflow-hidden border p-3">
                <header class="mb-4 flex justify-end">
                    <input
                        type="text"
                        class="w-96 rounded-full border p-2 px-5"
                        placeholder="Search job title"
                    />
                </header>

                <div class="h-16">
                    <swiper-container
                        ref="swiper-container"
                        class="h-full"
                        :space-between="10"
                        slides-per-view="auto"
                        :free-mode="true"
                        :free-mode-sticky="false"
                        :navigation="true"
                    >
                        <swiper-slide
                            class="flex w-auto items-center justify-center"
                        >
                            <p
                                href="/"
                                method="get"
                                class="rounded-full border border-blue-500 p-3 text-[12px] text-blue-500"
                            >
                                Social Media Manager
                            </p>
                        </swiper-slide>
                        <swiper-slide
                            class="flex w-auto items-center justify-center"
                        >
                            <p
                                href="/"
                                method="get"
                                class="rounded-full border border-blue-300 p-3 text-blue-300"
                            >
                                Social Media Manager
                            </p>
                        </swiper-slide>
                        <swiper-slide
                            class="flex w-auto items-center justify-center"
                        >
                            <p
                                href="/"
                                method="get"
                                class="rounded-full border border-blue-300 p-3 text-blue-300"
                            >
                                Social Media Manager
                            </p>
                        </swiper-slide>
                        <swiper-slide
                            class="flex w-auto items-center justify-center"
                        >
                            <p
                                href="/"
                                method="get"
                                class="rounded-full border border-blue-300 p-3 text-blue-300"
                            >
                                Social Media Manager
                            </p>
                        </swiper-slide>
                        <swiper-slide
                            class="flex w-auto items-center justify-center"
                        >
                            <p
                                href="/"
                                method="get"
                                class="rounded-full border border-blue-300 p-3 text-blue-300"
                            >
                                Social Media Manager
                            </p>
                        </swiper-slide>
                        <swiper-slide
                            class="flex w-auto items-center justify-center"
                        >
                            <p
                                href="/"
                                method="get"
                                class="rounded-full border border-blue-300 p-3 text-blue-300"
                            >
                                Social Media Manager
                            </p>
                        </swiper-slide>
                        <swiper-slide
                            class="flex w-auto items-center justify-center"
                        >
                            <p
                                href="/"
                                method="get"
                                class="rounded-full border border-blue-300 p-3 text-blue-300"
                            >
                                Social Media Manager
                            </p>
                        </swiper-slide>
                        <swiper-slide
                            class="flex w-auto items-center justify-center"
                        >
                            <p
                                href="/"
                                method="get"
                                class="rounded-full border border-blue-300 p-3 text-blue-300"
                            >
                                Social Media Manager
                            </p>
                        </swiper-slide>
                        <swiper-slide
                            class="flex w-auto items-center justify-center"
                        >
                            <p
                                href="/"
                                method="get"
                                class="rounded-full border border-blue-300 p-3 text-blue-300"
                            >
                                Social Media Manager
                            </p>
                        </swiper-slide>
                        <swiper-slide
                            class="flex w-auto items-center justify-center"
                        >
                            <p
                                href="/"
                                method="get"
                                class="rounded-full border border-blue-300 p-3 text-blue-300"
                            >
                                Social Media Manager
                            </p>
                        </swiper-slide>
                        <swiper-slide
                            class="flex w-auto items-center justify-center"
                        >
                            <p
                                href="/"
                                method="get"
                                class="rounded-full border border-blue-300 p-3 text-blue-300"
                            >
                                Social Media Manager
                            </p>
                        </swiper-slide>
                        <swiper-slide
                            class="flex w-auto items-center justify-center"
                        >
                            <p
                                href="/"
                                method="get"
                                class="rounded-full border border-blue-300 p-3 text-blue-300"
                            >
                                Social Media Manager
                            </p>
                        </swiper-slide>
                    </swiper-container>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped></style>
