<script setup>
import { ref } from "vue";


const preview = ref([
  {
    id: 1,
    title: "Basic Computer Skills",
    description: "Learn the basics of using a computer.",
    image: "/assets/hero.jpg",
  },

  {
    id: 2,
    title: "Resume Writing 101",
    description: "Create a professional resume to get noticed.",
    image: "/assets/hero.jpg",
  },
  {
    id: 3,
    title: "Interview Preparation",
    description: "Tips and tricks to ace your job interview.",
    image: "/assets/hero.jpg",
  },
]);


// Define courses data
const courses = ref([
  {
    id: 1,
    category: "health",
    title: "Disability Hiring Playbook",
    description: `Play video to learn more about disability hiring. <a href="https://www.youtube.com/watch?v=PgFhjcu_SLM" target="_blank" class="text-blue-500 underline">Watch here</a>`,
    image: "https://www.youtube.com/watch?v=PgFhjcu_SLM",
  },
  {
    id: 2,
    category: "health",
    title: "Yoga Full Body Stretch",
    description: `Yoga is a great way to relax and stretch your body. <a href="https://www.youtube.com/watch?v=4pKly2JojMw" target="_blank" class="text-blue-500 underline">Watch here</a>`,
    image: "https://www.youtube.com/watch?v=4pKly2JojMw",
  },
  {
    id: 3,
    category: "health",
    title: "Interview Preparation",
    description: "Tips and tricks to ace your job interview.<a href='https://www.youtube.com/watch?v=LCWr-TJrc0k' target='_blank' class='text-blue-500 underline'>Watch here</a>",
    image: "https://www.youtube.com/watch?v=LCWr-TJrc0k",
  },
  {
    id: 4,
    category: "health",
    title: "Supervisor Health & Safety Awareness Training",
    description: "Tips and tricks to ace your job interview.",
    image: "https://www.youtube.com/watch?v=S_GF3Kf3Nrs",
  },
  {
    id: 5,
    category: "health",
    title: "Mental Health & Wellbeing",
    description: "Tips and tricks to ace your job interview.<a href='https://www.iamlearningcontent.com/get-started' target='_blank' class='text-blue-500 underline'>Watch here</a>",
    image: "https://www.youtube.com/watch?v=DAglPr_kaeU&t=36s",
  },

]);

const courses2 = ref([
  {
    id: 1,
    category: "creative",
    title: "Basic Computer Skills",
    description: "Learn the basics of using a computer.",
    image: "https://www.youtube.com/watch?v=B4Jpw5pszvw",
  },
  {
    id: 2,
    category: "creative",
    title: "Resume Writing 101",
    description: "Create a professional resume to get noticed.",
    image: "https://www.youtube.com/watch?v=w0GsNFDFtUo",
  },
  {
    id: 3,
    category: "creative",
    title: "Interview Preparation",
    description: "Tips and tricks to ace your job interview.",
    image: "https://www.youtube.com/watch?v=LCWr-TJrc0k",
  },
]);


const courses3 = ref([
  {
    id: 1,
    category: "job",
    title: "Freelancing for Beginners",
    description: "Teaches the basics of freelancing, including finding work online.",
    image: "https://www.youtube.com/watch?v=v9l4Jz20ofQ",
  },
  {
    id: 2,
    category: "job",
    title: "Resume Writing 101",
    description: "Create a professional resume to get noticed.",
    image: "https://www.youtube.com/watch?v=4SyKG7ijWjI",
  },
  {
    id: 3,
    category: "job",
    title: "Job Readiness Skills",
    description: "Learn the skills needed to get a job.<a href='https://www.youtube.com/watch?v=-vk-99seC_I&list=PLje7yAIv6jIn-JEdT2mcyLg8aeinxELlN' target='_blank' class='text-blue-500 underline'>Watch here</a>",
    image: "https://www.youtube.com/watch?v=-vk-99seC_I&list=PLje7yAIv6jIn-JEdT2mcyLg8aeinxELlN",
  },

]);
const courses4 = ref([
  {
    id: 1,
    category: "tech",
    title: "Information Technology Fundamentals",
    description: "Describes the basics of how a computer processes data. <a href='https://www.youtube.com/watch?v=penP04k-Lqo&list=PLZxiarL-F2CTPcmTDGAXXJiCWbKJtz0zX' target='_blank' class='text-blue-500 underline'>Watch here</a>",
    image: "https://www.youtube.com/watch?v=penP04k-Lqo&list=PLZxiarL-F2CTPcmTDGAXXJiCWbKJtz0zX",
  },
  {
    id: 2,
    category: "tech",
    title: "Resume Writing 101",
    description: "Create a professional resume to get noticed.",
    image: "https://www.youtube.com/watch?v=4SyKG7ijWjI",
  },
  {
    id: 3,
    category: "tech",
    title: "Job Readiness Skills",
    description: "Learn the skills needed to get a job.<a href='https://www.youtube.com/watch?v=-vk-99seC_I&list=PLje7yAIv6jIn-JEdT2mcyLg8aeinxELlN' target='_blank' class='text-blue-500 underline'>Watch here</a>",
    image: "https://www.youtube.com/watch?v=-vk-99seC_I&list=PLje7yAIv6jIn-JEdT2mcyLg8aeinxELlN",
  },

]);

