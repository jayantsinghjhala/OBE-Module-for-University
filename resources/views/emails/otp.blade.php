<!DOCTYPE html>
<html>
<head>
    <title>SPSU OBE Portal - OTP Verification</title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; padding: 20px;">
        <h2>Sir Padampat Singhania University - Online OBE Portal</h2>
        <p>Dear {{ $data["name"] }},</p>
        <p>You have requested an OTP for verification on the SPSU OBE portal. Please find your OTP below:</p>
        <h3>Verification OTP: {{ $data["otp"] }}</h3>
        <p>This OTP is valid for a single use and will expire shortly, so please use it promptly to complete your verification process.</p>
        <p>If you didn't request this OTP or have any concerns, please contact our support team.</p>
        <p>Thank you for choosing SPSU's Online OBE Portal.</p>
        <br>
        <p>Best regards,</p>
        <p>The SPSU OBE Portal Team</p>
    </div>
</body>
</html>
