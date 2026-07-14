<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>System Under Maintenance</title>

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
            --primary-color: #5b6ee1;
            --secondary-color: #eef1ff;
            --text-color: #29324a;
            --muted-color: #6c757d;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background:
                radial-gradient(circle at top left, #f3e8ff 0, transparent 35%),
                radial-gradient(circle at bottom right, #e4f4ff 0, transparent 35%),
                #f8f9fc;
            color: var(--text-color);
            font-family: Arial, Helvetica, sans-serif;
        }

        .maintenance-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .maintenance-card {
            width: 100%;
            max-width: 850px;
            padding: 50px 40px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(91, 110, 225, 0.12);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(41, 50, 74, 0.12);
            text-align: center;
        }

        .maintenance-icon {
            width: 110px;
            height: 110px;
            margin: 0 auto 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--secondary-color);
            border-radius: 50%;
            color: var(--primary-color);
            font-size: 48px;
        }

        .status-code {
            display: inline-block;
            margin-bottom: 15px;
            padding: 7px 18px;
            background: #fff4df;
            border-radius: 30px;
            color: #b76e00;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        h1 {
            margin-bottom: 18px;
            font-size: 42px;
            font-weight: 700;
        }

        .description {
            max-width: 620px;
            margin: 0 auto;
            color: var(--muted-color);
            font-size: 17px;
            line-height: 1.8;
        }

        .progress-container {
            max-width: 500px;
            margin: 34px auto 12px;
        }

        .progress {
            height: 9px;
            background: #edf0f7;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar {
            width: 70%;
            background: linear-gradient(
                90deg,
                var(--primary-color),
                #8c70e8
            );
            animation: maintenanceProgress 2.5s ease-in-out infinite;
        }

        .info-box {
            max-width: 560px;
            margin: 30px auto 0;
            padding: 18px 20px;
            background: #f8f9fc;
            border-radius: 14px;
            color: var(--muted-color);
            font-size: 15px;
        }

        .footer-text {
            margin-top: 35px;
            color: #9ca3af;
            font-size: 13px;
        }

        @keyframes maintenanceProgress {
            0% {
                width: 20%;
            }

            50% {
                width: 80%;
            }

            100% {
                width: 20%;
            }
        }

        @media (max-width: 576px) {
            .maintenance-card {
                padding: 38px 22px;
                border-radius: 18px;
            }

            .maintenance-icon {
                width: 90px;
                height: 90px;
                font-size: 38px;
            }

            h1 {
                font-size: 30px;
            }

            .description {
                font-size: 15px;
            }
        }
    </style>
</head>

<body>
    <main class="maintenance-wrapper">
        <section class="maintenance-card">

            <div class="maintenance-icon">
                <i class="fa-solid fa-screwdriver-wrench"></i>
            </div>

            <div class="status-code">
                503 · MAINTENANCE MODE
            </div>

            <h1>We’ll Be Back Shortly</h1>

            <p class="description">
                The Task Management System is currently undergoing scheduled
                maintenance.
                Please check again after some time.
            </p>

            <div class="progress-container">
                <div class="progress">
                    <div
                        class="progress-bar"
                        role="progressbar"
                        aria-label="Maintenance progress"
                    ></div>
                </div>
            </div>

            <div class="info-box">
                <i class="fa-regular fa-clock me-2"></i>
                Our technical team is actively working to restore the system.<br>
                Your task data and information remain secure.
            </div>

            <div class="footer-text">
                &copy; {{ date('Y') }} Task Management System.
                All rights reserved.
            </div>

        </section>
    </main>
</body>
</html>