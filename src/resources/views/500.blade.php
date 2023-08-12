<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}</title>

    <style>
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }

        .h4,
        h4 {
            font-size: 1.9rem;
        }

        .h1,
        .h2,
        .h3,
        .h4,
        .h5,
        .h6,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            margin-top: 0;
            margin-bottom: 0.5rem;
            font-weight: 500;
            line-height: 1.2;
        }

        .text-end {
            text-align: right !important;
        }

        .mt-2 {
            margin-top: 0.5rem !important;
        }

        .text-muted {
            color: #6c757d !important;
        }

        textarea.form-control {
            min-height: calc(1.5em + 0.75rem + 2px);
        }

        .form-control {
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border-radius: 0.25rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        textarea {
            resize: vertical;
        }


        .btn {
            display: inline-block;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            text-align: center;
            text-decoration: none;
            vertical-align: middle;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
            background-color: transparent;
            border: 1px solid transparent;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            border-radius: 0.25rem;
            transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .btn-primary {
            color: #fff;
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .text-muted-primary {
            color: #28292a !important
        }
    </style>
</head>

<body>
    <section>
        <div class="container" style="padding-top:8rem;">

            <div class="" style="margin-left:12%; margin-right:12%;">
                <h4 class="text-muted">It looks like we're having some internal issues.</h4>
                <p class="text-muted">Our team has been notified. If you'd like to help, tell us what happened below.
                </p>

                <form action="{{ route('notifier.send') }}" method="POST">
                    @csrf
                    <textarea name="message" class="form-control" rows="7" required
                        placeholder="Describe what happened and how we might reproduce this error."></textarea>
                    @if (isset($errors) && $errors->has('message'))
                        <span class="text-danger">{{ $errors->first('message') }}</span>
                    @endif
                    <input type="hidden" name="access_url"
                        value="{{ url()->current() }}">
                    <input type="hidden" name="is_authenticated" value="{{ auth()->check() }}">
                    <input type="hidden" name="id" value="{{ auth()->check() ? auth()->id() : null }}">
                    <input type="hidden" name="email" value="{{ auth()->check() ? auth()->user()->email : null }}">
                    <input type="hidden" name="notifier_message" value="{{ session()->get('error_notifier_package_message_123') }}">
                    <input type="hidden" name="notifier_data" value="{{ session()->get('error_notifier_package_data_123') }}">
                    <input type="hidden" name="notifier_file" value="{{ session()->get('error_notifier_package_file_123') }}">
                    <input type="hidden" name="notifier_line" value="{{ session()->get('error_notifier_package_line_123') }}">


                    <div class="text-end mt-2">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>

            </div>

        </div>

    </section>
</body>

</html>
