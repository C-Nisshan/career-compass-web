<!DOCTYPE html>
<html>
<head>
    <title>{{ __('Registration Status') }}</title>
</head>
<body>
    <h1>{{ __('Hello, :name', ['name' => $user->profile->first_name ?? $user->email]) }}</h1>
    <p>
        @if($status === 'pending')
            {{ __('Your registration has been submitted and is awaiting admin approval.') }}
        @elseif($status === 'approved')
            {{ __('Your registration has been approved. You can now log in.') }}
        @elseif($status === 'rejected')
            {{ __('Your registration was rejected. Reason: :reason', ['reason' => $user->verificationDocuments->first()->verificationWorkflow->rejection_reason ?? 'N/A']) }}
            {{ __('Please resubmit your documents.') }}
        @endif
    </p>
    <p>{{ __('Thank you for using our platform!') }}</p>
</body>
</html>