const courses5 = ref([
  {
    id: 1,
    category: "business",
    title: "Business Knowledge",
    description: "You'll learn the complete A-Z guide of how to start a business in 2024.<a href='https://www.youtube.com/watch?v=9VlvbpXwLJs&t=177s' target='_blank' class='text-blue-500 underline'>Watch here</a>",
    image: "https://www.youtube.com/watch?v=9VlvbpXwLJs&t=177s",
  },
  {
    id: 2,
    category: "business",
    title: "Resume Writing 101",
    description: "Create a professional resume to get noticed.",
    image: "https://www.youtube.com/watch?v=4SyKG7ijWjI",
  },
  {
    id: 3,
    category: "business",
    title: "Job Readiness Skills",
    description: "Learn the skills needed to get a job.<a href='https://www.youtube.com/watch?v=-vk-99seC_I&list=PLje7yAIv6jIn-JEdT2mcyLg8aeinxELlN' target='_blank' class='text-blue-500 underline'>Watch here</a>",
    image: "https://www.youtube.com/watch?v=-vk-99seC_I&list=PLje7yAIv6jIn-JEdT2mcyLg8aeinxELlN",
  },

]);
const courses6 = ref([
  {
    id: 1,
    category: "education",
    title: "Education and Training Careers",
    description: "Education and Training Careers.<a href='https://www.youtube.com/watch?v=4r-QVk5-umI&list=PL5AAyLKIyw_qR3yM6BCb7AglZ7GMbwl9K' target='_blank' class='text-blue-500 underline'>Watch here</a>",
    image: "https://www.youtube.com/watch?v=4r-QVk5-umI&list=PL5AAyLKIyw_qR3yM6BCb7AglZ7GMbwl9K",
  },
  {
    id: 2,
    category: "education",
    title: "Resume Writing 101",
    description: "Create a professional resume to get noticed.",
    image: "https://www.youtube.com/watch?v=4SyKG7ijWjI",
  },
  {
    id: 3,
    category: "education",
    title: "Job Readiness Skills",
    description: "Learn the skills needed to get a job.<a href='https://www.youtube.com/watch?v=-vk-99seC_I&list=PLje7yAIv6jIn-JEdT2mcyLg8aeinxELlN' target='_blank' class='text-blue-500 underline'>Watch here</a>",
    image: "https://www.youtube.com/watch?v=-vk-99seC_I&list=PLje7yAIv6jIn-JEdT2mcyLg8aeinxELlN",
  },

]);
const courses7 = ref([
  {
    id: 1,
    category: "finance",
    title: " Introduction to Financial Accounting",
    description: "Basic financial ratios are analyzed.<a href='https://www.youtube.com/watch?v=Rpa_UAciIeU&list=PLSlzC-HFo7w5MA7vJy_m6T5ub_UvB2d10' target='_blank' class='text-blue-500 underline'>Watch here</a>",
    image: "https://www.youtube.com/watch?v=Rpa_UAciIeU&list=PLSlzC-HFo7w5MA7vJy_m6T5ub_UvB2d10",
  },
  {
    id: 2,
    category: "finance",
    title: "Introduction to GAAP",
    description: "(Generally Accepted Accounting Principles)<a href='https://www.youtube.com/watch?v=nqkk9omr3sc&list=PL_KGEFWqEaTCDEzqFn0imjpyIRDy4p63s' target='_blank' class='text-blue-500 underline'>Watch here</a>",
    image: "https://www.youtube.com/watch?v=nqkk9omr3sc&list=PL_KGEFWqEaTCDEzqFn0imjpyIRDy4p63s",
  },
  {
    id: 3,
    category: "finance",
    title: "Job Readiness Skills",
    description: "Learn the skills needed to get a job.<a href='https://www.youtube.com/watch?v=-vk-99seC_I&list=PLje7yAIv6jIn-JEdT2mcyLg8aeinxELlN' target='_blank' class='text-blue-500 underline'>Watch here</a>",
    image: "https://www.youtube.com/watch?v=-vk-99seC_I&list=PLje7yAIv6jIn-JEdT2mcyLg8aeinxELlN",
  },

]);
const courses8 = ref([
  {
    id: 1,
    category: "personal",
    title: "Techniques to INCREASE Your Intelligence",
    description: "The QUEST for knowledge require efforts.<a href='https://www.youtube.com/watch?v=HjpYMxPjcqc&list=PLzl3Cxso4GJnSm4LLnnFbNyYeRUHQWC0A' target='_blank' class='text-blue-500 underline'>Watch here</a>",
    image: "https://www.youtube.com/watch?v=HjpYMxPjcqc&list=PLzl3Cxso4GJnSm4LLnnFbNyYeRUHQWC0A",
  },
  {
    id: 2,
    category: "personal",
    title: "Introduction to GAAP",
    description: "Basic financial ratios are analyzed.<a href='https://www.youtube.com/watch?v=nqkk9omr3sc&list=PL_KGEFWqEaTCDEzqFn0imjpyIRDy4p63s' target='_blank' class='text-blue-500 underline'>Watch here</a>",
    image: "https://www.youtube.com/watch?v=nqkk9omr3sc&list=PL_KGEFWqEaTCDEzqFn0imjpyIRDy4p63s",
  },
  {
    id: 3,
    category: "personal;",
    title: "Job Readiness Skills",
    description: "Learn the skills needed to get a job.<a href='https://www.youtube.com/watch?v=-vk-99seC_I&list=PLje7yAIv6jIn-JEdT2mcyLg8aeinxELlN' target='_blank' class='text-blue-500 underline'>Watch here</a>",
    image: "https://www.youtube.com/watch?v=-vk-99seC_I&list=PLje7yAIv6jIn-JEdT2mcyLg8aeinxELlN",
  },

]);
// Expose the courses data to the parent component
defineExpose({
  courses,
  courses2,
  courses3,
  courses4,
  courses5,
  courses6,
  courses7,
  courses8,
  preview
});
</script>

<template>
  <!-- This component doesn't render anything on its own -->
</template>
