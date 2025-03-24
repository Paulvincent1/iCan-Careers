<script setup>
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
    faFacebook,
    faYoutube,
    faLinkedin,
    faTwitter,
} from "@fortawesome/free-brands-svg-icons";
import { Link } from "@inertiajs/vue3";

// Add the icons only for this component
library.add(faFacebook, faYoutube, faLinkedin, faTwitter);
</script>

<template>
    <footer class="bg-gray-900 py-12 text-white">
        <div
            class="container mx-auto grid grid-cols-1 gap-8 px-6 md:grid-cols-4"
        >
            <!-- About -->
            <div>
                <h3 class="text-lg font-bold">About iCan Careers</h3>
                <p class="mt-3 text-gray-400">
                    Connecting senior citizens and PWDs with meaningful job
                    opportunities. Empowering an inclusive workforce where
                    talent knows no limits.
                </p>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-lg font-bold">Quick Links</h3>
                <ul class="mt-3 space-y-2">
                    <li>
                        <a href="/" class="text-gray-400 hover:text-white"
                            >Home</a
                        >
                    </li>
                    <li>
                        <Link
                            :href="route('jobsearch')"
                            class="text-gray-400 hover:text-white"
                            >Find Jobs
                        </Link>
                    </li>
                    <li>
                        <a
                            href="/employers"
                            class="text-gray-400 hover:text-white"
                            >For Employers</a
                        >
                    </li>
                    <li>
                        <a
                            href="/pricing"
                            class="text-gray-400 hover:text-white"
                            >Pricing</a
                        >
                    </li>
                    <li>
                        <a
                            href="/learning"
                            class="text-gray-400 hover:text-white"
                            >Learning</a
                        >
                    </li>
                    <li>
                        <a href="/" class="text-gray-400 hover:text-white"
                            >FAQs</a
                        >
                    </li>
                </ul>
            </div>

            <!-- Contact Information -->
            <div>
                <h3 class="text-lg font-bold">Contact Us</h3>
                <p class="mt-3 text-gray-400">
                    📍 123 Amansabina, Mangaldan, Pangasinan
                </p>
                <p class="text-gray-400">📧 support@ican-careers.com</p>
                <p class="text-gray-400">📞 +63 912 345 6789</p>
                <p class="text-gray-400">🕒 Mon - Fri: 9 AM - 5 PM</p>
            </div>

            <!-- Social Media -->
            <div>
                <h3 class="text-lg font-bold">Follow Us</h3>
                <div class="mt-3 flex space-x-4">
                    <a
                        href="https://www.facebook.com/profile.php?id=61574488294916"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-gray-400 hover:text-white"
                    >
                        <font-awesome-icon
                            :icon="['fab', 'facebook']"
                            size="lg"
                        />
                    </a>
                    <a
                        href="https://www.youtube.com/@ICanCareers-d3l"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-gray-400 hover:text-white"
                    >
                        <font-awesome-icon
                            :icon="['fab', 'youtube']"
                            size="lg"
                        />
                    </a>
                </div>
            </div>
        </div>

        <!-- Bottom Footer -->
        <div
            class="mt-8 border-t border-gray-700 pt-4 text-center text-sm text-gray-500"
        >
            <p>© 2025 iCan Careers. All rights reserved.</p>
        </div>
    </footer>
</template>
