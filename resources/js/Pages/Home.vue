<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/swiper-bundle.css'; // Import Swiper styles
import { ref, watchEffect } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import Rating from "./Components/Rating.vue";
import Searchbar from "./Components/Searchbar.vue";
import Statistic from "./Components/Statistic.vue";
import Joblisting from "./Components/Joblisting.vue";
import Testimonials from "./Components/Testimonials.vue";
import Footer from "./Components/Admin/Footer.vue";




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
    {
        id: 4,
        title: "Virtual Assistant",
        company: "Elite Solutions",
        description: "Manage emails, scheduling, and admin tasks remotely.",
    },
    {
        id: 5,
        title: "Online English Tutor",
        company: "LearnFast Academy",
        description: "Teach English to international students online.",
    },
    {
        id: 6,
        title: "Content Writer",
        company: "BlogSphere",
        description: "Write engaging blog posts and articles from home.",
    },
    {
        id: 7,
        title: "Social Media Manager",
        company: "DigitalTrendz",
        description: "Manage and grow social media accounts for brands.",
    },
    {
        id: 8,
        title: "E-commerce Product Lister",
        company: "ShopEase",
        description: "List and manage products on an e-commerce website.",
    },
    {
        id: 9,
        title: "Remote IT Support Technician",
        company: "TechAssist",
        description: "Provide remote technical assistance to clients.",
    },
    {
        id: 10,
        title: "Online Survey Taker",
        company: "SurveyMasters",
        description: "Participate in surveys and earn money from home.",
    },
    {
        id: 11,
        title: "Remote Sales Representative",
        company: "SalesPro",
        description: "Sell products and services over the phone.",
    },
    {
        id: 12,
        title: "Freelance Web Developer",
        company: "CodeCrafters",
        description: "Build and maintain websites for clients.",
    },
    {
        id: 13,
        title: "Online Researcher",
        company: "InfoSeekers",
        description: "Conduct online research and gather data.",
    },
    {
        id: 14,
        title: "Remote Project Manager",
        company: "ProjectHub",
        description: "Manage projects and teams remotely.",
    },
    {
        id: 15,
        title: "Virtual Event Coordinator",
        company: "EventMasters",
        description: "Plan and coordinate virtual events.",
    },
];

const stats = [
    {  label: "Jobs Listed" },
    { label: "Companies Registered" },
    {  label: "Job Categories" },
    {  label: "Countries Served" },
    { label: "Verified Workers" },
    {  label: "Job Applications" },
    {  label: "Job Listings" },
    {  label: "Employers Hiring" },
    { label: "Job Seekers" },
    {  label: "Job Matches" },
    {  label: "Success Stories" },
    {  label: "Testimonials" },
    {  label: "Community Support" },
    {  label: "Partnerships" },
    {  label: "Events Hosted" },
    {  label: "Training Programs" },
    {  label: "Mentorship Connections" },
    {  label: "Feedback Received" },
    {  label: "User Satisfaction" },
    {  label: "Social Media Followers" },
    {  label: "Newsletter Subscribers" },
    {  label: "Community Engagements" },
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
    {
        name: "Mark Johnson",
        message: "iCan Careers made it easy for me to find a job that fits my skills and schedule. The support from the community is amazing!"
    },
    {
        name: "Sarah Lee",
        message: "I love how iCan Careers focuses on inclusivity. I found a job that not only matches my skills but also respects my needs as a person with a disability."
    },
    {
        name: "David Kim",
        message: "iCan Careers is a game-changer for job seekers like me. The platform is user-friendly, and the job listings are relevant and accessible."
    },
    {
        name: "Emily Garcia",
        message: "I appreciate how iCan Careers highlights employers who are committed to inclusivity. It gives me confidence that I can find a supportive workplace."
    },
    {
        name: "Michael Brown",
        message: "iCan Careers helped me find a job that not only pays well but also allows me to work from home. This flexibility is a blessing for my family!"
    },
    {
        name: "Sophia Martinez",
        message: "I was skeptical at first, but iCan Careers exceeded my expectations. The job matches were spot on, and I found a great employer who values diversity."
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
                <div class="2xl:w-[500px]">
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

                <div
                    class="relative 2xl:left-[-20px] 2xl:top-[-300px] 2xl:w-[500px]"
                >
                    <img
                        class="absolute left-0 top-0 h-[600px] w-[1000px] max-w-none"
                        src="/assets/hero.jpg"
                        alt="hero-section"
                    />
                </div>
            </div>
        </div>
    </section>
    <Statistic :stats="stats" />
    <Joblisting :jobs="jobs" />
    <Testimonials :testimonials="testimonials" />
    <Footer />
</template>
