<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Interview Scheduled</title>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-xl mx-auto bg-white shadow-md rounded-md p-6">
        <h1 class="text-2xl font-bold mb-4">Interview Scheduled</h1>
        <p>Hi {{ $worker->name ?? 'there' }},</p>
        <p>Your interview for the <strong>{{ $jobPost->job_title }}</strong> position has been scheduled.</p>
        <p>Please wait for further instructions from the employer.</p>
    </div>
</body>
</html>
