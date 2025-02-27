<script setup>
import { router, useForm, usePage } from "@inertiajs/vue3";
import {
    computed,
    nextTick,
    onMounted,
    onUpdated,
    ref,
    useTemplateRef,
    watch,
} from "vue";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";
import MessageBox from "../Components/MessageBox.vue";
import { nanoid } from "nanoid";
import { debounce } from "lodash";

let props = defineProps({
    chatHeadProps: null,
    firstMessageChatHeadProps: null,
    messageProps: null,
});

let chatContainer = useTemplateRef("chat-container");
let messageInput = useTemplateRef("messageInput");

function loadMessages() {
    nextTick(() => {
        chatContainer.value.scrollTo({
            top: chatContainer.value.scrollHeight,
            behavior: "smooth",
        });
    });
    chatContainer.value.addEventListener("scroll", loadMoreMessagesDebounce);
}

let startedFromTheBottom = ref(false);
let highestPage = ref(null);
let lowestPage = ref(null);

const loadMoreMessagesDebounce = debounce(() => {
    if (chatContainer.value.scrollTop <= 0) {
        const previousScrollHeight = chatContainer.value.scrollHeight;

        if (props.messageProps.next_page_url > highestPage.value) {
            if (
                props.messageProps.current_page === 1 &&
                messages.value.length <= 20
            ) {
                startedFromTheBottom.value = true;
            }

            router.get(
                props.messageProps.next_page_url,
                {},
                {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: () => {
                        messages.value = [
                            ...props.messageProps.data.reverse(),
                            ...messages.value,
                        ];
                        nextTick(() => {
                            chatContainer.value.scrollTop +=
                                chatContainer.value.scrollHeight -
                                previousScrollHeight;
                        });
                    },
                },
            );
        }
    }
    if (
        chatContainer.value.scrollTop +
            chatContainer.value.getBoundingClientRect().height >=
        chatContainer.value.scrollHeight
    ) {
        const previousScrollHeight = chatContainer.value.scrollHeight;
        if (props.messageProps.prev_page_url) {
            if (startedFromTheBottom.value) {
                console.log(startedFromTheBottom.value);
                return;
            }

            router.get(
                props.messageProps.prev_page_url,
                {},
                {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: () => {
                        if (!startedFromTheBottom.value) {
                            console.log("called");
                            messages.value = [
                                ...messages.value,
                                ...props.messageProps.data.reverse(),
                            ];
                        }

                        if (props.messageProps.current_page === 1) {
                            startedFromTheBottom.value = true;
                        }

                        nextTick(() => {
                            chatContainer.value.scrollTop =
                                previousScrollHeight;
                        });
                    },
                },
            );
        }
    }
}, 500);

let chatHeads = ref(props.chatHeadProps ?? []);
let messages = ref(props.messageProps?.data.reverse() ?? null);

onMounted(() => {
    console.log(props.messageProps);
    highestPage.value = props.messageProps.current_page;
    lowestPage.value = props.messageProps.current_page;

    if (props.firstMessageChatHeadProps) {
        chatHeads.value.unshift(props.firstMessageChatHeadProps);
    }
    if (route().params.user) {
        listenToChannelMessage(route().params.user);
    }

    loadMessages();
    updateMessageInputVisibility();
});

let page = usePage();
async function sendMessage() {
    if (messageInput.value.value) {
        messages.value.push({
            id: nanoid(),
            message: messageInput.value.value,
            sender_id: page.props.auth.user.authenticated.id,
            receiver_id: route().params.user,
        });
        loadMessages();
        const messageToBeSent = messageInput.value.value;
        messageInput.value.value = null;

        const response = await fetch(
            route("messages.send", {
                receiverId: route().params.user,
            }),
            {
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": page.props.csrf_token,
                },
                method: "POST",
                body: JSON.stringify({ message: messageToBeSent }),
            },
        );
        console.log(response.ok);

        console.log(messages.value);
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
            onSuccess: () => {
                messages.value = props.messageProps?.data.reverse();
                updateMessageInputVisibility();
                loadMessages();

                listenToChannelMessage(id);
            },
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

//pusher
function listenToChannelMessage(senderId) {
    window.onload = () => {
        window.Echo.channel(
            "message-" + page.props.auth.user.authenticated.id + "-" + senderId,
        ).listen(".message.event", (e) => {
            console.log(e);
            messages.value.push({
                id: e.id,
                message: e.message,
                sender_id: e.sender_id,
                receiver_id: e.receiver_id,
            });

            loadMessages();
        });
    };
}
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
                            'flex cursor-pointer gap-2 p-4',
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
                                {{ chatHead.latestMessage?.message }}
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
                            <MessageBox
                                v-for="message in messages"
                                :key="message.id"
                                :message="message"
                            ></MessageBox>
                            <!-- <div
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
                            </div> -->
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
