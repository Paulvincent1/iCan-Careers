<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Job Application Received</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9fafb;
            color: #333;
            padding: 30px;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 25px 30px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        h1 {
            color: #2563eb;
            font-size: 22px;
            margin-bottom: 15px;
        }
        p {
            line-height: 1.6;
        }
        .highlight {
            color: #111;
            font-weight: bold;
        }
        .footer {
            font-size: 13px;
            color: #777;
            margin-top: 30px;
            text-align: center;
        }
        .btn {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 6px;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Hello, {{ $employer->name }}!</h1>

        <p>
            A new applicant has applied to your job post:
            <strong>{{ $jobPost->job_title }}</strong>.
        </p>

        <p>
            <strong>Applicant Name:</strong> {{ $applicant->name }}<br>
            <strong>Email:</strong> {{ $applicant->email }}
        </p>

        <a href="{{ url('/employer/job-posts/' . $jobPost->id) }}" class="btn">View Job Post</a>

        <p class="footer">
            — The iCan Careers Team<br>
            If you have any questions, contact us at
            <a href="mailto:icancareers2@gmail.com">icancareers2@gmail.com</a>.
        </p>
    </div>
</body>
</html>
