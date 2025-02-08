<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }
        }

        body {
            background-color: #f8fafc;
            color: #2d3748;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            background-color: #f8fafc;
            margin: 0;
            padding: 30px;
            width: 100%;
        }

        .inner-body {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin: 0 auto;
            max-width: 570px;
            padding: 32px;
        }

        .header {
            background-color: #4f46e5;
            margin: -32px -32px 32px -32px;
            padding: 32px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .logo {
            max-height: 50px;
            margin-bottom: 20px;
        }

        .title {
            color: #ffffff;
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            text-align: center;
        }

        .task-details {
            background-color: #f7fafc;
            border-radius: 6px;
            margin: 24px 0;
            padding: 20px;
        }

        .detail-row {
            margin-bottom: 12px;
        }

        .detail-label {
            color: #718096;
            font-weight: 600;
            margin-right: 8px;
        }

        .button {
            background-color: #4f46e5;
            border-radius: 4px;
            color: #ffffff !important;
            display: inline-block;
            font-weight: 600;
            font-size: 16px;
            margin: 24px 0;
            padding: 12px 24px;
            text-align: center;
            text-decoration: none;
        }

        .button:hover {
            background-color: #4338ca;
        }

        .footer {
            background-color: #1a202c;
            color: #ffffff;
            margin: 32px -32px -32px -32px;
            padding: 24px;
            text-align: center;
            border-radius: 0 0 8px 8px;
        }

        .footer p {
            margin: 8px 0;
            font-size: 14px;
        }

        .company-name {
            color: #a5b4fc;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="inner-body">
        <div class="header">
            <h1 class="title">Task Assigned</h1>
        </div>

        <p>Hello {{ $task->user->name }},</p>

        <p>You have been assigned a new task. Here are the details:</p>

        <div class="task-details">
            <div class="detail-row">
                <span class="detail-label">Title:</span>
                <span>{{ $task->title }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Description:</span>
                <span>{{ $task->description }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span>{{ $task->status }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Start Date:</span>
                <span>{{ $task->start_date->format('F j, Y H:i') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">End Date:</span>
                <span>{{ $task->end_date->format('F j, Y H:i') }}</span>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('employee.task.showSingle', $task->id) }}" class="button">
                View Task
            </a>
        </div>

        <div class="footer">
            <p>Thank you for your attention.</p>
            <p>Best regards,</p>
            <p class="company-name">OptiManage</p>
        </div>
    </div>
</div>
</body>
</html>
