<script setup>
defineProps({
    steps: {
        type: Array,
    },
});
</script>
<template>
    <div class="rounded-lg bg-white p-5">
        <div class="mb-4">
            <h1 class="text-[28px] font-bold">Lets Get You Started!</h1>
        </div>
        <div class="flex flex-col items-start gap-5 sm:flex-row">
            <p
                v-for="(step, index) in steps"
                :key="index"
                :class="[
                    'text-gray-800',
                    {
                        'text-orange-600': step.active,
                        'done text-orange-600': step.done,
                    },
                ]"
            >
                <i
                    :class="[
                        'bi',
                        {
                            'bi-check-circle-fill text-orange6500': step.done,
                            'bi-check-circle': !step.done,
                        },
                    ]"
                ></i>
                {{ step.name }}
                <i
                    v-show="steps.length - 1 != index"
                    class="bi bi-caret-right-fill"
                ></i>
            </p>
        </div>
    </div>
</template>
