<script setup>
import { ref, computed, onMounted } from "vue";
import SearchBar from "../Components/Learning/SearchBar.vue";
import UseCourses from "../Components/Learning/UseCourses.vue";
import CourseSlider from "../Components/Learning/CourseSlider.vue";
import Footer from "../Components/Admin/Footer.vue";
import CourtList from "../Components/Learning/CourtList.vue";



// Reference to the UseCourses component
const useCoursesComponent = ref(null);

const searchQuery = ref("");
const activeTab = ref("all"); // Default to "all"

const courses = ref([]);
const courses2 = ref([]);
const courses3 = ref([]);
const courses5 = ref([]);
const preview = ref([]);

const tabs = [
    { id: "all", label: "All Courses" },
    { id: "health", label: "Health and Wellness" },
    { id: "creative", label: "Creative Arts and Hobbies" },
    { id: "job", label: "Job-Ready Skills" },
    { id: "business", label: "Business Marketing" },
];

// Fetch courses data from UseCourses component when mounted
onMounted(() => {
  if (useCoursesComponent.value) {
    courses.value = useCoursesComponent.value.courses;
    courses2.value = useCoursesComponent.value.courses2;
    courses3.value = useCoursesComponent.value.courses3;
    courses5.value = useCoursesComponent.value.courses5;
    preview.value = useCoursesComponent.value.preview;
  }
});

// 🔍 Filter courses based on search query and selected tab
const filteredCourses = computed(() => {
  let allCourses = [...courses.value, ...courses2.value, ...courses5.value, ...preview.value];

  if (activeTab.value === "health") {
    allCourses = courses.value; // Business courses
  } else if (activeTab.value === "creative") {
    allCourses = courses2.value; // Individual courses
  } else if (activeTab.value === "business") {
    allCourses = courses5.value; // Individual courses // Individual courses
  }

  return allCourses.filter(
    (course) =>
      course.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      course.description.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

// 🔄 Dynamic title based on the selected tab
const courseTitle = computed(() => {
  if (activeTab.value === "health") return "Health and Wellness";
  if (activeTab.value === "creative") return "Creative Arts and Hobbies";
  if (activeTab.value === "business") return "Business and Marketing";
  return "Featured Courses"; // Default title
});
</script>

<template>
  <Head title="Business Marketing | iCan Careers" />

  <div class="max-h-screen bg-gray-50 p-6">
    <div class="max-w-6xl mx-auto">

      <!-- 🔍 Search Bar -->
      <SearchBar v-model="searchQuery" class="mb-6 p-8" />
      <div><h1 class="text-[50px] font-bold">Trending Searches</h1></div>
      <!-- 📌 Tabs for Course Type Filters -->
      <nav class="mb-6">
        <ul class="flex space-x-4 border-b overflow-x-auto">
          <li v-for="tab in tabs" :key="tab.id"
              @click="activeTab = tab.id"
              :class="{
                'border-b-2 border-yellow-300 font-semibold': activeTab === tab.id,
                'text-yellow-300': activeTab === tab.id,
                'text-gray-500': activeTab !== tab.id
              }"
              class="cursor-pointer px-4 py-2 whitespace-nowrap">
            {{ tab.label }}
          </li>
        </ul>
      </nav>

      <!-- 📚 Course Slider (Changes based on selected tab) -->
      <CourseSlider :courses="filteredCourses" :title="courseTitle" />


    </div>

  </div>
    <div class="max-w-6xl mx-auto">
      <div><h1 class="text-[20px] text-gray-400">Results for "Business Marketing"</h1></div>
        <CourtList category="business" />
    </div>
  <Footer />
  <!-- Include the UseCourses component -->
  <UseCourses ref="useCoursesComponent" />
</template>
