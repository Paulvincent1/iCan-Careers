<script setup>
import PricingPlan from "../Components/Pricing/PricingPlan.vue";
import WhyChooseUs from "../Components/Pricing/WhyChooseUs.vue";
import FAQSection from "../Components/Pricing/FAQSection.vue";
import Footer from "../Components/Admin/Footer.vue";
import ReusableModal from "../Components/Modal/ReusableModal.vue";
import { ref } from "vue";

let props = defineProps({
    subscriptionInvoicesProps: null,
});

const pricingPlans = [
    {
        title: "Free",
        tag: "Free",
        subtitle: "Why No Free Trial?",
        price: "Free",
        features: [
            { text: "Communicate with Workers", strike: true },
            { text: "Hire Workers", strike: true },
            { text: "Create up to 3 jobs per month" },
            { text: "2 days Job Post approval" },
            { text: "View Job Applications" },
            { text: "Bookmark Workers" },
            { text: "Easypay" },
        ],
        borderColor: "border-green-500 border-t-8 rounded",
        priceColor: "text-green-500"
    },
    {
        title: "Pro Tier",
        tag: "Pro",
        subtitle: "Monthly",
        price: "₱3999",
        features: [
            { text: "Communicate with Workers" },
            { text: "Hire Workers" },
            { text: "Create up to 3 jobs per month" },
            { text: "Instant Job Approvals" },
            { text: "View Job Applications" },
            { text: "Bookmark Workers" },
            { text: "Easypay" },
        ],
        borderColor: "border-blue-500 border-t-8 rounded",
        priceColor: "text-blue-500"
    },
    {
        title: "Premium Tier",
        tag: "Premium",
        subtitle: "Anually",
        price: "₱5699",
        features: [
            { text: "Communicate with Workers" },
            { text: "Hire Workers" },
            { text: "Create up to 3 jobs per month" },
            { text: "Instant Job Approval" },
            { text: "View Job Applications" },
            { text: "Bookmark Workers" },
            { text: "Easypay" },
            { text: "Unlimited Background Data Checks" },
        ],
        borderColor: "border-red-500 border-t-8 rounded",
        priceColor: "text-red-500"
    },
];

const faqs = [
    {
        sectionTitle: "General FAQs",
        faqs: [
            {
                question:
                    "How does your platform help senior citizens and PWDs find jobs?",
                answer: " Our platform connects senior citizens and PWDs with inclusive employers who value their skills and experience. We offer job recommendations based on abilities, verified employers, and accessibility-friendly features to make job searching easy and effective.",
            },
            {
                question: "Are employers on this platform verified?",
                answer: `Yes! We carefully verify all employers to ensure they provide fair opportunities for senior citizens and PWDs. Employers who meet our inclusion standards are marked as "Verified."`,
            },
            {
                question:
                    "Can I apply for a job even if I have limited work experience?",
                answer: "Absolutely! Many employers value life experience, skills, and dedication over formal work history. Our platform allows you to showcase your strengths, even if you have limited job experience.",
            },
        ],
    },
    {
        sectionTitle: "Job Seeker FAQs",
        faqs: [
            {
                question: "How do I improve my chances of getting hired?",
                answer: `Here are some tips to increase your chances:<br>
                        ✅ Complete your profile with detailed skills and experience.<br>
                        ✅ Apply for jobs that match your abilities and interests.<br>
                        ✅ Stay active and respond to employer messages quickly.<br>
                        ✅ Take advantage of mentorship and training resources available on our platform.`,
            },
            {
                question:
                    "Is there a fee to use this platform as a job seeker?",
                answer: `No, job seekers can sign up and apply for jobs for free. Some premium features, like mentorship programs or priority applications, may have additional benefits for subscribers.`,
            },
        ],
    },

    {
        sectionTitle: "Employer FAQs",
        faqs: [
            {
                question: "Why should I hire senior citizens and PWDs?",
                answer: `Senior citizens bring years of experience and reliability, while PWDs offer unique skills and perspectives that enhance workplace diversity. Hiring inclusively also improves company reputation and attracts socially responsible customers.`,
            },
            {
                question: "How do I post a job for senior citizens or PWDs?",
                answer: `Simply create an employer account, complete your profile, and click “Post a Job.” Specify any accommodations or flexible work options to attract the right candidates.`,
            },
        ],
    },

    {
        sectionTitle: "Technical & Account FAQs",
        faqs: [
            {
                question: "Is my personal information safe on this platform?",
                answer: `Yes! We prioritize data privacy and use encryption to protect user information. Your details are only shared with employers when you apply for a job.`,
            },
            {
                question: "How do I reset my password if I forget it?",
                answer: `Click on "Forgot Password" on the login page, enter your email, and follow the instructions to reset your password securely.`,
            },
        ],
    },
];

let isOpenInvoiceModal = ref(false);
let invoice = ref(null);
function openInvoiceModal(e) {
    if (e === "Pro") {
        invoice.value = props.subscriptionInvoicesProps.find(
            (invoice) => invoice.subscription_type === "Pro",
        );
        isOpenInvoiceModal.value = true;
    }
    if (e === "Premium") {
        invoice.value = props.subscriptionInvoicesProps.find(
            (invoice) => invoice.subscription_type === "Premium",
        );
        isOpenInvoiceModal.value = true;
    }

    // console.log(invoice.value);
}

function closeInvoiceModal() {
    isOpenInvoiceModal.value = false;
}
</script>

<template>
    <Head title="Pricing | iCan Careers" />
    <section class="relative bg-[#fa8334] py-20 text-center text-white">
        <div class="container mx-auto px-6">
            <h1 class="animate-fadeIn mb-4 text-4xl font-bold sm:text-5xl">
                Find the Best Workers for Your Needs
            </h1>
            <p class="mx-auto max-w-2xl text-lg font-light sm:text-xl">
                Simplify hiring with our verified workers. Choose the right plan
                for your business and start hiring today.
            </p>
            <a
                href="#pricing"
                class="mt-6 inline-block rounded-lg bg-white px-6 py-3 font-bold text-[#fa8334] shadow-md transition hover:bg-gray-100"
            >
                View Pricing Plans
            </a>
        </div>
    </section>

    <section id="pricing" class="bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <h2 class="pt-10 text-3xl font-semibold">Pricing Plans</h2>
                <p class="mt-2 text-lg text-gray-600">
                    Choose a pricing plan to suit you.
                </p>
            </div>

            <div class="mx-auto my-10 max-w-screen-lg">
                <div
                    class="grid grid-cols-1 justify-center gap-6 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3"
                >
                    <PricingPlan
                        @buyNow="openInvoiceModal"
                        v-for="(plan, index) in pricingPlans"
                        :key="index"
                        :title="plan.title"
                        :subtitle="plan.subtitle"
                        :price="plan.price"
                        :features="plan.features"
                        :borderColor="plan.borderColor"
                        :priceColor="plan.priceColor"
                        :tag="plan.tag"
                    />
                </div>
            </div>
        </div>

        <WhyChooseUs />
    </section>

    <FAQSection :faqs="faqs" />

    <Footer />

    <ReusableModal v-if="isOpenInvoiceModal" @closeModal="closeInvoiceModal">
        <div class="h-[500px] w-[350px] rounded bg-white sm:w-[500px]">
            <iframe
                :src="invoice.invoice_url"
                title="Xendit Invoice"
                class="h-full w-full"
            >
            </iframe>
        </div>
    </ReusableModal>
</template>
