@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
<img src="{{ rtrim(config('app.url'), '/') }}/brand/logo-email.png" class="logo" alt="Logo {{ config('firm.name') }}" width="88" height="88">
<span style="display: block; margin-top: 8px; color: #0f1e3a; font-size: 16px; font-weight: 700; letter-spacing: .04em;">{{ config('firm.name') }}</span>
</a>
</td>
</tr>
