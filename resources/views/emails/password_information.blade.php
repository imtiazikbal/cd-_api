@component('mail::message')
    # Welcome to Our Website

    Hello {{ $user['name'] }},

    Thank you for registering on our website. Your account has been created, and here is your login information:

    **Email:** {{ $user['email'] }}
    **Password:** {{ $password }}

    Please keep your password secure and do not share it with anyone.

    If you have any questions or need assistance, feel free to contact us.

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
