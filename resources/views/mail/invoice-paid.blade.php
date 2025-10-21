<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice Paid</title>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-xl mx-auto bg-white shadow-md rounded-md p-6">
        <h1 class="text-2xl font-bold mb-4">Invoice Paid</h1>
        <p>Hi {{ $worker->name ?? 'there' }},</p>
        <p>Your invoice from <strong>{{ $employer->name }}</strong> has been marked as <strong>Paid</strong>.</p>
        <p>Invoice Details:</p>
        <ul class="list-disc list-inside mb-4">
            <li>Invoice ID: {{ $invoice->id }}</li>
            <li>Amount: ${{ number_format($invoice->amount, 2) }}</li>
            <li>Date Paid: {{ $invoice->updated_at->format('M d, Y') }}</li>
        </ul>
        <p>Thank you for using iCan Careers!</p>
    </div>
</body>
</html>
