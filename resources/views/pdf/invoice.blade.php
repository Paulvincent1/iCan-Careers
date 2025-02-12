<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">

    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md">
        <!-- Invoice Header -->
        <div class=" px-8 pt-8">


            <div class="flex justify-between items-center border-b pb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-700">Invoice</h1>
                    <p class="text-gray-500">Invoice #: {{ $invoiceId }}</p>
                    <p class="text-gray-500">Date: {{ $dueDate }}</p>
                </div>
            </div>

            <!-- Billing Details -->
            <div class="mt-6">
                <h3 class="font-semibold text-gray-700">Bill To:</h3>
                <p class="text-gray-600">{{ $employer->employerProfile->full_name }}</p>
                <p class="text-gray-500">{{ $employer->employerProfile->phone_number }}</p>
                <p class="text-gray-500">{{ $employer->email }}</p>
            </div>

            <!-- Invoice Items -->
            <div class="mt-6">
                <table class="w-full border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border p-3 text-left">Description</th>
                            <th class="border p-3 text-right">Hours</th>
                            <th class="border p-3 text-right">Rate</th>
                            <th class="border p-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                        <tr>
                            <td class="border p-3">{{ $item['id'] }}</td>
                            <td class="border p-3 text-right">{{ $item['hours'] }}</td>
                            <td class="border p-3 text-right">₱ {{ number_format($item['rate'] , 2) }}</td>
                            <td class="border p-3 text-right">₱ {{ number_format($item['amount'] , 2) }}</td>
                        </tr>
                        @endforeach
                        <tr class="font-bold">
                            <td colspan="3" class="border p-3 text-right">Total Amount</td>
                            <td class="border p-3 text-right text-green-600">₱ {{ number_format($totalAmount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            
        <!-- Footer -->
        <div class="mt-6 text-gray-600 text-sm text-center">
            <p>Thank you for using iCan Careers!</p>
        </div>
        </div>
        <!-- Payment Button -->
        <div class="mt-6 text-center ">
            <a href="{{ $paymentUrl ? $paymentUrl : '#' }} target="_blank" class="w-full inline-block bg-[#1e2e50] text-white px-6 py-3 rounded-b-lg shadow-md hover:bg-blue-700 transition">
                 PAY NOW
            </a>
        </div>

    </div>

</body>
</html>
