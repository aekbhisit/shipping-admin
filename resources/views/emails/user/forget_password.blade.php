@component('mail::message')
# Introduction

The body of your message.

{{-- Action Button --}}
@isset($actionText)
<?php
    switch ($level) {
        case 'success':
        case 'error':
            $color = $level;
            break;
        default:
            $color = 'primary';
    }
?>

@component('mail::panel')
This is the panel content.
@endcomponent

@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ $actionText }}
@endcomponent
@endisset

Thanks,<br>
{{ config('app.name') }}
@endcomponent
 