<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Invoice Notification</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9fafb; padding: 30px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; padding: 25px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <h2 style="color: #333;">🧾 New Invoice from {{ $worker->name }}</h2>

        <p>Hello {{ $employer->name }},</p>

        <p>You have received a new invoice from <strong>{{ $worker->name }}</strong>.</p>

        <h4>Description:</h4>
        <p>{{ $description }}</p>

        <h4>Items:</h4>
        <ul>
            @foreach($items as $item)
                <li>{{ $item['description'] }} - {{ $item['hours'] }} hrs × ₱{{ $item['rate'] }} = ₱{{ $item['hours'] * $item['rate'] }}</li>
            @endforeach
        </ul>

        <p><strong>Total Amount:</strong> ₱{{ number_format($totalAmount, 2) }}</p>
        <p><strong>Due Date:</strong> {{ $dueDate }}</p>

        <p>
            You can view the invoice here:<br>
            <a href="{{ $invoiceUrl }}" style="color: #2563eb;">View Invoice</a>
        </p>

        <p style="margin-top: 30px; font-size: 13px; color: #777;">Thank you,<br><strong>iCan Careers Team</strong></p>
    </div>
</body>
</html>
