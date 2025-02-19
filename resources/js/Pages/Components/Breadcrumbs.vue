<script setup>
defineProps({
    steps: {
        type: Array,
    },
});
</script>
<template>
    <div>
        <div class="mb-4">
            <h1 class="text-[28px]">Lets Get You Started!</h1>
        </div>
        <div
            class="flex flex-col items-start justify-around gap-2 font-semibold sm:flex-row"
        >
            <p
                v-for="(step, index) in steps"
                :key="index"
                :class="{ 'text-blue-500': step.active }"
            >
                <i
                    :class="[
                        'bi',
                        {
                            'bi-check-circle-fill text-green-500': step.done,
                            'bi-check-circle': !step.done,
                        },
                    ]"
                ></i>
                {{ step.name }}
            </p>
        </div>
    </div>
</template>
