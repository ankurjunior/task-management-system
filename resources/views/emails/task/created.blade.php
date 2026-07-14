<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Task Created Successfully</title>
</head>

<body style="margin:0;padding:0;background:#f5f7fa;font-family:'Segoe UI',Tahoma,sans-serif;color:#374151;">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding:30px 15px;">
        <tr>
            <td align="center">

                <table width="700" cellpadding="0" cellspacing="0"
                    style="background:#ffffff;border-radius:14px;overflow:hidden;
                            box-shadow:0 4px 20px rgba(0,0,0,0.06);">

                    <!-- Header -->
                    <tr>
                        <td style="background:#dceefb;padding:30px 40px;border-bottom:1px solid #c9e2f2;">

                            <div style="font-size:12px;color:#6b7280;letter-spacing:1px;text-transform:uppercase;">
                                Task Management System
                            </div>

                            <h2 style="margin:8px 0 0 0;color:#234e70;font-weight:600;">
                                Task Created Successfully
                            </h2>

                            <p style="margin-top:8px;color:#5b6b7a;font-size:14px;">
                                The task has been recorded and assigned successfully.
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:35px 40px;">

                            <p style="margin-top:0;">
                                Dear <strong>{{ $creator->name }}</strong>,
                            </p>

                            <p style="line-height:1.7;color:#4b5563;">
                                This is to confirm that a new task has been created in the
                                Task Management System and assigned to the designated owner.
                            </p>

                            <!-- Task Summary Card -->
                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="margin-top:25px;background:#f9fbfc;
                                        border:1px solid #e5edf3;
                                        border-radius:10px;">

                                <tr>
                                    <td colspan="2"
                                        style="padding:18px 22px;
                                            background:#edf7f2;
                                            border-bottom:1px solid #d8ebe0;
                                            font-weight:600;
                                            color:#355e3b;">
                                        Task Summary
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:14px 22px;width:35%;color:#6b7280;">
                                        Task Name
                                    </td>
                                    <td style="padding:14px 22px;font-weight:600;">
                                        {{ $task->task_name }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:14px 22px;color:#6b7280;">
                                        Assigned To
                                    </td>
                                    <td style="padding:14px 22px;">
                                        {{ $owner->name }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:14px 22px;color:#6b7280;">
                                        Priority
                                    </td>
                                    <td style="padding:14px 22px;">
                                        <span style="
                                            background:#fff4d6;
                                            color:#8a6d1d;
                                            padding:5px 12px;
                                            border-radius:20px;
                                            font-size:12px;
                                            font-weight:600;">
                                            {{ $task->priority }}
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:14px 22px;color:#6b7280;">
                                        Planned Completion Date
                                    </td>
                                    <td style="padding:14px 22px;">
                                        {{ \Carbon\Carbon::parse($task->planned_completion_date)->format('d M Y') }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:14px 22px;color:#6b7280;">
                                        Update Frequency
                                    </td>
                                    <td style="padding:14px 22px;">
                                        {{ $task->expected_update_frequency }}
                                    </td>
                                </tr>
                            </table>

                            @if(!empty($task->additional_details))
                            <div style="
                                margin-top:25px;
                                background:#fafbfd;
                                border-left:4px solid #c8dff0;
                                padding:18px;
                                border-radius:6px;">

                                <div style="font-weight:600;margin-bottom:8px;color:#234e70;">
                                    Additional Details
                                </div>

                                <div style="line-height:1.7;color:#4b5563;">
                                    {{ $task->additional_details }}
                                </div>

                            </div>
                            @endif

                            <!-- CTA -->
                            <div style="text-align:center;margin-top:35px;">

                                <a href="{{ $taskUrl }}"
                                    style="
                                background:#8fb8d8;
                                color:#ffffff;
                                text-decoration:none;
                                padding:14px 30px;
                                border-radius:8px;
                                display:inline-block;
                                font-weight:600;">
                                    View Task Details
                                </a>

                            </div>

                            <!-- Notification Note -->
                            <div style="
                                margin-top:30px;
                                padding:16px 18px;
                                background:#f3faf6;
                                border:1px solid #dcefe2;
                                border-radius:8px;
                                color:#46604c;
                                font-size:14px;">

                                You will automatically receive notifications regarding:
                                <ul style="margin:10px 0 0 20px;padding:0;">
                                    <li>Task updates submitted by the owner</li>
                                    <li>Approaching due dates</li>
                                    <li>Overdue tasks requiring attention</li>
                                    <li>Task completion and closure</li>
                                </ul>

                            </div>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="
                            background:#fafafa;
                            border-top:1px solid #ececec;
                            padding:20px 40px;
                            text-align:center;
                            font-size:13px;
                            color:#6b7280;">

                            Task Management System (TMS)<br>
                            Directorate Monitoring & Activity Tracking Platform

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>