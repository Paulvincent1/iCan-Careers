<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Job Post Has Been Approved</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9fafb;
            padding: 30px;
            color: #333;
        }
        .container {
            background: #ffffff;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            max-width: 600px;
            margin: auto;
        }
        h1 {
            color: #2563eb;
            font-size: 22px;
            margin-bottom: 15px;
        }
        p {
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            margin-top: 15px;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 6px;
        }
        .btn:hover {
            background-color: #1e40af;
        }
        .footer {
            margin-top: 25px;
            font-size: 13px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Hello, {{ $employer->name }}!</h1>

        <p>Good news 🎉 Your job post has been <strong>approved by the admin</strong> and is now <strong>open</strong> for applicants.</p>

        <p><strong>Job Title:</strong> {{ $jobPost->job_title }}</p>
        <p><strong>Status:</strong> {{ ucfirst($jobPost->job_status) }}</p>

        <a href="{{ url('/jobs/' . $jobPost->id) }}" class="btn">View Job Post</a>

        <div class="footer">
            <p>Thank you for using <strong>iCan Careers</strong>.</p>
            <p>If you have any questions, contact us at <a href="mailto:icancareers2@gmail.com">icancareers2@gmail.com</a>.</p>
        </div>
    </div>
</body>
</html>
