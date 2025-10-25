<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contract Ended Notification</title>
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
            color: #dc2626;
            font-size: 22px;
            margin-bottom: 15px;
        }
        p {
            line-height: 1.6;
        }
        .footer {
            font-size: 13px;
            color: #777;
            margin-top: 30px;
            text-align: center;
        }
        .highlight {
            color: #111;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Hello, {{ $worker->name }}!</h1>

        <p>
            We appreciate the effort and dedication you’ve shown while working under
            <span class="highlight">{{ $employer->name }}</span>.
        </p>

        <p>
            However, this email is to inform you that your contract for the job post
            <strong>{{ $jobPost->job_title }}</strong> has now ended.
        </p>

        <p>
            We truly wish you all the best in your future opportunities and thank you for being part of iCan Careers.
        </p>

        <div class="footer">
            <p>— The iCan Careers Team</p>
            <p>If you have any questions, contact us at
                <a href="mailto:icancareers2@gmail.com">icancareers2@gmail.com</a>.
            </p>
        </div>
    </div>
</body>
</html>
