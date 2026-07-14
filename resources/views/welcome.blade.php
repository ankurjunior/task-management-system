<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('login.h1') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            overflow: hidden;
            background: #f8f9fa;
        }

        .login-container {
            height: 100vh;
        }

        /* .left-panel {
            background:
                linear-gradient(rgba(13, 110, 253, .75),
                    rgba(13, 110, 253, .75)),
                url('https://images.unsplash.com/photo-1521791136064-7986c2920216');
            background-size: cover;
            background-position: center;
            color: #fff;
        } */


        .left-panel {
            background:
                linear-gradient(rgba(13, 110, 253, .75),
                    rgba(13, 110, 253, .75)),
                /* url('/images/bg_image.png'); */
                url('https://images.unsplash.com/photo-1521791136064-7986c2920216');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #fff;
        }

        .left-content {
            max-width: 500px;
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
                        Streamline task management, monitor progress, and enhance team collaboration with real-time updates, notifications, and comprehensive audit tracking.
                    </p>
                    <hr>
                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            ✅ Task Assignment
                        </div>
                        <div class="col-md-6 mb-3">
                            ✅ Progress Tracking
                        </div>
                        <div class="col-md-6 mb-3">
                            ✅ Audit Trail
                        </div>
                        <div class="col-md-6 mb-3">
                            ✅ Email Alerts
                        </div>
                        <div class="col-md-6 mb-3">
                            ✅ Reports
                        </div>
                        <div class="col-md-6 mb-3">
                            ✅ Dashboards
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
                            width="80"
                            class="mb-3"
                            alt="Logo">

                        <h2 class="fw-bold">
                            Welcome
                        </h2>

                        <p class="text-muted">
                            Sign in to continue
                        </p>

                    </div>

                    <form method="POST">

                        <div class="mb-3">

                            <label class="form-label">
                                Username / Email
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                placeholder="Enter Username">

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Password
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                placeholder="Enter Password">

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
                            Organization Task Management System
                            <br>
                            Version 1.0
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>