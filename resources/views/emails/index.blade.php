@php
    $trace = json_decode($data['trace'] ?? '[]', true);
    if (! is_array($trace)) {
        $trace = [];
    }

    $statusCode = $data['status_code'] ?? 500;
    $isAuthenticated = (bool) ($data['is_authenticated'] ?? false);
    $appName = config('app.name') ?: ucfirst((string) config('notifier.name'));
@endphp
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="x-apple-disable-message-reformatting" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    {{-- Tells dark-mode-aware clients (Outlook.com, Windows Mail, some Gmail
         contexts) that this email already handles both schemes itself, so
         they use the prefers-color-scheme rule below as authored instead of
         auto-inverting the light design with their own heuristic. --}}
    <meta name="color-scheme" content="light dark" />
    <meta name="supported-color-schemes" content="light dark" />
    <title>{{ $appName }} — Error Report</title>
    <style type="text/css" rel="stylesheet" media="all">
        body {
            width: 100% !important;
            height: 100%;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            background-color: #f1f2f5;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        td { word-break: break-word; }

        .preheader {
            display: none !important;
            visibility: hidden;
            mso-hide: all;
            font-size: 1px;
            line-height: 1px;
            max-height: 0;
            max-width: 0;
            opacity: 0;
            overflow: hidden;
        }

        .email-wrapper { width: 100%; background-color: #f1f2f5; }

        .email-content {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }

        .accent-bar { height: 4px; line-height: 4px; font-size: 0; background-color: #e11d48; }

        .header-cell {
            padding: 24px 32px 16px 32px;
            background-color: #ffffff;
        }

        .app-name {
            font-size: 14px;
            font-weight: 700;
            color: #6b7280;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            background-color: #fef2f2;
            color: #e11d48;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.03em;
        }

        .body-cell {
            padding: 8px 32px 32px 32px;
            background-color: #ffffff;
        }

        .eyebrow {
            margin: 0 0 8px 0;
            font-size: 12px;
            font-weight: 700;
            color: #e11d48;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .exception-message {
            margin: 0 0 24px 0;
            font-size: 20px;
            line-height: 1.4;
            font-weight: 700;
            color: #111827;
        }

        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }

        .meta-cell {
            width: 50%;
            padding: 12px 16px;
            background-color: #f9fafb;
            border: 1px solid #eef0f3;
            vertical-align: top;
        }

        .meta-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #9ca3af;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .meta-value {
            display: block;
            font-size: 14px;
            color: #1f2937;
        }

        .meta-value.mono {
            font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, monospace;
            font-size: 13px;
        }

        .meta-full {
            width: 100%;
            padding: 12px 16px;
            background-color: #f9fafb;
            border: 1px solid #eef0f3;
        }

        .section-title {
            margin: 28px 0 12px 0;
            font-size: 12px;
            font-weight: 700;
            color: #6b7280;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .trace-block {
            background-color: #111827;
            border-radius: 6px;
            padding: 16px 18px;
        }

        .trace-frame { padding: 6px 0; }
        .trace-frame + .trace-frame { border-top: 1px solid #1f2937; }

        .trace-call {
            font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, monospace;
            font-size: 13px;
            color: #f9fafb;
        }

        .trace-location {
            font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, monospace;
            font-size: 12px;
            color: #9ca3af;
            margin-top: 2px;
        }

        .footer-cell {
            padding: 24px 32px 32px 32px;
            text-align: center;
        }

        .footer-text {
            margin: 0;
            font-size: 12px;
            line-height: 1.6;
            color: #9ca3af;
        }

        @media only screen and (max-width: 600px) {
            .email-content { width: 100% !important; }
            .header-cell, .body-cell, .footer-cell { padding-left: 20px !important; padding-right: 20px !important; }
            .meta-table, .meta-table tbody, .meta-table tr, .meta-cell {
                display: block !important;
                width: 100% !important;
            }
            .meta-cell { border-top: none !important; }
        }

        @media (prefers-color-scheme: dark) {
            body, .email-wrapper { background-color: #0b0d12 !important; }
            .header-cell, .body-cell { background-color: #15181e !important; }
            .app-name { color: #9ca3af !important; }
            .exception-message { color: #f9fafb !important; }
            .meta-cell, .meta-full { background-color: #1c2027 !important; border-color: #262b33 !important; }
            .meta-value { color: #e5e7eb !important; }
            .footer-text { color: #6b7280 !important; }
        }
    </style>
    <!--[if mso]>
    <style type="text/css">
        body, .app-name, .exception-message, .meta-value, .footer-text { font-family: Arial, sans-serif !important; }
    </style>
    <![endif]-->
</head>
<body>
    <span class="preheader">{{ $data['message'] ?? 'A new error was reported' }}</span>
    <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="email-content" cellpadding="0" cellspacing="0" role="presentation">
                    <tr><td class="accent-bar">&nbsp;</td></tr>
                    <tr>
                        <td class="header-cell">
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td class="app-name">{{ $appName }}</td>
                                    <td align="right"><span class="status-badge">{{ $statusCode }} ERROR</span></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="body-cell">
                            <p class="eyebrow">Unhandled exception reported</p>
                            <p class="exception-message">{{ $data['message'] ?? 'No message provided.' }}</p>

                            <table class="meta-table" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td class="meta-cell">
                                        <span class="meta-label">File</span>
                                        <span class="meta-value mono">{{ $data['file'] ?? 'N/A' }}</span>
                                    </td>
                                    <td class="meta-cell">
                                        <span class="meta-label">Line</span>
                                        <span class="meta-value mono">{{ $data['line'] ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="meta-cell">
                                        <span class="meta-label">Reported at</span>
                                        <span class="meta-value">{{ now()->toDayDateTimeString() }}</span>
                                    </td>
                                    <td class="meta-cell">
                                        <span class="meta-label">Authenticated</span>
                                        <span class="meta-value">{{ $isAuthenticated ? 'Yes' : 'No' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="meta-cell">
                                        <span class="meta-label">User ID</span>
                                        <span class="meta-value">{{ $data['id'] ?? 'N/A' }}</span>
                                    </td>
                                    <td class="meta-cell">
                                        <span class="meta-label">User email</span>
                                        <span class="meta-value">{{ $data['email'] ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-top: 12px;">
                                <tr>
                                    <td class="meta-full">
                                        <span class="meta-label">URL</span>
                                        <span class="meta-value mono">{{ $data['access_url'] ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                            </table>

                            @if(count($trace))
                                <p class="section-title">Stack trace</p>
                                <div class="trace-block">
                                    @foreach($trace as $index => $frame)
                                        @php
                                            $frameFile = $frame['file'] ?? null;
                                            $frameLine = $frame['line'] ?? null;
                                            $callable = isset($frame['class'])
                                                ? $frame['class'].($frame['type'] ?? '::').($frame['function'] ?? '')
                                                : ($frame['function'] ?? null);
                                        @endphp
                                        <div class="trace-frame">
                                            <div class="trace-call">#{{ $index }} {{ $callable ?: '{closure}' }}</div>
                                            <div class="trace-location">{{ $frameFile ? $frameFile.':'.($frameLine ?? '?') : 'internal' }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                    </tr>
                </table>

                <table class="email-content" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td class="footer-cell">
                            <p class="footer-text">This is an automated error notification from <strong>{{ $appName }}</strong>.</p>
                            <p class="footer-text">Sent by laravel-error-notifier &middot; {{ now()->year }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
