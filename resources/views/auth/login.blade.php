<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('login.h1') }}</title>
    <meta name="description" content="{{ __('login.para') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ __('login.h1') }}">
    <meta property="og:description" content="{{ __('login.para') }}">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">
    <meta property="og:site_name" content="{{ __('login.app_name') }}">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ __('login.h1') }}">
    <meta name="twitter:description" content="{{ __('login.para') }}">
    <meta name="twitter:image" content="{{ asset('images/logo.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            overflow: hidden;
            background: #f8f9fa;
        }

        .login-gov-header {
            min-height: 100px;
            display: grid;
            grid-template-columns: 180px minmax(0, 1fr) 180px;
            align-items: center;
            gap: 20px;
            padding: 10px 30px;
            background: #fff;
            border-bottom: 1px solid #e9ecef;
        }

        .login-gov-logo {
            display: flex;
            align-items: center;
        }

        .login-gov-logo img {
            max-width: 150px;
            max-height: 100px;
            object-fit: contain;
        }

        .login-gov-logo-right {
            justify-content: flex-end;
        }

        .login-gov-title {
            text-align: center;
            color: #000;
            line-height: 1.25;
        }

        .login-gov-title h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
        }

        .login-gov-title p {
            margin: 4px 0 0;
            font-size: 19px;
            font-weight: 800;
        }

        .login-gov-right-mark {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            color: #4b5563;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .login-gov-right-mark strong {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 82px;
            height: 34px;
            border: 1px solid #9ca3af;
            border-radius: 999px;
            color: #111827;
            font-size: 15px;
            font-weight: 800;
        }

        .login-container {
            height: calc(100vh - 0px);
        }
 


        .left-panel {
            background: linear-gradient(rgb(194 215 247 / 75%), rgb(0 0 0 / 75%)), /* url('/images/bg_image.png'); */ url(https://images.unsplash.com/photo-1521791136064-7986c2920216);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #fff;
        }

        .left-content {
            max-width: 520px;
        }

        .system-title {
            font-size: 42px;
            font-weight: 700;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
        }

        .form-control {
            height: 50px;
        }

        .btn-login {
            height: 50px;
            font-weight: 600;
        }

        .left-panel {
            position: relative;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            inset: 0;

            background: rgba(0, 0, 0, 0.55);
        }

        .left-content {
            position: relative;
            z-index: 2;
        }

        @media(max-width:991px) {
            body {
                overflow: auto;
            }

            .login-gov-header {
                min-height: auto;
                grid-template-columns: 72px minmax(0, 1fr) 72px;
                gap: 10px;
                padding: 12px 14px;
            }

            .login-gov-logo img {
                max-width: 62px;
                max-height: 58px;
            }

            .login-gov-title h1 {
                font-size: 16px;
            }

            .login-gov-title p {
                font-size: 12px;
            }

            .login-gov-right-mark strong {
                width: 50px;
                height: 24px;
                font-size: 11px;
            }

            .login-gov-right-mark span {
                display: none;
            }

            .login-container {
                height: auto;
                min-height: calc(100vh - 82px);
            }

            .left-panel {
                display: none !important;
            }

        }
    </style>
</head>

<body>
    <div class="container-fluid">

        <div class="row login-container">

            <!-- LEFT PANEL -->

            <div class="col-lg-7 d-flex align-items-center justify-content-center left-panel">
                <div class="left-content">
                    <h1 class="system-title mb-4">
                        {{ __('login.h1') }}
                    </h1>
                    <p class="lead">
                        {{ __('login.para') }}
                    </p>
                    <hr>
                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            ✅ {{__('login.home_item1')}}
                        </div>
                        <div class="col-md-6 mb-3">
                            ✅ {{__('login.home_item2')}}
                        </div>
                        <div class="col-md-6 mb-3">
                            ✅ {{__('login.home_item3')}}
                        </div>
                        <div class="col-md-6 mb-3">
                            ✅ {{__('login.home_item4')}}
                        </div>
                        <div class="col-md-6 mb-3">
                            ✅ {{__('login.home_item5')}}
                        </div>
                        <div class="col-md-6 mb-3">
                            ✅ {{__('login.home_item6')}}
                        </div>

                    </div>

                </div>

            </div>

            <!-- RIGHT PANEL -->

            <div class="col-lg-5 bg-white d-flex align-items-center justify-content-center">

                <div class="login-card">

                    <div class="text-center mb-5">

                        <img
                            src="/images/logo.png"
                            width="200"
                            class="mb-3"
                            alt="Logo">

                        <h2 class="fw-bold">
                            Welcome
                        </h2>

                        <p class="text-muted">
                            Sign in to continue
                        </p>

                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">

                            <label class="form-label"> Username / Email</label>
                            <input name="username" id="username" type="text" class="form-control" placeholder="Enter Username" value="{{ old('username') }}">
                            @error('username')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                            @enderror

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="Enter Password">
                            @error('password')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                            @enderror

                        </div>

                        <div class="d-flex justify-content-between mb-4">

                            <div class="form-check">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="remember">

                                <label
                                    class="form-check-label"
                                    for="remember">
                                    Remember Me
                                </label>

                            </div>

                            <a href="#">
                                Forgot Password?
                            </a>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary btn-login w-100">
                            Login
                        </button>

                    </form>

                    <div class="text-center mt-5">

                        <small class="text-muted">
                            {{ __('login.footer') }}
                            <br>
                            {{ __('login.version') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
