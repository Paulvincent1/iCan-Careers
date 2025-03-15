<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/swiper-bundle.css'; // Import Swiper styles
import Rating from "./Components/Rating.vue";
import Searchbar from "./Components/Searchbar.vue";
import Statistic from "./Components/Statistic.vue";
import Joblisting from "./Components/Joblisting.vue";
import Testimonials from "./Components/Testimonials.vue";
import Footer from "./Components/Admin/Footer.vue";




const images = [
    "/assets/hero.jpg",
    "/assets/hero1.png",
    "/assets/hero4.png",
];


const jobs = [
    {
        id: 1,
        title: "Remote Customer Support",
        company: "ABC Corp",
        description: "Work from home providing customer service.",
    },
    {
        id: 2,
        title: "Freelance Graphic Designer",
        company: "XYZ Designs",
        description: "Flexible job for experienced designers.",
    },
    {
        id: 3,
        title: "Part-time Data Entry",
        company: "DataWorks",
        description: "Easy typing job with flexible hours.",
    },
];

const stats = [
    { value: "1000+", label: "Jobs Listed" },
    { value: "500+", label: "Employers Hiring" },
    { value: "10,000+", label: "Job Seekers" },
];

const testimonials = [
    {
        name: "Jane Doe",
        message:
            "iCan Careers helped me find a flexible job that suits my abilities!",
    },
    {
        name: "John Smith, XYZ Corp",
        message:
            "Hiring through iCan was quick and efficient. Highly recommend!",
    },
    {
        name: "Edgar Alan Poe, XYZ Corp",
        message: "We loved with a love that was more than love",
    },
    {
        name: "Maria Santos",
        message:
            "Thanks to iCan Careers, I found a remote job that allows me to support my family while managing my disability. This platform truly empowers people like me!",
    },
    {
        name: "John Dela Cruz",
        message: "As a senior citizen, I struggled to find work. iCan Careers connected me with an employer who values my experience, and now I feel productive again!"
    },
    {
        name: "Anna Reyes",
        message: "I was hesitant about job hunting due to my mobility challenges, but iCan Careers provided me with opportunities I never thought possible. Grateful for this platform!"
    },
];
</script>

<template>
    <Head title="Home | iCan Careers" />
    <section class="overflow-hidden bg-[#FEFBF9]">
        <div class="container mx-auto h-[607px] px-[0.5rem] py-4 md:max-w-7xl">
            
            <div
                class="container mx-auto flex h-full w-full flex-col items-center justify-center gap-8 sm:flex-row"
            >
                <div class="2xl:w-[500px] text-center lg:text-left">
                    <p class="my-7 text-center text-[32px]">
                        Where Others Can't, We Do.
                    </p>

                    <div class="flex flex-col">
                        <label class="mb-2">Looking for Talent?</label>
                        <Searchbar endpoint="/" name="Talent" class="mb-4" />
                        <label class="mb-2">Looking for Work?</label>
                        <Searchbar endpoint="/" name="Work" />
                        <p class="mt-6 text-center">
                            New to iCan?
                            <Link
                                class="text-blue-500"
                                :href="route('register.create')"
                                >Join Now</Link
                            >
                        </p>
                    </div>
                </div>

                <!-- Swiper Carousel (Hidden on Small Screens) -->
                <div
                    class="relative hidden md:hidden lg:flex 2xl:left-[-20px] 2xl:top-[-300px] 2xl:w-[500px]"
                >
                    <Swiper
                        class="absolute left-0 top-0 h-[600px] w-[800px] max-w-none"
                        :slides-per-view="1"
                        :loop="true"
                        :autoplay="{ delay: 3000 }"
                    >
                        <SwiperSlide v-for="(image, index) in images" :key="index">
                            <img
                                :src="image"
                                :alt="`hero-section-${index}`"
                                class="h-full w-full object-cover"
                            />
                        </SwiperSlide>
                    </Swiper>
                </div>
            </div>
        </div>
    </section>

    <Statistic :stats="stats" />
    <Joblisting :jobs="jobs" />
    <Testimonials :testimonials="testimonials" />
    <Footer />
</template>
