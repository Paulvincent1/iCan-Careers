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
        const now = new Date().toISOString();
        messages.value.push({
            id: nanoid(),
            message: messageInput.value.value,
            sender_id: page.props.auth.user.authenticated.id,
            receiver_id: route().params.user,
            created_at: now,
            timestamp: now
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
                created_at: now,
                timestamp: now
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
    const index = chatHeads.value.findIndex((ch) => {
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
        chatHeads.value[index] = newChatHead;
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

onMounted(() => {
    console.log(chatHeads.value);
});

// Helper functions for message timestamps
function formatMessageTime(timestamp) {
    if (!timestamp) {
        return '';
    }

    const date = new Date(timestamp);
    const now = new Date();
    const diffInHours = (now - date) / (1000 * 60 * 60);

    // If message is from today, show only time
    if (date.toDateString() === now.toDateString()) {
        return date.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
    }

    // If message is from yesterday
    const yesterday = new Date(now);
    yesterday.setDate(yesterday.getDate() - 1);
    if (date.toDateString() === yesterday.toDateString()) {
        return 'Yesterday ' + date.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
    }

    // If message is within this week
    if (diffInHours < 168) {
        return date.toLocaleDateString('en-US', {
            weekday: 'short',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
    }

    // Older messages
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined,
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
}

function formatChatHeadTime(timestamp) {
    if (!timestamp) {
        return '';
    }

    const date = new Date(timestamp);
    const now = new Date();
    const diffInMinutes = (now - date) / (1000 * 60);
    const diffInHours = diffInMinutes / 60;
    const diffInDays = diffInHours / 24;

    // Less than a minute
    if (diffInMinutes < 1) {
        return 'Just now';
    }

    // Less than an hour
    if (diffInMinutes < 60) {
        const mins = Math.floor(diffInMinutes);
        return `${mins}m`;
    }

    // Less than a day
    if (diffInHours < 24) {
        const hrs = Math.floor(diffInHours);
        return `${hrs}h`;
    }

    // Less than a week
    if (diffInDays < 7) {
        const days = Math.floor(diffInDays);
        return `${days}d`;
    }

    // Less than a month
    if (diffInDays < 30) {
        const weeks = Math.floor(diffInDays / 7);
        return `${weeks}w`;
    }

    // Show date for older messages
    if (date.getFullYear() === now.getFullYear()) {
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric'
        });
    }

    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: '2-digit'
    });
}

function formatDateSeparator(timestamp) {
    if (!timestamp) {
        return '';
    }

    const date = new Date(timestamp);
    const now = new Date();

    // Today
    if (date.toDateString() === now.toDateString()) {
        return 'Today';
    }

    // Yesterday
    const yesterday = new Date(now);
    yesterday.setDate(yesterday.getDate() - 1);
    if (date.toDateString() === yesterday.toDateString()) {
        return 'Yesterday';
    }

    // This year
    if (date.getFullYear() === now.getFullYear()) {
        return date.toLocaleDateString('en-US', {
            weekday: 'long',
            month: 'long',
            day: 'numeric'
        });
    }

    // Different year
    return date.toLocaleDateString('en-US', {
        weekday: 'long',
        month: 'long',
        day: 'numeric',
        year: 'numeric'
    });
}

function shouldShowDateSeparator(message, index) {
    if (index === 0) return true;

    const currentDate = new Date(message.created_at || message.timestamp || new Date());
    const previousMessage = messages.value[index - 1];
    const previousDate = new Date(previousMessage.created_at || previousMessage.timestamp || new Date());

    return currentDate.toDateString() !== previousDate.toDateString();
}
</script>
<template>
    <Head title="Message | iCan Careers" />
    <div class="bg-[#f8f9fa] min-h-screen">
        <div class="mx-auto min-h-[calc(100vh-4.625rem)] max-w-7xl p-4 lg:p-8">
            <!-- Main container with improved spacing and responsiveness -->
            <div class="grid h-full min-h-[700px] w-full grid-cols-1 rounded-2xl bg-white shadow-lg md:grid-cols-[350px,1fr] overflow-hidden">

                <!-- Left sidebar - Conversation list -->
                <aside class="flex flex-col border-b md:border-b-0 md:border-r border-gray-200" role="navigation" aria-label="Conversations">

                    <!-- User profile section with better visibility -->
                    <header class="border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50">
                        <div class="flex items-center gap-3 p-5">
                            <div class="h-14 w-14 ring-2 ring-white shadow-sm rounded-full overflow-hidden">
                                <img
                                    :src="user.profile_photo_path || '/assets/profile_placeholder.jpg'"
                                    :alt="`${$page.props.auth.user?.authenticated.name}'s profile`"
                                    class="h-full w-full object-cover"
                                />
                            </div>
                            <div class="flex-1">
                                <h2 class="font-semibold text-lg text-gray-800">
                                    {{ $page.props.auth.user?.authenticated.name }}
                                </h2>
                                <p class="text-sm text-gray-600">
                                    <i class="bi bi-circle-fill text-green-500 text-xs mr-1"></i>
                                    Active now
                                </p>
                            </div>
                        </div>
                    </header>

                    <!-- Search section with improved accessibility -->
                    <div class="p-5 bg-gray-50">
                        <div class="relative">
                            <label for="search-conversations" class="sr-only">Search conversations</label>
                            <input
                                id="search-conversations"
                                type="text"
                                v-model="search"
                                class="w-full rounded-xl border-2 border-gray-200 p-3 pl-12 text-base focus:border-blue-500 focus:outline-none transition-colors"
                                placeholder="Search conversations..."
                                aria-label="Search conversations"
                            />
                            <i class="bi bi-search absolute left-4 top-[50%] translate-y-[-50%] text-gray-400 text-lg" aria-hidden="true"></i>
                        </div>
                        <h3 class="mt-4 mb-2 font-semibold text-gray-700 text-base">
                            <i class="bi bi-chat-dots mr-2"></i>Messages
                        </h3>
                    </div>

                    <!-- Conversations list with better touch targets -->
                    <div
                        ref="chathead-container"
                        class="relative flex-1 overflow-y-auto bg-white"
                        role="list"
                        aria-label="Conversation list"
                    >
                        <!-- Empty state -->
                        <div v-if="!chatHeads.length" class="flex flex-col items-center justify-center p-8">
                            <i class="bi bi-inbox text-6xl text-gray-300 mb-3"></i>
                            <p class="text-center text-gray-500 text-lg">No conversations yet</p>
                            <p class="text-center text-gray-400 text-sm mt-1">Start a new conversation to begin messaging</p>
                        </div>

                        <!-- Conversation items with better visual hierarchy -->
                        <button
                            v-for="(chatHead, index) in chatHeads"
                            @click="switchChat(chatHead.user.id)"
                            :key="chatHead.latestMessage?.id"
                            :class="[
                                'w-full flex gap-3 p-4 hover:bg-gray-50 transition-all duration-200 border-b border-gray-100',
                                {
                                    'bg-blue-50 border-l-4 border-l-blue-500 hover:bg-blue-100':
                                        chatHead.user.id === Number(route().params.user),
                                },
                            ]"
                            :ref="`chat-head-${chatHead.user.id}`"
                            role="listitem"
                            :aria-selected="chatHead.user.id === Number(route().params.user)"
                            :aria-label="`Conversation with ${chatHead.user.email}`"
                        >
                            <div class="h-14 w-14 flex-shrink-0">
                                <img
                                    :src="chatHead.user.profile_img || '/assets/profile_placeholder.jpg'"
                                    :alt="`${chatHead.user.email}'s profile`"
                                    class="h-full w-full rounded-full object-cover ring-2 ring-white shadow-sm"
                                />
                            </div>
                            <div class="flex-1 text-left min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="font-semibold text-gray-800 text-base truncate flex-1">
                                        {{ chatHead.user.email }}
                                    </p>
                                    <span v-if="chatHead.latestMessage?.created_at || chatHead.latestMessage?.timestamp" class="text-xs text-gray-500 ml-2">
                                        {{ formatChatHeadTime(chatHead.latestMessage?.created_at || chatHead.latestMessage?.timestamp) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1 truncate">
                                    {{ chatHead.latestMessage?.message || "No messages yet" }}
                                </p>
                            </div>
                        </button>
                    </div>
                </aside>

                <!-- Right side - Chat area -->
                <main class="flex flex-col bg-white" role="main">

                    <!-- Chat header with better user info display -->
                    <header class="flex items-center justify-between border-b border-gray-200 p-5 bg-gradient-to-r from-blue-50 to-purple-50">
                        <div v-if="route().params.user" class="flex items-center gap-3">
                            <div class="h-12 w-12 ring-2 ring-white shadow-sm rounded-full overflow-hidden">
                                <img
                                    :src="userDirectMessageProps?.profile_img || '/assets/profile_placeholder.jpg'"
                                    :alt="`${userDirectMessageProps?.name}'s profile`"
                                    class="h-full w-full object-cover"
                                />
                            </div>
                            <div>
                                <h1 class="font-semibold text-lg text-gray-800">
                                    {{ userDirectMessageProps?.name }}
                                </h1>
                                <p class="text-sm text-gray-600">
                                    {{ userDirectMessageProps?.email }}
                                </p>
                            </div>
                        </div>
                        <div v-else class="text-center w-full">
                            <h1 class="text-xl font-semibold text-gray-700">Select a conversation</h1>
                            <p class="text-gray-500 text-sm mt-1">Choose a conversation from the left to start messaging</p>
                        </div>

                        <!-- Options menu button -->
                        <button
                            v-if="route().params.user"
                            class="p-2 hover:bg-white/50 rounded-lg transition-colors"
                            aria-label="More options"
                        >
                            <i class="bi bi-three-dots-vertical text-xl text-gray-600"></i>
                        </button>
                    </header>

                    <!-- Messages container with improved readability -->
                    <div class="flex-1 flex flex-col bg-gradient-to-b from-gray-50 to-white">
                        <div
                            ref="chat-container"
                            class="chat-container overflow-y-auto p-6"
                            role="log"
                            aria-label="Message history"
                            aria-live="polite"
                        >
                            <!-- Empty state when no user selected -->
                            <div v-if="!route().params.user" class="flex flex-col items-center justify-center h-full">
                                <i class="bi bi-chat-text text-8xl text-gray-300 mb-4"></i>
                                <h2 class="text-2xl font-semibold text-gray-600 mb-2">Welcome to Messages</h2>
                                <p class="text-gray-500 text-lg">Select a conversation to start chatting</p>
                            </div>

                            <!-- Messages with time groups -->
                            <div v-for="(message, index) in messages" :key="message.id">
                                <!-- Date separator for new days -->
                                <div
                                    v-if="shouldShowDateSeparator(message, index)"
                                    class="flex items-center justify-center my-4"
                                >
                                    <div class="flex-1 border-t border-gray-200"></div>
                                    <span class="px-4 text-sm text-gray-500 bg-gray-50 rounded-full py-1">
                                        {{ formatDateSeparator(message.created_at || message.timestamp) }}
                                    </span>
                                    <div class="flex-1 border-t border-gray-200"></div>
                                </div>

                                <!-- Message with timestamp -->
                                <div :class="[
                                    'mb-4 flex',
                                    message.sender_id === $page.props.auth.user.authenticated.id
                                        ? 'justify-end'
                                        : 'justify-start'
                                ]">
                                    <div class="max-w-[70%] flex flex-col gap-1">
                                        <MessageBox
                                            :message="message"
                                        />
                                        <!-- Timestamp below message -->
                                        <span :class="[
                                            'text-xs text-gray-400 px-2',
                                            message.sender_id === $page.props.auth.user.authenticated.id
                                                ? 'text-right'
                                                : 'text-left'
                                        ]">
                                            {{ formatMessageTime(message.created_at || message.timestamp) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Message input area with better accessibility -->
                    <footer v-if="isShowMessageInput" class="p-5 bg-white border-t border-gray-200">
                        <form @submit.prevent="sendMessage" class="relative">
                            <label for="message-input" class="sr-only">Type your message</label>
                            <div class="flex gap-3">
                                <input
                                    id="message-input"
                                    ref="messageInput"
                                    type="text"
                                    placeholder="Type your message..."
                                    class="flex-1 rounded-xl bg-gray-100 px-5 py-4 text-base outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all"
                                    aria-label="Message input"
                                />
                                <button
                                    type="submit"
                                    class="px-6 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-colors flex items-center gap-2 font-medium shadow-sm hover:shadow-md"
                                    aria-label="Send message"
                                >
                                    <span class="hidden sm:inline">Send</span>
                                    <i class="bi bi-send-fill text-lg"></i>
                                </button>
                            </div>
                        </form>
                    </footer>
                </main>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Improved scrollbar styling for better visibility */
.chat-container {
    flex: 1 1 400px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

.chat-container::-webkit-scrollbar {
    width: 8px;
}

.chat-container::-webkit-scrollbar-track {
    background: #f7fafc;
    border-radius: 4px;
}

.chat-container::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 4px;
}

.chat-container::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

/* Improve focus visibility for keyboard navigation */
button:focus-visible,
input:focus-visible {
    outline: 3px solid #3b82f6;
    outline-offset: 2px;
}

/* Larger touch targets for mobile */
@media (max-width: 768px) {
    button {
        min-height: 48px;
        min-width: 48px;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .border-gray-200 {
        border-color: #4a5568;
    }

    .text-gray-600 {
        color: #2d3748;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
