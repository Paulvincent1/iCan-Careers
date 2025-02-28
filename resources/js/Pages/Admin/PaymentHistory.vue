<script setup>
import { getCurrentInstance, ref } from "vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faSearch, faSort, faCalendar } from "@fortawesome/free-solid-svg-icons";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import dayjs from "dayjs";

defineOptions({
    layout: AdminLayout,
});

let props = defineProps({
    subscriptionPaymentHistoryProps: null
})

// Add icons to FontAwesome library
library.add(faSearch, faSort, faCalendar);

// Dummy Data for Payment History
const paymentHistory = ref([
    { id: 1, user: "John Doe", amount: "$50", date: "2025-02-15", status: "Completed" },
    { id: 2, user: "Jane Smith", amount: "$30", date: "2025-02-14", status: "Pending" },
    { id: 3, user: "Mike Johnson", amount: "$25", date: "2025-02-13", status: "Completed" },
]);

const formatCurrency = getCurrentInstance().appContext.config.globalProperties.formatCurrency;
</script>

<template>
    <Head title="PaymentHistory | iCan Careers" />
    <div class="bg-white p-5 rounded-lg shadow">
        <h2 class="text-xl font-bold mb-4">💰 Payment History</h2>

        <!-- Search Input -->
        <div class="mb-4 flex items-center space-x-2 border p-2 rounded-lg">
            <font-awesome-icon :icon="['fas', 'search']" class="text-gray-500" />
            <input type="text" placeholder="Search..." class="w-full p-2 outline-none">
        </div>

        <!-- Desktop Table -->
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full border-collapse border rounded-lg">
                <thead>
                    <tr class="bg-gray-200 text-left text-sm">
                        <th class="p-3">#</th>
                        <th class="p-3">User</th>
                        <th class="p-3">
                            Amount <font-awesome-icon :icon="['fas', 'sort']" />
                        </th>
                        <th class="p-3">
                            Subscription Type <font-awesome-icon :icon="['fas', 'sort']" />
                        </th>
                        <th class="p-3">
                            Date <font-awesome-icon :icon="['fas', 'calendar']" />
                        </th>
                        
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="payment in subscriptionPaymentHistoryProps" :key="payment.id" class="border-t text-sm">
                        <td class="p-3">{{ payment.id }}</td>
                        <td class="p-3">{{ payment.employer.email }}</td>
                        <td class="p-3">{{ formatCurrency(payment.amount) }}</td>
                        <td class="p-3">{{ payment.subscription_type }}</td>
                        <td class="p-3">{{ dayjs(payment.created_at).format('MMMM DD, YYYY')}}</td>
                        
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mobile Card Layout -->
        <div class="md:hidden space-y-4">
            <div v-for="payment in paymentHistory" :key="payment.id" class="bg-gray-100 p-4 rounded-lg shadow">
                <p class="text-sm font-semibold">
                    <span class="text-gray-500">User:</span> {{ payment.user }}
                </p>
                <p class="text-sm">
                    <span class="text-gray-500">Amount:</span> {{ payment.amount }}
                </p>
                <p class="text-sm">
                    <span class="text-gray-500">Date:</span> {{ payment.date }}
                </p>
                <p class="text-sm font-medium"
                    :class="payment.status === 'Completed' ? 'text-green-500' : 'text-yellow-500'">
                    {{ payment.status }}
                </p>
            </div>
        </div>
    </div>
</template>
