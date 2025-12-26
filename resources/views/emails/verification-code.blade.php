<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kode Verifikasi JavaDev</title>
</head>

<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #374151; background-color: #f9fafb; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); overflow: hidden;">
        <div style="background: linear-gradient(135deg, #2563eb, #3b82f6); color: white; padding: 30px; text-align: center;">
            <h1 style="margin: 0 0 10px 0;">JavaDev Verification</h1>
            <p style="margin: 0;">Kode verifikasi untuk akun {{ $username }}</p>
        </div>

        <div style="padding: 40px 30px;">
            <h2 style="margin: 0 0 15px 0; color: #374151;">Halo {{ $username }}!</h2>
            <p style="margin: 0 0 15px 0;">Terima kasih telah mendaftar di JavaDev. Gunakan kode verifikasi berikut untuk menyelesaikan pendaftaran:</p>

            <div style="background: #f3f4f6; border: 2px dashed #d1d5db; border-radius: 8px; padding: 20px; text-align: center; margin: 20px 0;">
                <div style="font-size: 32px; font-weight: bold; color: #2563eb; letter-spacing: 4px; font-family: 'Courier New', monospace;">{{ $code }}</div>
            </div>

            <p style="margin: 15px 0 10px 0;"><strong>Penting:</strong></p>
            <ul style="margin: 0; padding-left: 20px;">
                <li style="margin-bottom: 5px;">Kode ini berlaku selama <strong>30 menit</strong></li>
                <li style="margin-bottom: 5px;">Kode hanya dapat digunakan <strong>1 kali</strong></li>
                <li style="margin-bottom: 5px;">Maksimal <strong>3 kali percobaan</strong> input</li>
            </ul>

            <p style="margin: 15px 0 0 0;">Jika Anda tidak merasa mendaftar di JavaDev, abaikan email ini.</p>
        </div>

        <div style="background: #f9fafb; padding: 20px 30px; text-align: center; font-size: 14px; color: #6b7280;">
            <p style="margin: 0 0 5px 0;">&copy; {{ date('Y') }} JavaDev. All rights reserved.</p>
            <p style="margin: 0;">Email ini dikirim otomatis, mohon tidak membalas.</p>
        </div>
    </div>
</body>

</html>
