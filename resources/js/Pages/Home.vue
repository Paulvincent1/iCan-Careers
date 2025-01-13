<script setup>
import Rating from "./Components/Rating.vue";
import Searchbar from "./Components/Searchbar.vue";
</script>

<template>
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

                        <form
                            action="
                        "
                        >
                            <Rating id="2"></Rating>
                            <Rating id="1"></Rating>
                        </form>
                    </div>
                </div>

                <div
                    class="relative 2xl:left-[-20px] 2xl:top-[-300px] 2xl:w-[500px]"
                >
                    <img
                        class="absolute left-0 top-0 h-[600px] w-[1000px] max-w-none"
                        src="storage/assets/hero.jpg"
                        alt="hero-section"
                    />
                </div>
            </div>
        </div>
    </section>
</template>
