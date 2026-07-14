<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">

    <title>Access Denied | Organization TMS</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        rel="stylesheet"
    >

    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --primary-soft: #eef2ff;
            --danger: #dc2626;
            --danger-soft: #fef2f2;
            --ink: #172033;
            --muted: #64748b;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
            color: var(--ink);
            font-family: Arial, Helvetica, sans-serif;
            background:
                radial-gradient(circle at 12% 12%, rgba(99, 102, 241, .18), transparent 30%),
                radial-gradient(circle at 88% 85%, rgba(14, 165, 233, .15), transparent 32%),
                #f8fafc;
        }

        .error-page {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
            isolation: isolate;
        }

        .ambient-shape {
            position: absolute;
            z-index: -1;
            border-radius: 50%;
            filter: blur(2px);
            animation: drift 8s ease-in-out infinite alternate;
        }

        .ambient-shape-one {
            top: 8%;
            right: 8%;
            width: 110px;
            height: 110px;
            background: rgba(99, 102, 241, .10);
        }

        .ambient-shape-two {
            bottom: 8%;
            left: 7%;
            width: 150px;
            height: 150px;
            background: rgba(14, 165, 233, .09);
            animation-delay: -3s;
        }

        .error-card {
            width: 100%;
            max-width: 880px;
            overflow: hidden;
            background: rgba(255, 255, 255, .96);
            border: 1px solid rgba(79, 70, 229, .12);
            border-radius: 28px;
            box-shadow: 0 28px 70px rgba(30, 41, 59, .15);
            animation: cardEntrance .75s cubic-bezier(.22, 1, .36, 1) both;
        }

        .visual-panel {
            min-height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 56px 36px;
            background: linear-gradient(145deg, var(--primary), var(--primary-dark));
        }

        .shield-wrap {
            position: relative;
            width: 210px;
            height: 210px;
            display: grid;
            place-items: center;
        }

        .shield-ring {
            position: absolute;
            inset: 0;
            border: 2px dashed rgba(255, 255, 255, .35);
            border-radius: 50%;
            animation: rotateRing 18s linear infinite;
        }

        .shield-ring::before,
        .shield-ring::after {
            content: '';
            position: absolute;
            width: 13px;
            height: 13px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 0 18px rgba(255, 255, 255, .8);
        }

        .shield-ring::before {
            top: 13px;
            left: 33px;
        }

        .shield-ring::after {
            right: 19px;
            bottom: 27px;
        }

        .shield-icon {
            position: relative;
            width: 132px;
            height: 132px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, .42);
            border-radius: 38px;
            color: #fff;
            font-size: 58px;
            background: rgba(255, 255, 255, .16);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .32), 0 18px 38px rgba(24, 21, 88, .28);
            backdrop-filter: blur(8px);
            animation: shieldFloat 3.4s ease-in-out infinite;
        }

        .shield-icon::after {
            content: '';
            position: absolute;
            inset: -12px;
            border: 1px solid rgba(255, 255, 255, .20);
            border-radius: 46px;
            animation: pulse 2.2s ease-out infinite;
        }

        .content-panel {
            padding: 58px 52px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            padding: 8px 14px;
            color: var(--danger);
            background: var(--danger-soft);
            border: 1px solid #fecaca;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .08em;
        }

        .error-code {
            margin: 0;
            color: var(--primary);
            font-size: clamp(66px, 10vw, 104px);
            font-weight: 900;
            line-height: .9;
            letter-spacing: -.06em;
        }

        h1 {
            margin: 22px 0 14px;
            font-size: clamp(28px, 4vw, 40px);
            font-weight: 800;
            letter-spacing: -.03em;
        }

        .description {
            max-width: 470px;
            margin-bottom: 28px;
            color: var(--muted);
            font-size: 16px;
            line-height: 1.75;
        }

        .help-note {
            display: flex;
            gap: 12px;
            margin-bottom: 30px;
            padding: 14px 16px;
            color: #475569;
            background: var(--primary-soft);
            border-left: 4px solid var(--primary);
            border-radius: 10px;
            font-size: 14px;
            line-height: 1.5;
        }

        .help-note i {
            margin-top: 3px;
            color: var(--primary);
        }

        .btn-dashboard {
            padding: 12px 22px;
            color: #fff;
            background: var(--primary);
            border-color: var(--primary);
            border-radius: 10px;
            font-weight: 700;
            box-shadow: 0 10px 22px rgba(79, 70, 229, .24);
            transition: transform .2s ease, box-shadow .2s ease, background-color .2s ease;
        }

        .btn-dashboard:hover,
        .btn-dashboard:focus {
            color: #fff;
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(79, 70, 229, .30);
        }

        .footer-note {
            margin-top: 26px;
            color: #94a3b8;
            font-size: 12px;
        }

        @keyframes cardEntrance {
            from {
                opacity: 0;
                transform: translateY(24px) scale(.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes shieldFloat {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-12px);
            }
        }

        @keyframes rotateRing {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes pulse {
            0% {
                opacity: .7;
                transform: scale(.92);
            }
            70%, 100% {
                opacity: 0;
                transform: scale(1.16);
            }
        }

        @keyframes drift {
            to {
                transform: translate(18px, -20px) scale(1.08);
            }
        }

        @media (max-width: 767.98px) {
            .error-card {
                max-width: 560px;
                border-radius: 22px;
            }

            .visual-panel {
                padding: 32px 24px;
            }

            .shield-wrap {
                width: 150px;
                height: 150px;
            }

            .shield-icon {
                width: 94px;
                height: 94px;
                border-radius: 28px;
                font-size: 42px;
            }

            .content-panel {
                padding: 38px 26px;
                text-align: center;
            }

            .description {
                margin-right: auto;
                margin-left: auto;
            }

            .help-note {
                text-align: left;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
            }
        }
    </style>
</head>

<body>
    <main class="error-page">
        <span class="ambient-shape ambient-shape-one" aria-hidden="true"></span>
        <span class="ambient-shape ambient-shape-two" aria-hidden="true"></span>

        <section class="error-card" aria-labelledby="error-title">
            <div class="row g-0">
                <div class="col-md-5">
                    <div class="visual-panel">
                        <div class="shield-wrap" aria-hidden="true">
                            <div class="shield-ring"></div>
                            <div class="shield-icon">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="content-panel">
                        <div class="status-badge">
                            <i class="fa-solid fa-lock"></i>
                            RESTRICTED AREA
                        </div>

                        <p class="error-code" aria-label="Error 403">403</p>
                        <h1 id="error-title">Access Denied</h1>

                        <p class="description">
                            You do not have permission to access this page or perform this action.
                            Please return to an area available to your account.
                        </p>

                        <div class="help-note">
                            <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                            <span>If you believe this is a mistake, contact your system administrator for access.</span>
                        </div>

                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-dashboard">
                                <i class="fa-solid fa-house me-2"></i>
                                Return to Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-dashboard">
                                <i class="fa-solid fa-right-to-bracket me-2"></i>
                                Go to Login
                            </a>
                        @endauth

                        <div class="footer-note">
                            &copy; {{ date('Y') }} Organization Task Management System
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
