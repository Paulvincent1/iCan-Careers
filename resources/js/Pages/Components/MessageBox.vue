<script setup>
import { onMounted, onUnmounted, onUpdated } from "vue";

defineProps({
    message: null,
});
// onUpdated(() => {
//     console.log("updated");
// });
// onMounted(() => {
//     console.log("mounted");
// });
// onUnmounted(() => {
//     console.log("unmounted");
// });
</script>
<template>
    <div
        :class="[
            'mb-6 flex justify-start',
            {
                'justify-end':
                    message.sender_id ===
                    $page.props.auth.user.authenticated.id,
            },
        ]"
    >
        <div class="w-fit max-w-lg rounded-lg border px-5 py-3">
            <p>
                {{ message.message }}
            </p>
        </div>
    </div>
</template>
