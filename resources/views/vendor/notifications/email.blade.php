@php
    $url = $actionUrl ?? ($url ?? null);
    $actionLabel = $actionText ?? __('Verify Email Address');
    $title = ! empty($greeting)
        ? $greeting
        : ($level === 'error' ? __('Whoops!') : __('Hello!'));
    $description = $introLines[0] ?? __('Please click the button below to verify your email address.');
    $closingLine = $outroLines[0] ?? __('If you did not create an account, no further action is required.');
    $fallbackUrl = $displayableActionUrl ?? $url;
@endphp

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; margin:0; padding:0; background-color:#eef2f7;">
    <tr>
        <td align="center" style="padding:24px 12px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; max-width:1120px; margin:0 auto; background-color:#edf4fd; background-image:linear-gradient(180deg, #f3f7fc 0%, #edf4fd 100%);">
                <tr>
                    <td align="center" style="padding:18px 20px 12px 20px;">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto;">
                            <tr>
                                <td valign="middle" style="padding-right:12px;">
                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td align="center" valign="middle" width="52" height="52" style="width:52px; height:52px; background-color:#163f9c; border-radius:16px; color:#ffffff; font-family:Arial, Helvetica, sans-serif; font-size:28px; line-height:52px; text-align:center; font-weight:700;">
                                                &#9878;
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td valign="middle" align="left">
                                    <div style="font-family:Arial, Helvetica, sans-serif; font-size:24px; line-height:1.1; font-weight:700; color:#1d3557; margin:0;">
                                        GABAY-Lex
                                    </div>
                                    <div style="font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:1.5; color:#64748b; margin:4px 0 0 0;">
                                        Your Legal Guide
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:12px auto 0 auto;">
                            <tr>
                                <td width="52" style="width:52px; height:3px; background-color:#3b82f6; border-radius:999px; font-size:0; line-height:0;">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td align="center" style="padding:0 20px 18px 20px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; max-width:730px; margin:0 auto; background-color:#ffffff; border-radius:18px; box-shadow:0 16px 46px rgba(15, 23, 42, 0.08);">
                            <tr>
                                <td align="center" style="padding:18px 20px 0 20px;">
                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto;">
                                        <tr>
                                            <td align="center" valign="middle" width="62" height="62" style="width:62px; height:62px; background-color:#edf4ff; border-radius:31px; color:#5b8def; font-family:Arial, Helvetica, sans-serif; font-size:28px; line-height:62px; text-align:center;">
                                                &#9993;
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td align="center" style="padding:10px 44px 0 44px;">
                                    <div style="font-family:Arial, Helvetica, sans-serif; font-size:26px; line-height:1.2; font-weight:700; color:#1f2f46; margin:0;">
                                        {{ $title }}
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td align="center" style="padding:8px 44px 0 44px;">
                                    <div style="font-family:Arial, Helvetica, sans-serif; font-size:16px; line-height:1.7; color:#556476; margin:0;">
                                        {{ $description }}
                                    </div>
                                </td>
                            </tr>

                            @if (! empty($url))
                                <tr>
                                    <td align="center" style="padding:18px 44px 0 44px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto;">
                                            <tr>
                                                <td align="center" style="border-radius:10px; background-color:#163f9c; background-image:linear-gradient(135deg, #214cbe 0%, #163f9c 100%); box-shadow:0 10px 24px rgba(22, 63, 156, 0.22);">
                                                    <a href="{{ $url }}" target="_blank" rel="noopener" style="display:inline-block; font-family:Arial, Helvetica, sans-serif; font-size:16px; line-height:20px; font-weight:700; color:#ffffff; text-decoration:none; padding:14px 28px;">
                                                        <span style="display:inline-block; width:24px; height:24px; line-height:24px; text-align:center; border-radius:12px; background-color:rgba(255,255,255,0.18); color:#ffffff; font-size:14px; font-weight:700; margin-right:10px;">&#10003;</span>{{ $actionLabel }}
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <td style="padding:26px 44px 0 44px;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td style="border-top:1px solid #e5eaf3; font-size:0; line-height:0;">&nbsp;</td>
                                            <td align="center" valign="middle" style="padding:0 10px; width:32px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:12px; color:#94a3b8; font-weight:700; text-transform:uppercase;">OR</td>
                                            <td style="border-top:1px solid #e5eaf3; font-size:0; line-height:0;">&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td align="center" style="padding:18px 44px 0 44px;">
                                    <div style="font-family:Arial, Helvetica, sans-serif; font-size:16px; line-height:1.7; color:#556476; margin:0;">
                                        {{ $closingLine }}
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:18px 44px 0 44px;">
                                    <div style="font-family:Arial, Helvetica, sans-serif; font-size:16px; line-height:1.7; color:#556476; margin:0;">
                                        Regards,
                                    </div>
                                    <div style="font-family:Arial, Helvetica, sans-serif; font-size:16px; line-height:1.5; color:#1f2f46; font-weight:700; margin:0;">
                                        GABAY-Lex
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:28px 34px 20px 34px;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f2f6fc; border-radius:14px;">
                                        <tr>
                                            <td valign="middle" width="78" style="width:78px; padding:18px 12px 18px 22px;">
                                                <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                                    <tr>
                                                        <td align="center" valign="middle" width="44" height="44" style="width:44px; height:44px; border-radius:22px; background-color:#ffffff; color:#2f69eb; font-family:Arial, Helvetica, sans-serif; font-size:22px; line-height:44px; text-align:center; box-shadow:0 6px 14px rgba(47, 105, 235, 0.08);">
                                                            &#128274;
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td valign="top" style="padding:18px 22px 18px 8px;">
                                                <div style="font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:1.55; color:#5b6470; margin:0 0 8px 0;">
                                                    If you're having trouble clicking the "{{ $actionLabel }}" button, copy and paste the URL below into your web browser:
                                                </div>
                                                @if (! empty($fallbackUrl))
                                                    <div style="font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:1.65; color:#1d5fe3; word-break:break-all; margin:0;">
                                                        {{ $fallbackUrl }}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td align="center" style="padding:0 20px 18px 20px;">
                        <div style="font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:1.6; color:#7d8da2; margin:0;">
                            &copy; 2026 GABAY-Lex. All rights reserved.
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
