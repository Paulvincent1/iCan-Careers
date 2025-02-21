<script setup>
import { ref } from "vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faSearch, faSort, faCalendar } from "@fortawesome/free-solid-svg-icons";
import AdminLayout from "../Layouts/Admin/AdminLayout.vue";
import DashboardChart from "../Components/Admin/DashboardChart.vue";

defineOptions({
    layout: AdminLayout,
});

// Add icons to FontAwesome library
library.add(faSearch, faSort, faCalendar);

// Dummy Data for Payment History
const paymentHistory = ref([
    { id: 1, user: "John Doe", amount: "$50", date: "2025-02-15", status: "Completed" },
    { id: 2, user: "Jane Smith", amount: "$30", date: "2025-02-14", status: "Pending" },
    { id: 3, user: "Mike Johnson", amount: "$25", date: "2025-02-13", status: "Completed" },
]);
</script>

<template>
    <div class="bg-white p-5 rounded-lg shadow">
        <h2 class="text-xl font-bold mb-4">💰 Payment History</h2>
        <div class="mb-4 flex items-center space-x-2">
            <font-awesome-icon :icon="['fas', 'search']" class="text-gray-500" />
            <input type="text" placeholder="Search..." class="p-2 border rounded w-full">
        </div>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border rounded-lg">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="p-3">#</th>
                        <th class="p-3">User</th>
                        <th class="p-3">Amount <font-awesome-icon :icon="['fas', 'sort']" /></th>
                        <th class="p-3">Date <font-awesome-icon :icon="['fas', 'calendar']" /></th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="payment in paymentHistory" :key="payment.id" class="border-t">
                        <td class="p-3">{{ payment.id }}</td>
                        <td class="p-3">{{ payment.user }}</td>
                        <td class="p-3">{{ payment.amount }}</td>
                        <td class="p-3">{{ payment.date }}</td>
                        <td class="p-3">
                            <span :class="payment.status === 'Completed' ? 'text-green-500' : 'text-yellow-500'">
                                {{ payment.status }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
