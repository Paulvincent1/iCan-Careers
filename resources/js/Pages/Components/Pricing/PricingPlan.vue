<template>
  <div class="max-w-xs mx-auto m-5">
    <div class="bg-white border shadow-lg" :class="borderColor">
      <div class="text-center py-6">
        <h4 class="text-xl font-semibold">{{ title }}</h4>
        <p v-if="subtitle" class="text-lg text-gray-600 mt-2">{{ subtitle }}</p>
        <p class="text-4xl text-primary my-4 font-bold">{{ price }}</p>
        <div class="p-4 card-body">
          <ul>
            <li v-for="(feature, index) in features" :key="index" class="pt-4">
              <span v-if="feature.strike"><strike>{{ feature.text }}</strike></span>
              <span v-else>{{ feature.text }}</span>
            </li>
          </ul>
        </div>
        <a
          href="#"
          class="mt-4 inline-block bg-transparent border border-blue-500 text-blue-500 font-bold py-2 px-4 rounded-lg hover:bg-blue-500 hover:text-white transition"
        >
          Buy Now
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  subtitle: String,
  price: String,
  features: Array,
  borderColor: String,
});
</script>