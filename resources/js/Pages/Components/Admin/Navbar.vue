
<script setup>
defineProps({
  isSidebarOpen: Boolean,
});
</script>
<template>
  <nav class="bg-white shadow p-4 flex justify-between items-center">
    <div>
      <!-- Sidebar Toggle Button -->
      <button @click="$emit('toggleSidebar')" class="text-gray-500 focus:outline-none md:hidden">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
        </svg>
      </button>
    </div>
    <div class="text-xl font-bold"> <img src="/assets/iCanCareersLogo.jpg" alt="" class="h-10 md:h-10 lg:h-16 w-auto object-contain"></div>
    <div>
      <!-- User Profile or Other Nav Items -->
    </div>
  </nav>
</template>
