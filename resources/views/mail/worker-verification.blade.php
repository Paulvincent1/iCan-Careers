<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $verified ? 'Congratulations!' : 'Verification Required' }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; margin:0; padding:0;">
    <div style="max-width: 600px; margin: 30px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <div style="background-color: {{ $verified ? '#22c55e' : '#ef4444' }}; color: white; text-align: center; padding: 20px;">
            <h1 style="margin: 0;">{{ $verified ? 'Congratulations!' : 'Verification Required' }}</h1>
        </div>
        <div style="padding: 24px; text-align: center;">
            <img src="{{ asset($verified ? 'assets/congratulations.jpg' : 'assets/denied.jpg') }}" alt="Notification Image" style="max-width: 150px; margin-bottom: 16px;">
            <p style="margin-bottom: 16px; font-size: 16px;">
                {{ $verified
                    ? "Your account is now fully verified! You can now apply for jobs."
                    : "Unfortunately, your verification was not approved. Please check your submitted documents and try again." }}
            </p>
            @if(!$verified)
                <a href="{{ url('/worker/verification') }}" style="display:inline-block; padding:10px 20px; background-color:#dc2626; color:white; text-decoration:none; border-radius:6px; font-weight:500;">Review Documents</a>
            @endif
        </div>
        <div style="padding:16px; text-align:center; font-size:12px; color:#6b7280;">
            &copy; {{ date('Y') }} iCan Careers. All rights reserved.
        </div>
    </div>
</body>
</html>
