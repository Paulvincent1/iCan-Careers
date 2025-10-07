@component('mail::message')
{{-- Header Logo --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
<img src="{{ config('app.url') }}/assets/SHORTS.svg" alt="{{ config('app.name') }} Logo" style="height:50px;">
@endcomponent
@endslot

# 👋 Welcome to {{ config('app.name') }}

Hi {{ $emailVerification->name ?? 'there' }},

We're excited to have you join **iCan Careers** 🎉
> *Helping senior citizens and PWDs find meaningful work.*

Please use the verification code below to confirm your email:

@component('mail::panel')
<div style="text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 8px; color: #2d3748;">
    {{ $emailVerification->verification_code }}
</div>
@endcomponent

Or simply click the button below to automatically verify your email:

@component('mail::button', ['url' => url("/email/verify/{$emailVerification->verification_code}"), 'color' => 'success'])
Confirm My Account
@endcomponent

This link will expire in 24 hours for security reasons.

If you didn't create an account, no further action is required.

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
*Empowering opportunities for senior citizens & PWDs.*
@endcomponent
@endslot
@endcomponent
