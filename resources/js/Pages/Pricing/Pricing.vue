<script setup>
import PricingPlan from "../Components/Pricing/PricingPlan.vue";
import WhyChooseUs from "../Components/Pricing/WhyChooseUs.vue";
import FAQSection from "../Components/Pricing/FAQSection.vue";
import Footer from "../Components/Admin/Footer.vue";
const pricingPlans = [
    {
        title: "Free",
        subtitle: "Why No Free Trial?",
        price: "Free",
        features: [
            { text: "Communicate with Workers", strike: true },
            { text: "Hire Workers", strike: true },
            { text: "Create up to 3 jobs per month" },
        ],
        borderColor: "border-green-500",
    },
    {
        title: "Complete Edition",
        subtitle: "Monthly",
        price: "₱499",
        features: [
            { text: "Communicate with Workers" },
            { text: "Hire Workers" },
            { text: "Create up to 3 jobs per month" },
            { text: "Contact 75 workers / month" },
        ],
        borderColor: "border-blue-500",
    },
    {
        title: "Ultimate Edition",
        subtitle: "Monthly",
        price: "₱999",
        features: [
            { text: "Communicate with Workers" },
            { text: "Hire Workers" },
            { text: "Create up to 3 jobs per month" },
            { text: "Contact 75 workers / month" },
            { text: "Workers Mentoring System", link: true },
            { text: "Unlimited Background Data Check", link: true },
        ],
        borderColor: "border-red-500",
    },
];

const faqs = [
    {
        sectionTitle: "General FAQs",
        faqs: [
            {
                question: "How do I find the right worker?",
                answer: "Our smart matching algorithm recommends top workers based on your job post and needs",
            },
            {
                question: "Are the workers verified?",
                answer: "Yes, every worker undergoes a background check before being listed on our platform.",
            },
            {
                question:
                    "How do I communicate with workers?",
                answer: "With our Complete and Ultimate plans, you can message and hire workers directly.",
            },

            {
                question:
                    "Can I post jobs for free?",
                answer: "Yes! Free users can create up to 3 job posts per month.",
            },
        ],
    },
    {
        sectionTitle: "Cancelling Your Subscription",
        faqs: [
            {
                question: "How do I cancel my subscription?",
                answer: "You can cancel your subscription at any time...",
            },
        ],
    },
];
</script>

<template>
    <Head title="Pricing" />
    <section
        class="relative bg-gradient-to-r from-blue-700 to-indigo-600 py-20 text-center text-white"
    >
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
                class="mt-6 inline-block rounded-lg bg-white px-6 py-3 font-bold text-blue-600 shadow-md transition hover:bg-gray-100"
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

            <div class="my-5 grid grid-cols-1 md:grid-cols-1 lg:grid-cols-3">
                <PricingPlan
                    v-for="(plan, index) in pricingPlans"
                    :key="index"
                    :title="plan.title"
                    :subtitle="plan.subtitle"
                    :price="plan.price"
                    :features="plan.features"
                    :borderColor="plan.borderColor"
                />
            </div>
        </div>

        <WhyChooseUs />
    </section>

    <FAQSection :faqs="faqs" />

    <Footer />
</template>
