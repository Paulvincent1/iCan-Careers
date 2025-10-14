<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-xl mx-auto bg-white shadow-md rounded-md p-6">
        <h1 class="text-2xl font-bold mb-4">Reset Your Password</h1>
        <p class="mb-4">Hi {{ $user->name ?? 'there' }},</p>
        <p class="mb-6">Click the button below to reset your password:</p>
        <a href="{{ url("/reset-password/{$token}?email={$user->email}") }}"
           class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
            Reset Password
        </a>
        <p class="mt-4 text-sm text-gray-500">This link will expire in 60 minutes.</p>
    </div>
</body>
</html>
