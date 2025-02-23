<script>
import { ref } from "vue";
import SearchBar from "../Components/Learning/SearchBar.vue";
import CourseCard from "../Components/Learning/CourseCard.vue";
import Footer from "../Components/Admin/Footer.vue";

export default {
  components: { SearchBar, CourseCard, Footer },
  setup() {
    const searchQuery = ref("");
    const courses = ref([
      { id: 1, title: "Basic Computer Skills", description: "Learn the basics of using a computer.", image: "/assets/hero.jpg" },
      { id: 2, title: "Resume Writing 101", description: "Create a professional resume to get noticed.", image: "/assets/hero.jpg" },
      { id: 3, title: "Interview Preparation", description: "Tips and tricks to ace your job interview.", image: "/assets/hero.jpg" }
    ]);

    return { searchQuery, courses };
  },
};
</script>

<template>
  <Head title="Learning | iCan Careers" />
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 p-6">
    <div class="max-w-6xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-6">Learning Resources</h1>

      <!-- Search Bar Component -->
      <SearchBar v-model="searchQuery" />

      <!-- Course Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <CourseCard 
          v-for="course in courses" 
          :key="course.id" 
          :course="course"
        />
      </div>
    </div>

    <!-- ✅ Footer must be inside the template -->
    
  </div>
  <Footer />
</template>

      

