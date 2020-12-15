@component('mail::message')
    ## Hello,
    #{{$content['name']}}

    @component('mail::table')
        | Username | Password |
        | ------------- |:-------------:|
        | {{$content['username']}} | {{$content['password']}} |
    @endcomponent

    If you did not request your password, no further action is required.

    Thanks, <br>
    {{ config('app.name') }}
@endcomponent