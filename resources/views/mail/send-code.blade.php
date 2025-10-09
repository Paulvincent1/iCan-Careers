<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verification Code</title>
</head>
<body style="font-family: Arial, sans-serif; color: #2d3748; line-height: 1.6;">
    <h1>👋 Welcome to {{ config('app.name') }}</h1>

    <p>Hi {{ $emailVerification->name ?? 'there' }},</p>

    <p>We're excited to have you join <strong>iCan Careers</strong> 🎉</p>
    <p><em>Helping senior citizens and PWDs find meaningful work.</em></p>

    <p>Please use the verification code below to confirm your email:</p>

    <div style="text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 8px; margin: 20px 0;">
        {{ $emailVerification->verification_code }}
    </div>

    <p>This link will expire in 24 hours for security reasons.</p>

    <p>If you didn't create an account, no further action is required.</p>

    <hr>

    <p style="font-size: 12px; color: #6b7280;">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
        Empowering opportunities for senior citizens & PWDs.
    </p>
</body>
</html>
