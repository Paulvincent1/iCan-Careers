<script setup>
import { ref, computed } from "vue";
import SearchBar from "../Components/Learning/SearchBar.vue";
import CourseCard from "../Components/Learning/CourseCard.vue";
import Footer from "../Components/Admin/Footer.vue";
import Carousel from "../Components/Learning/Carousel.vue";

const searchQuery = ref("");

const courses = ref([
  { id: 1, title: "Basic Computer Skills", description: "Learn the basics of using a computer.", image: "/assets/hero.jpg" },
  { id: 2, title: "Resume Writing 101", description: "Create a professional resume to get noticed.", image: "/assets/hero.jpg" },
  { id: 3, title: "Interview Preparation", description: "Tips and tricks to ace your job interview.", image: "/assets/hero.jpg" }
]);

// 🔍 Filter courses based on search query
const filteredCourses = computed(() => {
  return courses.value.filter(course => 
    course.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    course.description.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});
</script>

<template>
  <Head title="Learning | iCan Careers" />
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
    <div class="max-w-6xl mx-auto">
      <h1 class="text-4xl font-bold text-gray-800 dark:text-gray-100 text-center mb-6">
        Learning Resources for Seniors & PWDs
      </h1>
      <p class="text-lg text-gray-600 dark:text-gray-300 text-center mb-8">
        Enhance your skills and gain confidence in your job search.
      </p>

      <!-- Search Bar -->
      <SearchBar v-model="searchQuery" class="mb-6" />

      <!-- 🌟 Featured Courses Carousel Component -->
      <Carousel :slides="courses" />

      <!-- 📚 Course Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <CourseCard 
          v-for="course in filteredCourses" 
          :key="course.id" 
          :course="course"
        />
      </div>
    </div>
  </div>
  <Footer />
</template>
