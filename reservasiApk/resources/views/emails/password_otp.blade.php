<!doctype html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; line-height:1.6;">
    <h2>Reset Password</h2>
    <p>Kode OTP kamu:</p>

    <div style="font-size:28px; letter-spacing:6px; font-weight:bold;">
        {{ $otp }}
    </div>

    <p>Kode ini berlaku <b>{{ $ttlMinutes }} menit</b>.</p>
    <p>Jika kamu tidak merasa meminta reset password, abaikan email ini.</p>
</body>
</html>
