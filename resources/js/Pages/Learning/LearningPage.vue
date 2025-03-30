<script setup>
import { Link } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";
import SearchBar from "../Components/Learning/SearchBar.vue";
import UseCourses from "../Components/Learning/UseCourses.vue";
import CourseSlider from "../Components/Learning/CourseSlider.vue";
import Footer from "../Components/Admin/Footer.vue";
import Developers from "../Components/Learning/Developers.vue";
import Carousel from "../Components/Learning/Carousel.vue";
import Navlinks from "../Components/Learning/Navlinks.vue";

// Reference to the UseCourses component
const useCoursesComponent = ref(null);

const searchQuery = ref("");
const activeTab = ref("all"); // Default to "all"

const courses = ref([]);
const courses2 = ref([]);
const courses3 = ref([]);
const courses4 = ref([]);
const courses5 = ref([]);
const courses6 = ref([]);
const courses7 = ref([]);
const courses8 = ref([]);
const preview = ref([]);

const tabs = [
    { id: "all", label: "All Courses" },
    { id: "jobseeking", label: "Jobseeking" },
    { id: "creative", label: "Creative Arts and Hobbies" },
    { id: "job", label: "Job-Ready Skills" },
    { id: "tech", label: "Technology And IT" },
    { id: "business", label: "Business Marketing" },
    { id: "education", label: "Education And Training" },
    { id: "finance", label: "Finance And Accounting" },
    { id: "personal", label: "Personal Development" },
];

// Fetch courses data from UseCourses component when mounted
onMounted(() => {
    if (useCoursesComponent.value) {
        courses.value = useCoursesComponent.value.courses;
        courses2.value = useCoursesComponent.value.courses2;
        courses3.value = useCoursesComponent.value.courses3;
        courses4.value = useCoursesComponent.value.courses4;
        courses5.value = useCoursesComponent.value.courses5;
        courses6.value = useCoursesComponent.value.courses6;
        courses7.value = useCoursesComponent.value.courses7;
        courses8.value = useCoursesComponent.value.courses8;
        preview.value = useCoursesComponent.value.preview;
    }
});

// 🔍 Filter courses based on search query and selected tab
const filteredCourses = computed(() => {
    let allCourses = [
        ...courses.value,
        ...courses2.value,
        ...courses3.value,
        ...courses4.value,
        ...courses5.value,
        ...courses6.value,
        ...courses7.value,
        ...courses8.value,
        ...preview.value,
    ];

    if (activeTab.value === "jobseeking") {
        allCourses = courses.value;
    } else if (activeTab.value === "creative") {
        allCourses = courses2.value;
    } else if (activeTab.value === "job") {
        allCourses = courses3.value;
    } else if (activeTab.value === "tech") {
        allCourses = courses4.value;
    } else if (activeTab.value === "business") {
        allCourses = courses5.value;
    } else if (activeTab.value === "education") {
        allCourses = courses6.value;
    } else if (activeTab.value === "finance") {
        allCourses = courses7.value;
    } else if (activeTab.value === "personal") {
        allCourses = courses8.value;
    }

    return allCourses.filter(
        (course) =>
            course.title
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase()) ||
            course.description
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase()),
    );
});

// 🔄 Dynamic title based on the selected tab
const courseTitle = computed(() => {
    if (activeTab.value === "jobseeking") return "Jobseeking";
    if (activeTab.value === "creative") return "Creative Arts and Hobbies";
    if (activeTab.value === "job") return "Job-Ready Skills";
    if (activeTab.value === "tech") return "Technology And IT";
    if (activeTab.value === "business") return "Business Marketing";
    if (activeTab.value === "education") return "Education and Training";
    if (activeTab.value === "finance") return "Finance and Accounting";
    if (activeTab.value === "personal") return "Personal Development";
    return "Featured Courses"; // Default title
});
</script>

<template>
    <Head title="Learning | iCan Careers" />
    <section class="overflow-hidden bg-[#FEFBF9]">
        <div class="container mx-auto h-[607px] px-[0.5rem] py-4 md:max-w-7xl">
            <div
                class="container mx-auto flex h-full w-full flex-col items-center justify-center gap-8 sm:flex-row"
            >
                <div class="text-left 2xl:w-[500px]">
                    <div class="text-left 2xl:w-[500px]">
                        <p class="my-2 text-[40px]">
                            Enhance your skills and unlock new opportunities for
                            a brighter future.
                        </p>
                        <p class="my-1 text-[20px]">
                            Receive expert guidance to build essential skills in
                            <Link
                                :href="route('learning.health')"
                                class="font-bold hover:text-blue-400"
                                >jobseeking, </Link
                            >
                            <Link
                                :href="route('learning.creative')"
                                class="font-bold hover:text-blue-400"
                                >creative and design,</Link
                            >
                            and

                            by a platform that connects you to real
                            opportunities.
                        </p>
                        <div class="flex flex-col">
                        <p class="mt-6">
                            <Link
                                class="rounded-3xl bg-[#fa8334] px-7 py-2 font-medium text-white hover:bg-gray-600"
                                :href="route('pricing')"
                                >Start My Free Trial</Link
                            >
                        </p>
                    </div>
                    </div>


                </div>

                <div
                    class="relative 2xl:left-[-20px] 2xl:top-[-300px] 2xl:w-[500px]"
                >
                    <img
                        class="absolute left-0 top-0 h-[700px] w-[1000px] max-w-none"
                        src="/assets/hero4.png"
                        alt="hero-section"
                    />
                </div>
            </div>
        </div>
    </section>

    <div class="max-h-screen bg-gray-50 p-6">
        <div class="mx-auto max-w-6xl">
            <!-- 🔍 Search Bar -->
            <SearchBar v-model="searchQuery" class="mb-6 p-8" />
            <div><h1 class="text-[50px] font-bold">Trending Searches</h1></div>
            <!-- 📌 Tabs for Course Type Filters -->
            <nav class="mb-6">
                <ul class="flex space-x-4 overflow-x-auto border-b">
                    <li
                        v-for="tab in tabs"
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        :class="{
                            'border-b-2 border-yellow-300 font-semibold':
                                activeTab === tab.id,
                            'text-yellow-300': activeTab === tab.id,
                            'text-gray-500': activeTab !== tab.id,
                        }"
                        class="cursor-pointer whitespace-nowrap px-4 py-2"
                    >
                        {{ tab.label }}
                    </li>
                </ul>
            </nav>
            <!-- 📚 Course Slider (Changes based on selected tab) -->
            <CourseSlider :courses="filteredCourses" :title="courseTitle" />
        </div>
    </div>
    <Navlinks />
    <Developers />
    <Footer />

    <!-- Include the UseCourses component -->
    <UseCourses ref="useCoursesComponent" />
</template>
