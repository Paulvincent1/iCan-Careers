<script setup>
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faFacebook, faInstagram, faLinkedin, faTwitter } from "@fortawesome/free-brands-svg-icons";

// Add the icons only for this component
library.add(faFacebook, faInstagram, faLinkedin, faTwitter);
</script>

<template>
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-4 gap-8 px-6">
            <!-- About -->
            <div>
                <h3 class="text-lg font-bold">About iCan Careers</h3>
                <p class="mt-3 text-gray-400">
                    Connecting senior citizens and PWDs with meaningful job opportunities. 
                    Empowering an inclusive workforce where talent knows no limits.
                </p>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-lg font-bold">Quick Links</h3>
                <ul class="mt-3 space-y-2">
                    <li><a href="/" class="text-gray-400 hover:text-white">Home</a></li>
                    <li><a href="/jobs" class="text-gray-400 hover:text-white">Find Jobs</a></li>
                    <li><a href="/employers" class="text-gray-400 hover:text-white">For Employers</a></li>
                    <li><a href="/stories" class="text-gray-400 hover:text-white">Success Stories</a></li>
                    <li><a href="/faq" class="text-gray-400 hover:text-white">FAQs</a></li>
                </ul>
            </div>

            <!-- Contact Information -->
            <div>
                <h3 class="text-lg font-bold">Contact Us</h3>
                <p class="mt-3 text-gray-400">📍 123 Main St, Manila, Philippines</p>
                <p class="text-gray-400">📧 support@ican-careers.com</p>
                <p class="text-gray-400">📞 +63 912 345 6789</p>
                <p class="text-gray-400">🕒 Mon - Fri: 9 AM - 5 PM</p>
            </div>

            <!-- Social Media -->
            <div>
                <h3 class="text-lg font-bold">Follow Us</h3>
                <div class="mt-3 flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white">
                        <font-awesome-icon :icon="['fab', 'facebook']" size="lg" />
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white">
                        <font-awesome-icon :icon="['fab', 'instagram']" size="lg" />
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white">
                        <font-awesome-icon :icon="['fab', 'linkedin']" size="lg" />
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white">
                        <font-awesome-icon :icon="['fab', 'twitter']" size="lg" />
                    </a>
                </div>
            </div>
        </div>

        <!-- Bottom Footer -->
        <div class="mt-8 text-center text-gray-500 text-sm border-t border-gray-700 pt-4">
            <p>© 2025 iCan Careers. All rights reserved.</p>
            <a href="/privacy" class="hover:text-white">Privacy Policy</a> | 
            <a href="/terms" class="hover:text-white">Terms & Conditions</a>
        </div>
    </footer>
</template>
