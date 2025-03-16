<script setup>
import { ref } from "vue";

// Define variable first
const plainText = `Cause when you love someone
You open up your heart
When you love someone
You make room
If you love someone
And you're not afraid to lose 'em
You probably never loved someone like I do
You probably never loved someone like I do`;

// Card data with referenced variables
const cards = ref([
  {
    title: "Lloyd Jacky Mirasol",
    description: "Game Dev Developer",
    image: "/assets/Mirasol.jpg",
    backText: "I am a Game Dev Developer with so many years of experience.",
  },
  {
    title: "Draven Troy Coloma",
    description: "Document Writer",
    image: "/assets/Draven.jpg",
    backText: `I am a Document Writer and Perfectionist. ${plainText}`, // Use backticks here
  },
  {
    title: "Affordable Plans",
    description: "Flexible pricing options to fit your budget with no hidden fees.",
    image: null,
    backText: "Choose a plan that fits your hiring needs without extra costs.",
  },
]);

// Track flipped state for each card
const flippedCards = ref(cards.value.map(() => false));

// Function to flip a card
const flipCard = (index) => {
  flippedCards.value[index] = !flippedCards.value[index];
};
</script>
<template>
    <section class="py-10 bg-blue-50">
      <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Developers</h2>
        <p class="text-lg text-gray-600 mb-8">
          We connect employers with top-tier, verified workers, making hiring easy, fast, and secure.
        </p>

        <!-- Grid Layout -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Flippable Cards -->
          <div
            v-for="(card, index) in cards"
            :key="index"
            class="flex justify-center w-full cursor-pointer"
            @click="flipCard(index)"
          >
            <div
              class="relative w-full h-80 transition-transform duration-500 transform-style-3d"
              :class="{ 'rotate-y-180': flippedCards[index] }"
            >
              <!-- Front Side -->
              <div
                class="absolute w-full h-full backface-hidden p-6 bg-white shadow-lg rounded-lg flex flex-col items-center text-center"
              >
                <div v-if="card.image" class="flex justify-center">
                  <img
                    :src="card.image"
                    alt="Profile"
                    class="w-24 h-24 md:w-32 md:h-32 lg:w-40 lg:h-40 object-cover rounded-full"
                  />
                </div>
                <h3 class="text-xl font-semibold text-[#fa8334] mt-4">{{ card.title }}</h3>
                <p class="text-gray-600 mt-2">{{ card.description }}</p>
                <p class="text-blue-400 "><u>Click to know more about Developer</u></p>
              </div>

              <!-- Back Side -->
              <div
                class="absolute w-full h-full backface-hidden rotate-y-180 p-6 bg-[#fa8334] text-white shadow-lg rounded-lg flex items-center justify-center text-center"
              >
                <p class="text-lg">{{ card.backText }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </template>



  <style scoped>
  .transform-style-3d {
    transform-style: preserve-3d;
  }
  .backface-hidden {
    backface-visibility: hidden;
  }
  .rotate-y-180 {
    transform: rotateY(180deg);
  }
  /* Ensure card height is consistent */
  .relative {
    height: 20rem; /* Adjust height as needed */
  }
  .absolute {
    height: 100%;
  }
  </style>
