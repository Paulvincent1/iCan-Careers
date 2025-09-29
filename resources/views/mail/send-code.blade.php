<x-mail::message>
# Email Verification

Your verification code is:

<x-mail::panel>
{{ $emailVerification->verification_code }}
</x-mail::panel>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
