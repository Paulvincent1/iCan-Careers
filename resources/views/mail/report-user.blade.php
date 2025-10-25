<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>User Report Notification</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9fafb; padding: 30px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; padding: 25px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <h2 style="color: #333;">🚨 User Report Alert</h2>
        <p>Hello {{ $admin->name }},</p>
        <p><strong>{{ $user->name }}</strong> has reported a user.</p>

        <h4>Reported User:</h4>
        <p><strong>{{ $reported->name }}</strong></p>

        <h4>Reason:</h4>
        <blockquote style="background: #f1f5f9; border-left: 4px solid #3b82f6; padding: 10px 15px; border-radius: 4px;">
            {{ $reason }}
        </blockquote>

        <p style="margin-top: 20px;">Please review the report in your admin dashboard.</p>

        <p style="margin-top: 30px; font-size: 13px; color: #777;">Thank you,<br><strong>iCan Careers Team</strong></p>
    </div>
</body>
</html>
