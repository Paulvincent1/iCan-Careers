        <script setup>
        import { library } from "@fortawesome/fontawesome-svg-core";
        import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
        import {

        faYoutube,

    } from "@fortawesome/free-brands-svg-icons";
    library.add( faYoutube);
    defineProps({
    channelLink: {
        type: String,
        required: true,
    },

    });
    </script>

    <template>
    <section class="relative bg-[#fa8334] py-12 px-6 overflow-hidden">
        <!-- Decorative background effect -->
        <div class="absolute inset-0 bg-[url('/assets/yt-pattern.png')] bg-cover bg-center opacity-10"></div>

        <div class="relative z-10 flex flex-col items-center justify-center text-center space-y-6">
        <!-- Title -->
        <h2 class="text-3xl md:text-4xl font-bold text-white drop-shadow-md">
            📺 Expand Your Learning with Free Video Courses
        </h2>

        <!-- Subtitle -->
        <p class="max-w-2xl text-lg text-white/90 leading-relaxed">
            Explore exclusive tutorials and practical lessons designed to help you
            grow your career, improve your skills, and achieve your goals.
        </p>

        <!-- Button -->
        <a
            :href="channelLink"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-block bg-white text-red-600 font-semibold px-6 py-3 shadow-md hover:bg-gray-100 hover:scale-105 transition"
        >
        <font-awesome-icon
                                :icon="['fab', 'youtube']"
                                size="lg"
                            />
            Click Here to Watch More
        </a>
        </div>
    </section>
    </template>
