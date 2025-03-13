<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your OTP Code</title>
</head>

<body>
    <h1>Dear {{ $name }},</h1>
    <p>To complete your login to the system, please use the following One-Time Password (OTP)</p>
    <p>OTP: <strong>{{ $OTP }}</strong></p>
    <p>This OTP is valid for the next {{ $expiryTime }} minutes. If you didn't initiate this action or if you think you
        received this
        email by mistake, please contact our support team immediately.</p>
    <p>Thank you,</p>
    <p>Milk Vending Team</p>

</body>

</html>