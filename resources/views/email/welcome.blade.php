@component('mail::message')
## Hello,
#{{$content['name']}}
IQ Machines welcomes you to {{ config('app.property_name') }}.  Your Username and Password for the WiFi system are listed below.
@component('mail::table')
    | Username | Password |
    | ------------- |:-------------:|
    | {{$content['username']}} | {{$content['password']}} |
@endcomponent

Some devices such as TVs, game consoles, streaming devices, printers, and digital assistants will require you to
add the device’s unique wireless Media Access Control (MAC) address to the network before the device will connect
to the {{ config('app.property_name') }} network.<br>
<br>
Using our website below, you will be able to add your own devices to {{ config('app.property_name') }} network.
Please note this process is only required for devices which will not connect to the WiFi using your regular username
and password.<br>
<br>
Visit <a target="_blank" href="{{ config('app.url') }}">{{ config('app.url') }}</a> and sign in with your username and password.  After inputting the wireless MAC address of
your device to the website please connect that device to the WiFi titled {{ config('app.property_name') }} PSK and use the
word {{ config('app.psk_password') }} as the password.<br>
<br>
If you have and trouble please reach out to us at support@iqmachines.com and we would be happy to assist you.<br>
<br>
Thanks <br>
IQ Machines Support
@endcomponent