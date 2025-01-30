<script setup>
import { reactive } from "vue";

const collapseState = reactive({
    section1: true,
    section2: true,
    section3: true,
    section4: true,
    section5: true,
    section6: true,
    section7: true,
});

// Function to toggle collapse state
const toggleCollapse = (section) => {
    collapseState[section] = !collapseState[section];
};
</script>
<template>
    <Head title=" | Pricing" />
    <section>
        <div class="w-full h-[450px] bg-[#024570]">
            <h1 class="text-center">
                <span
                    class="font-bold text-white text-[40px] inline-block mt-[50px]"
                    >Hire A Great Worker</span
                >
                <span class="text-white text-[30px] ms-[10px]">or</span> <br />
                <span class="font-bold text-white text-[40px]"
                    >We'll Give Your Money Back!</span
                >
            </h1>
            <div class="text-center">
                <img src="" alt="" />
            </div>
        </div>
    </section>

     <section id="pricing" class="bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <h2 class="text-3xl font-semibold pt-10">Pricing Plans</h2>
                <p class="text-lg text-gray-600 mt-2">
                    Choose a pricing plan to suit you.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-3 my-5">
                <!-- Starter Edition -->
                <div class="lg:mt-6 lg:me-0 sm:mt-6 max-w-xs mx-auto m-10">
                    <div class="bg-white border border-green-500 shadow-lg">
                        <div class="text-center py-6">
                            <h4 class="text-xl font-semibold">Free</h4>
                            <a href="#"><u>Why No Free Trial?</u></a>
                            <p class="text-4xl text-primary my-4 font-bold">
                                Free
                            </p>
                            <div class="p-4 card-body">
                                <ul class="">
                                    <li class="pt-4">
                                        <strike
                                            >Communicate with Workers</strike
                                        >
                                    </li>
                                    <li class="pt-4">
                                        <strike>Hire Workers</strike>
                                    </li>
                                    <li class="pt-4">
                                        Create up to <strong>3</strong> jobs per
                                        month
                                    </li>
                                </ul>
                            </div>
                            <a
                                href="#"
                                class="mt-4 inline-block bg-transparent border border-blue-500 text-blue-500 font-bold py-2 px-4 rounded-lg hover:bg-blue-500 hover:text-white transition"
                            >
                                Buy Now
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Complete Edition -->
                <div class="max-w-xs sm:mt-6 mx-auto m-5">
                    <div class="bg-white border-2 border-blue-500">
                        <div class="text-center text-blue-500 py-4">
                            <div class="bg-blue-100 text-white py-2">
                                <p class="text-sm font-bold">Most Popular</p>
                            </div>
                        </div>
                        <div class="text-center py-5">
                            <h4 class="text-xl font-semibold">
                                Complete Edition
                            </h4>
                            <p class="text-lg text-gray-600 mt-2">Montly</p>
                            <p class="text-4xl text-primary my-4 font-bold">
                                ₱499
                            </p>
                            <div class="p-4 card-body">
                                <ul class="">
                                    <li class="pt-4">
                                        Communicate with Workers
                                    </li>
                                    <li class="pt-4">Hire Workers</li>
                                    <li class="pt-4">
                                        Create up to <strong>3</strong> jobs per
                                        month
                                    </li>
                                    <li class="pt-4">
                                        <strong>Contact 75 workers</strong> /
                                        month
                                    </li>
                                </ul>
                            </div>

                            <a
                                href="#"
                                class="mt-4 inline-block bg-transparent border border-blue-500 text-blue-500 font-bold py-2 px-4 rounded-lg hover:bg-blue-500 hover:text-white transition"
                            >
                                Buy Now
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Ultimate Edition -->
                <div
                    class="lg:mt-6 sm:mt-6 lg:ms-0 sm:mb-6 max-w-xs mx-auto m-5"
                >
                    <div class="bg-white border border-red-500 shadow-lg">
                        <div class="text-center py-6">
                            <h4 class="text-xl font-semibold">
                                Ultimate Edition
                            </h4>
                            <p class="text-lg text-gray-600 mt-2">Montly</p>
                            <p class="text-lg text-gray-600 mt-2">
                                download, updates & extras
                            </p>
                            <p class="text-4xl text-primary my-4 font-bold">
                                ₱999
                            </p>
                            <div class="p-4 card-body">
                                <ul class="">
                                    <li class="pt-4">
                                        Communicate with Workers
                                    </li>
                                    <li class="pt-4">Hire Workers</li>
                                    <li class="pt-4">
                                        Create up to <strong>3</strong> jobs per
                                        month
                                    </li>
                                    <li class="pt-4">
                                        <strong>Contact 75 workers</strong> /
                                        month
                                    </li>

                                    <li class="pt-4">
                                        <a href=""
                                            ><u>Workers Mentoring System</u></a
                                        >
                                    </li>
                                    <li class="pt-4">
                                        Unlimited
                                        <a href=""
                                            ><u>Background Data Check</u></a
                                        >
                                    </li>
                                </ul>
                            </div>
                            <a
                                href="#"
                                class="mt-4 inline-block bg-transparent border border-blue-500 text-blue-500 font-bold py-2 px-4 rounded-lg hover:bg-blue-500 hover:text-white transition"
                            >
                                Buy Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--MONEY back-->

        <div class="m-10 sm:flex pb-6 flex justify-center items-center">
            <a
                href="#"
                class="w-full max-w-sm sm:max-w-xl flex flex-col items-center bg-white border border-gray-200 rounded-lg shadow md:flex-row hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 mx-auto"
            >
                <div class="flex flex-col justify-between p-4 leading-normal">
                    <h5
                        class="mb-2 text-lg font-bold tracking-tight text-gray-900 dark:text-white sm:text-2xl"
                    >
                        100% Money Back Guarantee. If you don't find someone
                        great, we'll give you your money back.
                    </h5>
                    <p
                        class="mb-3 text-sm font-normal text-gray-700 dark:text-gray-400 sm:text-base"
                    >
                        NO CONTRACTS. NO COMMITMENTS. NO EXTRA FEES. NO SALARY
                        MARKUPS. CANCEL SUBSCRIPTION AT ANYTIME.
                    </p>
                    <p class="mb-3 font-bold text-gray-700 dark:text-gray-400">
                        Find A Great Worker!
                    </p>
                </div>
            </a>
        </div>

        <div class="p-5 flex justify-center items-center sm:flex">
            <p>Proudly based in</p>
            <strong class="ml-1">Pangasinan, Philippines</strong>
        </div>
    </section>

    <!--FAQ-->
    <section class="mb-5">
        <div class="">
            <h2 class="text-center m-10 text-gray-500 text-[40px]">
                Frequently Asked Questions
            </h2>
        </div>

        <div class="mx-4 sm:mx-10 md:mx-20 lg:mx-40 mt-0">
            <!-- Section 1 -->
            <div class="mx-4 sm:mx-10 md:mx-20 lg:mx-40 mt-4">
                <span class="text-lg font-bold text-gray-500 mb-2 block"
                    >Account and Pricing</span
                >
                <div class="border border-gray-300 rounded-lg shadow-md">
                    <button
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-100 text-gray-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                        @click="toggleCollapse('section1')"
                    >
                        <span>What does the free account get me?</span>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 transition-transform"
                            :class="{ 'rotate-180': !collapseState.section1 }"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>
                    </button>
                    <div
                        class="px-4 py-3 bg-white text-gray-700 transition"
                        :class="{ hidden: collapseState.section1 }"
                    >
                        Free allows you to post a job and see job applications.
                        It gives you a good idea of if you're going to find
                        someone good. You can't see applicants contact info or
                        communicate with them until you upgrade.
                        <p class="pt-2">
                            <span
                                >There's a
                                <a
                                    href=""
                                    class="text-blue-500 hover:text-blue-700"
                                    >good reason we don't offer a free trial.</a
                                ></span
                            >
                        </p>
                        <p class="pt-2">
                            <span
                                >You can only interview workers once you've paid
                                for an account.</span
                            >
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section 2 -->
            <div class="mx-4 sm:mx-10 md:mx-20 lg:mx-40 mt-4">
                <div class="border border-gray-300 rounded-lg shadow-md">
                    <button
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-100 text-gray-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                        @click="toggleCollapse('section2')"
                    >
                        <span
                            >Why can't I contact workers with the free
                            account?</span
                        >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 transition-transform"
                            :class="{ 'rotate-180': !collapseState.section2 }"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>
                    </button>
                    <div
                        class="px-4 py-3 bg-white text-gray-700 transition"
                        :class="{ hidden: collapseState.section2 }"
                    >
                        If we let employers contact workers without paying,<a
                            href="#"
                            class="text-blue-500 hover:text-blue-700"
                        >
                            they start to get scammy then workers respond the
                            same way, and it turns into a cess pool of scams.</a
                        >
                        <p>
                            <span
                                >Plus, it's how we make money and how we stay in
                                business.</span
                            >
                        </p>
                        <p>
                            You can look at full profiles and post a job before
                            paying. With the job post you'll get to see all job
                            applications so you can see if people are interested
                            in your position and if they have good skills. You
                            just won't be able to talk with them until you pay.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section 3 -->
            <div class="mx-4 sm:mx-10 md:mx-20 lg:mx-40 mt-4">
                <div class="border border-gray-300 rounded-lg shadow-md">
                    <button
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-100 text-gray-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                        @click="toggleCollapse('section3')"
                    >
                        <span
                            >Why do I have to pay up front? Seems more expensive
                            and risky for me.</span
                        >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 transition-transform"
                            :class="{ 'rotate-180': !collapseState.section3 }"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>
                    </button>
                    <div
                        class="px-4 py-3 bg-white text-gray-700 transition"
                        :class="{ hidden: collapseState.section3 }"
                    >
                        <p>
                            If you consider our money back guarantee, there's
                            not much risk. If you don't find someone, ask for
                            your money back. We're a US based company. We honor
                            our guarantee. We charge up front so we don't have
                            to mark up salaries. Nobody likes middlemen and
                            unnecessary markups. After you try it we think
                            you'll agree. Very few people who discover
                            OnlineJobs.ph ever go back to the old way of
                            oursourcing (upwork, freelancer, fiverr...).
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mx-4 sm:mx-10 md:mx-20 lg:mx-40 mt-0">
            <!-- Section 1 -->
            <div class="mx-4 sm:mx-10 md:mx-20 lg:mx-40 mt-4">
                <span class="text-lg font-bold text-gray-500 mb-2 block"
                    >Cancelling Your Subscription</span
                >
                <div class="border border-gray-300 rounded-lg shadow-md">
                    <button
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-100 text-gray-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                        @click="toggleCollapse('section4')"
                    >
                        <span>What does the free account get me?</span>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 transition-transform"
                            :class="{ 'rotate-180': !collapseState.section4 }"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>
                    </button>
                    <div
                        class="px-4 py-3 bg-white text-gray-700 transition"
                        :class="{ hidden: collapseState.section4 }"
                    >
                        Free allows you to post a job and see job applications.
                        It gives you a good idea of if you're going to find
                        someone good. You can't see applicants contact info or
                        communicate with them until you upgrade.
                        <p class="pt-2">
                            <span
                                >There's a
                                <a
                                    href=""
                                    class="text-blue-500 hover:text-blue-700"
                                    >good reason we don't offer a free trial.</a
                                ></span
                            >
                        </p>
                        <p class="pt-2">
                            <span
                                >You can only interview workers once you've paid
                                for an account.</span
                            >
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section 2 -->
            <div class="mx-4 sm:mx-10 md:mx-20 lg:mx-40 mt-4">
                <div class="border border-gray-300 rounded-lg shadow-md">
                    <button
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-100 text-gray-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                        @click="toggleCollapse('section5')"
                    >
                        <span>How does Tailwind work?</span>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 transition-transform"
                            :class="{ 'rotate-180': !collapseState.section5 }"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>
                    </button>
                    <div
                        class="px-4 py-3 bg-white text-gray-700 transition"
                        :class="{ hidden: collapseState.section5 }"
                    >
                        Tailwind works by providing utility classes for building
                        responsive and custom UIs without writing traditional
                        CSS.
                    </div>
                </div>
            </div>

            <!-- Section 3 -->
            <div class="mx-4 sm:mx-10 md:mx-20 lg:mx-40 mt-4">
                <div class="border border-gray-300 rounded-lg shadow-md">
                    <button
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-100 text-gray-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                        @click="toggleCollapse('section6')"
                    >
                        <span>How does Tailwind work?</span>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 transition-transform"
                            :class="{ 'rotate-180': !collapseState.section6 }"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>
                    </button>
                    <div
                        class="px-4 py-3 bg-white text-gray-700 transition"
                        :class="{ hidden: collapseState.section6 }"
                    >
                        Tailwind works by providing utility classes for building
                        responsive and custom UIs without writing traditional
                        CSS.
                    </div>
                </div>
            </div>
        </div>

        <div class="mx-4 sm:mx-10 md:mx-20 lg:mx-40 mt-0">
            <!-- Section 1 -->
            <div class="mx-4 sm:mx-10 md:mx-20 lg:mx-40 mt-4">
                <span class="text-lg font-bold text-gray-500 mb-2 block"
                    >Trust</span
                >
                <div class="border border-gray-300 rounded-lg shadow-md">
                    <button
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-100 text-gray-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                        @click="toggleCollapse('section7')"
                    >
                        <span
                            >How do I trust a virtual assistant with my business
                            info?</span
                        >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 transition-transform"
                            :class="{ 'rotate-180': !collapseState.section7 }"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>
                    </button>
                    <div
                        class="px-4 py-3 bg-white text-gray-700 transition"
                        :class="{ hidden: collapseState.section7 }"
                    >
                        Free allows you to post a job and see job applications.
                        It gives you a good idea of if you're going to find
                        someone good. You can't see applicants contact info or
                        communicate with them until you upgrade.
                        <p class="pt-2">
                            <span
                                >There's a
                                <a
                                    href=""
                                    class="text-blue-500 hover:text-blue-700"
                                    >good reason we don't offer a free trial.</a
                                ></span
                            >
                        </p>
                        <p class="pt-2">
                            <span
                                >You can only interview workers once you've paid
                                for an account.</span
                            >
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section></section>
</template>
