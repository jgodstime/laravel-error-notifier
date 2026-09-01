<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}</title>

    <style>
        :root {
            --accent: #e11d48;
            --accent-soft: #fef2f2;
            --text: #111827;
            --muted: #6b7280;
            --border: #e5e7eb;
            --bg: #f3f4f6;
            --card: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background-color: var(--bg);
            color: var(--text);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        .card {
            width: 100%;
            max-width: 480px;
            background-color: var(--card);
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .06), 0 8px 24px rgba(0, 0, 0, .06);
            padding: 40px 36px;
        }

        .icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background-color: var(--accent-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 20px;
            line-height: 1.4;
            margin: 0 0 8px;
            font-weight: 700;
        }

        .lead {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
            margin: 0 0 24px;
        }

        textarea {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 14px;
            font-family: inherit;
            color: var(--text);
            background-color: var(--card);
            resize: vertical;
            min-height: 120px;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(225, 29, 72, .12);
        }

        .error {
            display: block;
            color: var(--accent);
            font-size: 13px;
            margin-top: 6px;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        button {
            appearance: none;
            border: none;
            background-color: var(--accent);
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            padding: 10px 22px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color .15s ease;
        }

        button:hover {
            background-color: #be123c;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --text: #f3f4f6;
                --muted: #9ca3af;
                --border: #2a2f3a;
                --bg: #0b0d12;
                --card: #15181e;
                --accent-soft: #2a151b;
            }
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" />
                <path d="M12 9v4" />
                <path d="M12 17h.01" />
            </svg>
        </div>

        <h1>Something went wrong on our end</h1>
        <p class="lead">Our team has already been notified. If you can spare a moment, tell us what you were doing — it helps us fix it faster.</p>

        <form action="{{ route('notifier.send') }}" method="POST">
            @csrf
            <textarea name="message" rows="5" required
                placeholder="What were you trying to do when this happened?">{{ old('message') }}</textarea>
            @if (isset($errors) && $errors->has('message'))
                <span class="error">{{ $errors->first('message') }}</span>
            @endif

            <input type="hidden" name="access_url" value="{{ url()->current() }}">
            <input type="hidden" name="is_authenticated" value="{{ auth()->check() }}">
            <input type="hidden" name="id" value="{{ auth()->check() ? auth()->id() : null }}">
            <input type="hidden" name="email" value="{{ auth()->check() ? auth()->user()->email : null }}">
            <input type="hidden" name="notifier_message" value="{{ session()->get('error_notifier_package_message_123') }}">
            <input type="hidden" name="notifier_data" value="{{ session()->get('error_notifier_package_data_123') }}">
            <input type="hidden" name="notifier_file" value="{{ session()->get('error_notifier_package_file_123') }}">
            <input type="hidden" name="notifier_line" value="{{ session()->get('error_notifier_package_line_123') }}">

            <div class="actions">
                <button type="submit">Send report</button>
            </div>
        </form>
    </div>
</body>

</html>
