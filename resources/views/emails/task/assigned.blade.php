<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>New Task Assigned</title>
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
                        <td style="background:#e6f3ff;padding:30px 40px;border-bottom:1px solid #d6e8f7;">

                            <div style="font-size:12px;color:#6b7280;letter-spacing:1px;text-transform:uppercase;">
                                Task Management System
                            </div>

                            <h2 style="margin:8px 0 0 0;color:#1f4e79;font-weight:600;">
                                New Task Assigned
                            </h2>

                            <p style="margin-top:8px;color:#5b6b7a;font-size:14px;">
                                A new task has been assigned to you for action and periodic updates.
                            </p>

                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:35px 40px;">

                            <p style="margin-top:0;">
                                Dear <strong>{{ $owner->name }}</strong>,
                            </p>

                            <p style="line-height:1.7;color:#4b5563;">
                                You have been assigned a new task in the Task Management System.
                                Please review the task details below and provide updates as per the
                                expected update frequency until completion.
                            </p>

                            <!-- Task Summary Card -->
                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="margin-top:25px;background:#f9fbfc;
                                      border:1px solid #e5edf3;
                                      border-radius:10px;">

                                <tr>
                                    <td colspan="2"
                                        style="padding:18px 22px;
                                           background:#eef7ff;
                                           border-bottom:1px solid #d9e8f5;
                                           font-weight:600;
                                           color:#1f4e79;">
                                        Assignment Details
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
                                        Assigned By
                                    </td>
                                    <td style="padding:14px 22px;">
                                        {{ $creator->name }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:14px 22px;color:#6b7280;">
                                        Assigned On
                                    </td>
                                    <td style="padding:14px 22px;">
                                        {{ $task->created_at->format('d M Y h:i A') }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:14px 22px;color:#6b7280;">
                                        Priority
                                    </td>
                                    <td style="padding:14px 22px;">
                                        @php
                                        $priorityName = $task->priority?->priority_name ?? 'Not specified';
                                        $priorityColor = $task->priority?->color_code ?? '#4c5a9b';
                                        $priorityStyle = 'background:#f8fafc; color:' . $priorityColor . '; border:1px solid ' . $priorityColor . '; padding:5px 12px; border-radius:20px; font-size:12px; font-weight:600; display:inline-block;';
                                        @endphp

                                        <span style="{{ $priorityStyle }}">
                                            {{ $priorityName }}
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:14px 22px;color:#6b7280;">
                                        Planned Completion Date
                                    </td>
                                    <td style="padding:14px 22px;">
                                        @php($dueDate = $task->planned_completion_date ?? $task->next_update_due_date)
                                        {{ $dueDate ? $dueDate->format('d M Y') : 'Not specified' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:14px 22px;color:#6b7280;">
                                        Update Frequency
                                    </td>
                                    <td style="padding:14px 22px;">
                                        {{ $task->is_perennial ? 'Periodic updates required' : 'As needed' }}
                                    </td>
                                </tr>

                            </table>

                            @if(!empty($task->task_details))
                            <div style="
                            margin-top:25px;
                            background:#fafbfd;
                            border-left:4px solid #c8dff0;
                            padding:18px;
                            border-radius:6px;">

                                <div style="font-weight:600;margin-bottom:8px;color:#1f4e79;">
                                    Task Description
                                </div>

                                <div style="line-height:1.7;color:#4b5563;">
                                    {!! nl2br(e($task->task_details)) !!}
                                </div>

                            </div>
                            @endif

                            <!-- Action Required -->
                            <div style="
                            margin-top:25px;
                            padding:18px;
                            background:#f5faf7;
                            border:1px solid #dcefe2;
                            border-radius:8px;">

                                <div style="font-weight:600;color:#355e3b;margin-bottom:10px;">
                                    Action Required
                                </div>

                                <ul style="margin:0;padding-left:18px;color:#4b5563;line-height:1.8;">
                                    <li>Review the assigned task.</li>
                                    <li>Begin work as planned.</li>
                                    <li>Submit periodic progress updates.</li>
                                    <li>Update task status until completion.</li>
                                </ul>

                            </div>

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
                                    Review Task
                                </a>

                            </div>

                            <!-- Note -->
                            <p style="
                            margin-top:30px;
                            color:#6b7280;
                            font-size:13px;
                            line-height:1.7;">
                                Automated reminders will be sent before the due date and for any pending updates based on the configured update frequency.
                            </p>

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
