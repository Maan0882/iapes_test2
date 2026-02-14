<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Interview Scheduled</title>
</head>
<body style="font-family: Arial, sans-serif; font-size:14px; color:#333;">

    <h2>Interview Scheduled</h2>

    <p>Dear {{ $application->name ?? 'Candidate' }},</p>

    <p>Your interview has been scheduled.</p>

    <p><strong>Batch:</strong> {{ $batch->batch_name ?? 'Not Assigned' }}</p>

    <p>
        <strong>Date:</strong>
        {{ $batch->interview_date
            ? \Carbon\Carbon::parse($batch->interview_date)->format('d-m-Y')
            : 'Not Assigned' }}
    </p>

    <p>
        <strong>Time:</strong>
        {{ $batch->start_time
            ? \Carbon\Carbon::parse($batch->start_time)->format('h:i A')
            : 'Not Assigned' }}
    </p>

    <p><strong>Location:</strong> {{ $batch->location ?? 'Not Assigned' }}</p>

    <hr style="border:none;border-top:1px solid #ccc;margin:20px 0;">

    <p>Please be available 15 minutes before the scheduled time.</p>

    <p>Best Regards,<br>TechStrota Team</p>

    <!-- Signature Section -->
    <table cellpadding="0" cellspacing="0" border="0"
           style="font-family: Arial, sans-serif; font-size:14px; color:#333;">
        <tr>
            <!-- Logo -->
            <td style="vertical-align:middle; padding-right:10px;">
                <img 
                    src="https://techstrota.com/images/logo/TsLogo.png"
                    alt="TechStrota Logo"
                    width="170"
                    style="display:block; max-width:170px; height:auto;"
                />
            </td>

            <!-- Company Details -->
            <td style="vertical-align:middle;">
                <strong style="font-size:16px;">
                    <a href="https://techstrota.com" style="text-decoration:none; color:#1F6AAE;">
                        TechStrota
                    </a>
                </strong><br>

                <a href="https://techstrota.com" style="color:#1F6AAE; text-decoration:underline;">Website</a> |
                <a href="https://linkedin.com/company/techstrota" style="color:#1F6AAE; text-decoration:underline;">LinkedIn</a> |
                <a href="https://wa.me/918128840055" style="color:green; text-decoration:underline;">WhatsApp</a>
                <br>

                Mobile:
                <a href="tel:+918128840055" style="color:#1F6AAE; text-decoration:underline;">
                    +91 8128840055
                </a>,
                Email:
                <a href="mailto:sales@techstrota.com" style="color:#1F6AAE; text-decoration:underline;">
                    sales@techstrota.com
                </a>
                <br>

                CIN: GJ240114897<br>
                156, 1st Floor, K10 Atlantis, C Tower, Vadodara, Gujarat,
                <b>India</b> – 390007
            </td>
        </tr>
    </table>

</body>
</html>
