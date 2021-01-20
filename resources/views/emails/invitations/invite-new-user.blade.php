@component('mail::message')
# Hi
You have been invited to join the team
**{{$invitation->team->name}}**.
[Register for free]({{$url}}) and you can accept or reject the invitation in your team management console

@component('mail::button', ['url' => $url])
Register for free
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
