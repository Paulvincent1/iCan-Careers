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
// Expose the courses data to the parent component
defineExpose({
  courses,
  courses2,
  courses3,
  preview
});
</script>

<template>
  <!-- This component doesn't render anything on its own -->
</template>
