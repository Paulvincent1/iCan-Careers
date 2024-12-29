<script setup>
import Searchbar from "./Components/Searchbar.vue";
</script>

<template>
    <div class="container mx-auto px-[0.5rem] py-4 md:max-w-7xl">
        <div class="flex flex-col">
            <div>
                <p class="text-center text-[32px]">
                    Where Others Can't, We Do.
                </p>
                <div class="flex flex-col">
                    <label class="mb-2">Looking for Talent?</label>
                    <Searchbar endpoint="/" class="mb-4" />
                    <label class="mb-2">Looking for Work?</label>
                    <Searchbar endpoint="/" />
                </div>
            </div>
            <div>
                dsd
                <img src="" alt="" />
            </div>
        </div>
    </div>
</template>
