<template>

  <div class="max-w-xs mx-auto m-5 transition-transform transform hover:scale-105">
    <div class="bg-white border shadow-lg" :class="borderColor">
      <div class="text-center py-6 ">
        <h4 class="text-xl font-semibold">{{ title }}</h4>
        <p v-if="subtitle" class="text-lg text-gray-600 mt-2">{{ subtitle }}</p>
        <p class="text-4xl text-primary my-4 font-bold">{{ price }}</p>
        <div class="p-4 card-body ">
          <ul>
            <li v-for="(feature, index) in features" :key="index" class="pt-4">
              <span v-if="feature.strike"><strike>{{ feature.text }}</strike></span>
              <span v-else>{{ feature.text }}</span>
            </li>
          </ul>

    <div class="m-5 mx-auto max-w-xs">
        <div class="border bg-white shadow-lg" :class="borderColor">
            <div class="py-6 text-center">
                <h4 class="text-xl font-semibold">{{ title }}</h4>
                <p v-if="subtitle" class="mt-2 text-lg text-gray-600">
                    {{ subtitle }}
                </p>
                <p class="text-primary my-4 text-4xl font-bold">{{ price }}</p>
                <div class="card-body p-4">
                    <ul>
                        <li
                            v-for="(feature, index) in features"
                            :key="index"
                            class="pt-4"
                        >
                            <span v-if="feature.strike"
                                ><p class="line-through">
                                    {{ feature.text }}
                                </p></span
                            >
                            <span v-else>{{ feature.text }}</span>
                        </li>
                    </ul>
                </div>
                <button
                    @click="$emit('buyNow', tag)"
                    class="mt-4 inline-block rounded-lg border border-blue-500 bg-transparent px-4 py-2 font-bold text-blue-500 transition hover:bg-blue-500 hover:text-white"
                >
                    Buy Now
                </button>
            </div>

        </div>
    </div>
</template>

<script setup>
defineProps({
    title: String,
    subtitle: String,
    price: String,
    features: Array,
    borderColor: String,
    tag: null,
});
</script>
