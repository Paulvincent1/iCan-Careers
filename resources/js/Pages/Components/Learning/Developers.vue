<script setup>
import { ref } from "vue";

const plainText = `Cause when you love someone,
You open up your heart.
When you love someone,
You make room.
If you love someone,
And you're not afraid to lose 'em,
You probably never loved someone like I do,
You probably never loved someone like I do"`;

const plainTextNath = `"Passionate Front-End Developer specializing in creating responsive, user-friendly interfaces with HTML, CSS, JavaScript, and modern frameworks like Vue.js. Committed to delivering seamless user experiences through clean, efficient code and intuitive design."`;
const plainTextPaul = `"Versatile Full-Stack Developer skilled in both front-end and back-end technologies, including HTML, CSS, JavaScript, Vue.js, Laravel, and database management. Passionate about building scalable, high-performance applications with a seamless user experience."`;
const plainTextLloyd = `"I am a skilled and confident developer passionate about building innovative solutions. With a strong problem-solving mindset, I create efficient and user-friendly applications. I continuously strive for excellence and embrace new challenges to grow and improve."`;

const cards = ref([
  {
    title: "Lloyd Jacky Mirasol",
    description: "Enthusiast Game Developer",
    image: "/assets/Jacky.jpg",
    backText: `${plainTextLloyd}`,
  },
  {
    title: "Draven Troy Coloma",
    description: "Wordsmith Document Writer",
    image: "/assets/Draven_prof.jpg",
    backText: `"I am a Documentation Expert of Tarlac and Perfectionist. ${plainText}`,
  },
  {
    title: "Nathaniel Ree Manzano",
    description: "Front end Developer",
    image: "/assets/Nathaniel.jpg",
    backText: `${plainTextNath}`,
  },
  {
    title: "Paul Vincent Sumalinog",
    description: "Hearththrob Full Stack Developer",
    image: "/assets/Paul.jpg",
    backText: `${plainTextPaul}`,
  },
]);

const flippedCards = ref(cards.value.map(() => false));

const flipCard = (index) => {
  flippedCards.value[index] = !flippedCards.value[index];
};
</script>

<template>
  <section class="bg-blue-50 py-10">
    <div class="container mx-auto px-4 text-center">
      <h2 class="mb-6 text-3xl font-bold text-gray-800">Developers</h2>
      <p class="mb-8 text-lg text-gray-600">
        We connect employers with top-tier, verified workers, making hiring easy, fast, and secure.
      </p>

      <!-- Grid Layout -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Flippable Cards -->
        <div
          v-for="(card, index) in cards"
          :key="index"
          class="flex w-full cursor-pointer justify-center"
          @click="flipCard(index)"
        >
          <div
            class="transform-style-3d relative h-[22rem] w-full max-w-xs transition-transform duration-500 sm:h-[24rem] md:h-[26rem] lg:h-[28rem]"
            :class="{ 'rotate-y-180': flippedCards[index] }"
          >
            <!-- Front Side -->
            <div
              class="backface-hidden absolute flex h-full w-full flex-col items-center rounded-xl bg-white p-4 sm:p-6 text-center shadow-lg"
            >
              <div v-if="card.image" class="flex justify-center">
                <img
                  :src="card.image"
                  alt="Profile"
                  class="h-24 w-24 rounded-full object-cover sm:h-28 sm:w-28 md:h-32 md:w-32 lg:h-36 lg:w-36"
                />
              </div>
              <h3 class="mt-4 text-lg sm:text-xl font-semibold text-[#fa8334]">
                {{ card.title }}
              </h3>
              <p class="mt-2 text-sm sm:text-base text-gray-600">
                {{ card.description }}
              </p>
              <p class="mt-2 text-xs sm:text-sm text-blue-400">
                <u>Tap/Click to know more</u>
              </p>
            </div>

            <!-- Back Side -->
            <div
              class="backface-hidden rotate-y-180 absolute flex h-full w-full items-center justify-center rounded-xl bg-[#fa8334] p-4 sm:p-6 text-left text-white shadow-lg"
            >
              <p class="text-sm sm:text-base md:text-lg leading-relaxed">
                {{ card.backText }}
              </p>
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
</style>
