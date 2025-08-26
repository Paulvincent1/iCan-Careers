<script setup>
import { router, useForm, usePage } from "@inertiajs/vue3";
import {
    computed,
    nextTick,
    onBeforeMount,
    onBeforeUnmount,
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
    userDirectMessageProps: null,
    user: {
        type: Object,
        required: true,
    },
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
let loadedPages = ref([]);

function nextPageUrlVisited() {
    if (props.messageProps?.next_page_url) {
        const url = new URL(props.messageProps?.next_page_url);
        const nextPageNumber = Number(url.searchParams.get("page"));

        const isNextPageNumberVisited = loadedPages.value.find(
            (pageNumber) => pageNumber === nextPageNumber,
        );
        return isNextPageNumberVisited;
    }
    return null;
}

function previousPageVisited() {
    if (props.messageProps?.prev_page_url) {
        const url = new URL(props.messageProps?.prev_page_url);
        const prevPageNumber = Number(url.searchParams.get("page"));

        const isPrevPageNumberVisited = loadedPages.value.find(
            (pageNumber) => pageNumber === prevPageNumber,
        );

        return isPrevPageNumberVisited;
    }
    return null;
}

const loadMoreMessagesDebounce = debounce(() => {
    if (chatContainer.value.scrollTop <= 0) {
        const previousScrollHeight = chatContainer.value.scrollHeight;

        let isNextPageNumberVisited = nextPageUrlVisited();
        console.log(isNextPageNumberVisited);

        if (isNextPageNumberVisited) {
            let pageToCheck = isNextPageNumberVisited + 1;

            //check if the next next page after the next page is visited.
            while (!loadedPages.value.includes(pageToCheck)) {
                pageToCheck++;
            }

            console.log(loadedPages.value);
            if (!loadedPages.value.includes(pageToCheck)) {
                const nextNextPageValid = props.messageProps.links.find(
                    (link) => {
                        const url = new URL(
                            "http://127.0.0.1:8000/messages?user=1&page=1",
                        );

                        url.searchParams.set("page", pageToCheck);

                        console.log(link.url);
                        console.log(url.toString());
                        return link.url === url.toString();
                    },
                );
                console.log(nextNextPageValid);

                if (nextNextPageValid) {
                    console.log("trigger is nextNextValid");
                    router.get(
                        nextNextPageValid.url,
                        {},
                        {
                            preserveState: true,
                            preserveScroll: true,
                            onSuccess: () => {
                                messages.value = [
                                    ...props.messageProps.data.reverse(),
                                    ...messages.value,
                                ];
                                loadedPages.value.push(
                                    props.messageProps?.current_page,
                                );
                                nextTick(() => {
                                    chatContainer.value.scrollTop +=
                                        chatContainer.value.scrollHeight -
                                        previousScrollHeight;
                                    loadedPages.value = [
                                        ...new Set([...loadedPages.value]),
                                    ];
                                });
                            },
                        },
                    );

                    return;
                }
            }
        }

        if (props.messageProps?.next_page_url && !isNextPageNumberVisited) {
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
                        loadedPages.value.push(
                            props.messageProps?.current_page,
                        );
                        nextTick(() => {
                            chatContainer.value.scrollTop +=
                                chatContainer.value.scrollHeight -
                                previousScrollHeight;
                            loadedPages.value = [
                                ...new Set([...loadedPages.value]),
                            ];
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
        let isPrevPageNumberVisited = previousPageVisited();
        if (isPrevPageNumberVisited) {
            if (isPrevPageNumberVisited != 1) {
                const prevPrevPageVisited = loadedPages.value.find(
                    (pageNumber) => isPrevPageNumberVisited - 1 === pageNumber,
                );

                if (!prevPrevPageVisited) {
                    if (prevPrevPageVisited != 1) {
                        const prevPrevPageValid = props.messageProps.links.find(
                            (link) => {
                                const url = new URL(
                                    "http://127.0.0.1:8000/messages?user=1&page=1",
                                );
                                url.searchParams.set(
                                    "page",
                                    isPrevPageNumberVisited - 1,
                                );

                                return link.url === url.toString();
                            },
                        );

                        if (prevPrevPageValid) {
                            router.get(
                                prevPrevPageValid.url,
                                {},
                                {
                                    preserveState: true,
                                    preserveScroll: true,
                                    onSuccess: () => {
                                        if (!startedFromTheBottom.value) {
                                            messages.value = [
                                                ...messages.value,
                                                ...props.messageProps.data.reverse(),
                                            ];
                                        }

                                        if (
                                            props.messageProps.current_page ===
                                            1
                                        ) {
                                            startedFromTheBottom.value = true;
                                        }

                                        nextTick(() => {
                                            chatContainer.value.scrollTop =
                                                previousScrollHeight;
                                        });
                                        loadedPages.value.push(
                                            props.messageProps?.current_page,
                                        );
                                        loadedPages.value = [
                                            ...new Set([...loadedPages.value]),
                                        ];
                                    },
                                },
                            );
                            return;
                        }
                    }
                }
            }
        }

        const previousScrollHeight = chatContainer.value.scrollHeight;
        if (props.messageProps.prev_page_url && !isPrevPageNumberVisited) {
            if (startedFromTheBottom.value) {
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
                        loadedPages.value.push(
                            props.messageProps?.current_page,
                        );
                        loadedPages.value = [
                            ...new Set([...loadedPages.value]),
                        ];
                    },
                },
            );
        }
    }
}, 500);

let chatHeads = ref(props.chatHeadProps ?? []);
console.log(props.userDirectMessageProps);

let messages = ref(props.messageProps?.data.reverse() ?? null);

onMounted(() => {
    chatHeadVisibleToScreen();
    listenChannelChatHeads();

    if (props.messageProps?.current_page) {
        if (
            props.messageProps.current_page != 1 &&
            props.messageProps.data.length < 20
        ) {
            loadedPages.value.push(props.messageProps?.current_page);
            loadedPages.value = [...new Set([...loadedPages.value])];
            router.get(
                props.messageProps.prev_page_url,
                {},
                {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: () => {
                        if (!startedFromTheBottom.value) {
                            messages.value = [
                                ...messages.value,
                                ...props.messageProps.data.reverse(),
                            ];
                        }

                        if (props.messageProps.current_page === 1) {
                            startedFromTheBottom.value = true;
                        }

                        nextTick(() => {
                            loadedPages.value.push(
                                props.messageProps?.current_page,
                            );
                            loadedPages.value = [
                                ...new Set([...loadedPages.value]),
                            ];
                        });
                    },
                },
            );

            console.log(loadedPages.value);
        }
    }

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
    console.log(chatHeads.value);

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

        const newChatHead = {
            latestMessage: {
                id: nanoid(),
                message: messageToBeSent,
                receiver_id: route().params.user,
                sender_id: page.props.auth.user.authenticated.id,
            },
            user: props.userDirectMessageProps,
        };

        unshiftLatestChatHead(route().params.user, newChatHead);

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
        // console.log(response.ok);

        // console.log(messages.value);
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
                loadedPages.value = [1];
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
        if (res) {
            isShowMessageInput.value = true;
        } else {
            isShowMessageInput.value = false;
        }
    } else {
        isShowMessageInput.value = false;
    }
}

let channel = null;
//pusher
function listenToChannelMessage(senderId) {
    const channelName =
        "message-" + page.props.auth.user.authenticated.id + "-" + senderId;
    console.log("listen");

    if (window.Echo.connector.channels[channelName]?.subscription) {
        if (
            !window.Echo.connector.channels[channelName]?.subscription
                .subscribed
        ) {
            window.Echo.connector.channels[channelName].subscribe();
            return;
        }
    }

    channel = window.Echo.channel(channelName).listen(".message.event", (e) => {
        messages.value.push({
            id: e.id,
            message: e.message,
            sender_id: e.sender_id,
            receiver_id: e.receiver_id,
        });
        console.log("listen");

        loadMessages();
    });
}

let channelChatHeads = null;
function listenChannelChatHeads() {
    const channelName = "chathead-" + page.props.auth.user.authenticated.id;

    console.log(
        window.Echo.connector.channels[channelName]?.subscription.subscribed,
    );

    if (window.Echo.connector.channels[channelName]?.subscription) {
        if (
            !window.Echo.connector.channels[channelName]?.subscription
                .subscribed
        ) {
            console.log("pumasok");

            window.Echo.connector.channels[channelName].subscribe();
            return;
        }
    }

    channelChatHeads = window.Echo.channel(channelName).listen(
        ".message.event",
        (e) => {
            unshiftLatestChatHead(e.user.id, e);

            console.log(chatHeads.value);
        },
    );

    console.log(
        window.Echo.connector.channels[channelName] === channelChatHeads,
    );
    console.log(channelChatHeads);
}

function unshiftLatestChatHead(userId, newChatHead) {
    const index = chatHeads.value.find((ch) => {
        return Number(ch?.user.id) === Number(userId);
    });

    if (index) {
        if (index != 0) {
            chatHeads.value.splice(index, 1);
            chatHeads.value.unshift(newChatHead);
        } else {
            chatHeads.value[index] = newChatHead;
        }
    } else {
        chatHeads.value.unshift(newChatHead);
    }
}

let chatHeadContainer = useTemplateRef("chathead-container");
let refId;
let chatHeadActive;
function chatHeadVisibleToScreen() {
    if (route().params.user) {
        refId = "chat-head-" + route().params.user;
        chatHeadActive = useTemplateRef(refId);
        nextTick(() => {
            chatHeadContainer.value.scrollTo({
                top: chatHeadActive.value[0].offsetTop,
                behavior: "smooth",
            });
        });
    }
}

onBeforeUnmount(() => {
    channel?.stopListening(".message.event");
    channelChatHeads?.stopListening(".message.event");
});

let search = ref("");
watch(
    search,
    debounce(() => {
        router.get(
            route("messages"),
            {
                q: search.value,
            },
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    }, 500),
);
</script>
<template>
    <Head title="Message | iCan Careers" />
    <div class="bg-[#eff2f6]">
        <div
            class="mx-auto min-h-[calc(100vh-4.625rem)] max-w-7xl p-8 px-[0.5rem]"
        >
            <div
                class="grid h-full min-h-[650px] w-full grid-cols-1 rounded-xl bg-white md:grid-cols-[300px,1fr]"
            >
                <div class="flex flex-col border-b md:border-none">
                    <div>
                        <div class="flex items-center gap-2 border-b p-4">
                            <div class="h-10 w-10">
                                <img
                                    :src="
                                        user.profile_photo_path
                                            ? user.profile_photo_path
                                            : 'assets/profile_placeholder.jpg'
                                    "
                                    alt="User Profile"
                                    class="h-full w-full rounded-full object-cover"
                                />
                            </div>
                            <div>
                                <p>
                                    {{
                                        $page.props.auth.user?.authenticated
                                            .name
                                    }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    Info account
                                </p>
                            </div>
                        </div>
                        <div class="mt-5 px-4">
                            <div class="relative">
                                <input
                                    type="text"
                                    v-model="search"
                                    class="w-full rounded-full border p-2 px-8"
                                    placeholder="Search a username"
                                />
                                <i
                                    class="bi bi-search absolute left-3 top-[50%] translate-y-[-50%] text-[14px] text-gray-500"
                                ></i>
                            </div>
                            <p class="my-3 text-gray-500">Messages</p>
                        </div>
                    </div>
                    <div
                        ref="chathead-container"
                        class="relative min-h-44 flex-1 basis-1 overflow-auto"
                    >
                        <div v-if="!chatHeads.length" class="">
                            <p class="text-center text-gray-500">
                                No Messages Available.
                            </p>
                        </div>
                        <div
                            v-for="(chatHead, index) in chatHeads"
                            @click="switchChat(chatHead.user.id)"
                            :key="chatHead.latestMessage?.id"
                            :class="[
                                'flex cursor-pointer gap-2 p-4',
                                {
                                    'bg-orange-400 text-white':
                                        chatHead.user.id ===
                                        Number(route().params.user),
                                },
                            ]"
                            :ref="`chat-head-${chatHead.user.id}`"
                        >
                            <div class="h-12 w-12">
                                <img
                                    :src="
                                        chatHead.user.profile_img ??
                                        '/assets/profile_placeholder.jpg'
                                    "
                                    alt=""
                                    class="h-full w-full rounded-full"
                                />
                            </div>
                            <div>
                                <p class="font-bold">
                                    {{ chatHead.user.email }}
                                </p>
                                <p class="text-sm">
                                    {{
                                        chatHead.latestMessage?.message ??
                                        "No Message yet"
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col border-l">
                    <div class="flex flex-1 flex-col">
                        <div
                            class="flex h-[77px] items-center justify-between border-b p-4"
                        >
                            <div v-if="route().params.user" class="flex gap-2">
                                <div class="h-10 w-10">
                                    <img
                                        class="h-full w-full"
                                        :src="
                                            userDirectMessageProps.profile_img ??
                                            '/assets/profile_placeholder.jpg'
                                        "
                                        alt=""
                                    />
                                </div>
                                <div>
                                    <p>{{ userDirectMessageProps?.name }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ userDirectMessageProps?.email }}
                                    </p>
                                </div>
                            </div>
                            <div v-if="route().params.user">
                                <i class="bi bi-three-dots-vertical"></i>
                            </div>
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
                        <div class="relative">
                            <input
                                ref="messageInput"
                                type="text"
                                placeholder="Type your message..."
                                class="w-full rounded-full bg-[#f4f4f4] p-4 outline-none"
                            />
                            <button
                                @click="sendMessage"
                                class="absolute right-5 top-[50%] translate-y-[-50%]"
                            >
                                <i class="bi bi-send text-lg text-gray-500"></i>
                            </button>
                        </div>
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
