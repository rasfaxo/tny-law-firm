@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
@if (is_file(public_path('brand/logo-email.png')))
<img src="cid:tny-logo@tnypartners.com" class="logo" alt="Logo {{ config('firm.name') }}" width="88" height="88">
@endif
<span style="display: block; margin-top: 8px; color: #0f1e3a; font-size: 16px; font-weight: 700; letter-spacing: .04em;">{{ config('firm.name') }}</span>
</a>
</td>
</tr>
