<script setup>
import { ref } from "vue";

// Define variable first
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
const plainTextLloyd= `"I am a skilled and confident developer passionate about building innovative solutions. With a strong problem-solving mindset, I create efficient and user-friendly applications. I continuously strive for excellence and embrace new challenges to grow and improve."`;

// Card data with referenced variables
const cards = ref([
    {
        title: "Lloyd Jacky Mirasol",
        description: "Enthusiast Game Developer",
        image: "/assets/Mirasol.jpg",
        backText: `${plainTextLloyd}`,
    },
    {
        title: "Draven Troy Coloma",
        description: "Wordsmith Document Writer",
        image: "/assets/Draven_prof.jpg",
        backText: `"I am a Documentation Expert of Tarlac and Perfectionist. ${plainText}`, // Use backticks here
    },
    {
        title: "Nathaniel Ree Manzano",
        description: "Front end Developer",
        image: "/assets/Nathaniel.jpg",
        backText:
            `${plainTextNath}`,
    },
    {
        title: "Paul Vincent Sumalinog",
        description: "Hearththrob Full Stack Developer",
        image: "/assets/Paul.jpg",
        backText:
            `${plainTextPaul}`,
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
    <section class="bg-blue-50 py-10">
        <div class="container mx-auto px-4 text-center">
            <h2 class="mb-6 text-3xl font-bold text-gray-800">Developers</h2>
            <p class="mb-8 text-lg text-gray-600">
                We connect employers with top-tier, verified workers, making
                hiring easy, fast, and secure.
            </p>

            <!-- Grid Layout -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                <!-- Flippable Cards -->
                <div
                    v-for="(card, index) in cards"
                    :key="index"
                    class="flex w-full cursor-pointer justify-center"
                    @click="flipCard(index)"
                >
                    <div
                        class="transform-style-3d relative h-80 w-full transition-transform duration-500"
                        :class="{ 'rotate-y-180': flippedCards[index] }"
                    >
                        <!-- Front Side -->
                        <div
                            class="backface-hidden absolute flex h-full w-full flex-col items-center rounded-lg bg-white p-6 text-center shadow-lg"
                        >
                            <div v-if="card.image" class="flex justify-center">
                                <img
                                    :src="card.image"
                                    alt="Profile"
                                    class="h-24 w-24 rounded-full object-cover md:h-32 md:w-32 lg:h-40 lg:w-40"
                                />
                            </div>
                            <h3
                                class="mt-4 text-xl font-semibold text-[#fa8334]"
                            >
                                {{ card.title }}
                            </h3>
                            <p class="mt-2 text-gray-600">
                                {{ card.description }}
                            </p>
                            <p class="text-blue-400">
                                <u>Click to know more about Developer</u>
                            </p>
                        </div>

                        <!-- Back Side -->
                        <div
                            class="backface-hidden rotate-y-180 absolute flex h-full w-full items-center justify-center rounded-lg bg-[#fa8334] p-6 text-left text-white shadow-lg"
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
