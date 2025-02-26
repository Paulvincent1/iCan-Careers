<script setup>
import { router, usePage } from "@inertiajs/vue3";
import { computed, onMounted, ref, useTemplateRef, watch } from "vue";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";

let props = defineProps({
    chatHeadProps: null,
    firstMessageChatHeadProps: null,
    messageProps: null,
});

let chatContainer = useTemplateRef("chat-container");
let messageInput = useTemplateRef("messageInput");

let chatHeads = ref(props.chatHeadProps ?? []);
let messages = computed(() => props.messageProps);

onMounted(() => {
    // console.log(chatContainer.value.scrollHeight);
    chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    chatContainer.value.addEventListener("scroll", () => {
        if (chatContainer.value.scrollTop <= 0) {
            //load more
            console.log("hi");
        }
    });

    if (props.firstMessageChatHeadProps) {
        chatHeads.value.unshift(props.firstMessageChatHeadProps);
    }

    console.log(chatHeads.value);
});

watch(
    () => props.chatHeadProps,
    () => {},
);

let page = usePage();
function sendMessage() {
    console.log(messageInput.value.value);

    if (messageInput.value.value) {
        router.post(
            route("messages.send", {
                receiverId: route().params.user,
            }),
            {
                message: messageInput.value.value,
            },
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    messages.value.push({
                        message: messageInput.value,
                        sender_id: page.props.auth.user.authenticated.id,
                        receiver_id: route().params.user,
                    });
                },
            },
        );
    }
}

function switchChat(id) {
    router.get(
        route("messages"),
        {
            user: id,
        },
        {
            preserveScroll: true,
            preserveState: true,
        },
    );
}

let isShowMessageInput = ref(false);
function updateMessageInputVisibility() {
    if (route().params.user) {
        let res = chatHeads.value.find((chatHead) => {
            return chatHead.user.id === Number(route().params.user);
        });
        console.log(res);

        if (res) {
            isShowMessageInput.value = true;
        } else {
            isShowMessageInput.value = false;
        }
    } else {
        isShowMessageInput.value = false;
    }
}
onMounted(() => {
    updateMessageInputVisibility();
});
</script>
<template>
    <div
        class="mx-auto min-h-[calc(100vh-4.625rem)] max-w-7xl p-8 px-[0.5rem] md:h-[calc(100vh-4.625rem)]"
    >
        <div
            class="grid h-full min-h-[650px] w-full grid-cols-1 border md:grid-cols-[300px,1fr]"
        >
            <div class="flex flex-col border-b md:border-none">
                <div>
                    <div class="border-b p-4">
                        <p>Paul Vincent</p>
                    </div>
                    <div class="px-4">
                        <input
                            type="text"
                            class="my-5 w-full rounded-full border p-2 px-6"
                            placeholder="Search Applicant Name"
                        />
                    </div>
                </div>
                <div class="flex-1 basis-1 overflow-auto">
                    <div
                        v-for="(chatHead, index) in chatHeads"
                        @click="switchChat(chatHead.user.id)"
                        :key="chatHead.id"
                        :class="[
                            'flex gap-2 p-4',
                            {
                                'bg-slate-300':
                                    chatHead.user.id ===
                                    Number(route().params.user),
                            },
                        ]"
                    >
                        <div class="h-12 w-12">
                            <img
                                src="/assets/profile_placeholder.jpg"
                                alt=""
                                class="h-full w-full rounded-full"
                            />
                        </div>
                        <div>
                            <p>{{ chatHead.user.email }}</p>
                            <p class="text-sm">
                                {{ chatHead.latestMessage.message }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col border-l">
                <div class="flex flex-1 flex-col">
                    <div class="border-b p-4">
                        <p>Nathan</p>
                    </div>
                    <div class="flex flex-1 flex-col">
                        <div
                            ref="chat-container"
                            class="chat-container overflow-auto p-4"
                        >
                            <div
                                v-for="message in messages"
                                :class="[
                                    'mb-6 flex justify-start',
                                    {
                                        'justify-end':
                                            message.sender_id ===
                                            $page.props.auth.user.authenticated
                                                .id,
                                    },
                                ]"
                            >
                                <div
                                    class="w-fit max-w-lg rounded-lg border px-5 py-3"
                                >
                                    <p>
                                        {{ message.message }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="isShowMessageInput" class="p-4">
                    <div class="flex gap-3">
                        <input
                            ref="messageInput"
                            type="text"
                            class="w-full rounded-full border p-2"
                        />
                        <button @click="sendMessage">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
.chat-container {
    flex: 1 1 400px;
    overflow-y: auto;
}
</style>
