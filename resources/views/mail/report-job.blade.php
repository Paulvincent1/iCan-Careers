<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Job Post Report Notification</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9fafb; padding: 30px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; padding: 25px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <h2 style="color: #333;">🚨 Job Post Report Alert</h2>

        <p>Hello {{ $admin->name }},</p>

        <p><strong>{{ $user->name }}</strong> has reported a job post titled:</p>
        <h4 style="color: #1d4ed8;">{{ $jobPost->job_title }}</h4>

        <h4>Reason for Report:</h4>
        <blockquote style="background: #f1f5f9; border-left: 4px solid #3b82f6; padding: 10px 15px; border-radius: 4px;">
            {{ $reason }}
        </blockquote>

        <p style="margin-top: 15px;">Please review this report in your admin dashboard to take necessary actions.</p>

        <p style="margin-top: 30px; font-size: 13px; color: #777;">Thank you,<br><strong>iCan Careers Team</strong></p>
    </div>
</body>
</html>
