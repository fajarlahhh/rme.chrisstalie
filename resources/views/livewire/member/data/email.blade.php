<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Verifikasi Email - Chrisstalie Derma Clinic</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f7fafc;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .email-container {
            background: #fff;
            width: 100%;
            max-width: 500px;
            margin: 40px auto;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.09);
            padding: 36px 32px 32px 32px;
            border: 1px solid #e3e3e3;
        }

        .logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo img {
            max-width: 160px;
        }

        h2 {
            color: #317292;
            text-align: center;
            margin-top: 0;
        }

        .button {
            display: inline-block;
            padding: 14px 28px;
            background-color: #38b6ff;
            color: #fff !important;
            border-radius: 6px;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: .5px;
            margin: 32px auto 18px auto;
            text-align: center;
            transition: background 0.2s;
        }

        .button:hover {
            background: #317292;
        }

        .footer {
            text-align: center;
            color: #888;
            font-size: 13px;
            margin-top: 24px;
        }

        .verify-link {
            display: block;
            word-break: break-all;
            background: #f2f6fa;
            padding: 10px 12px;
            border-radius: 4px;
            color: #317292;
            font-size: 13px;
            margin: 12px 0;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="logo">
            <img src="https://rme.chrisstaliederma.id/assets/img/favicon.png" alt="Chrisstalie Derma Clinic Logo">
        </div>
        <h2>Verifikasi Email Anda</h2>
        <p>
            Halo <strong>{{ $nama }}</strong>,
        </p>
        <p>
            Terima kasih telah melakukan pendaftaran di <strong>Chrisstalie Derma Clinic</strong>.<br>
            Untuk menyelesaikan proses pendaftaran, silakan verifikasi email Anda dengan menekan tombol di bawah ini:
        </p>
        <div style="text-align:center;">
            <a href="{{ $url }}" class="button" target="_blank">
                Verifikasi Email
            </a>
        </div>
        <p style="margin-top:30px;">
            Jika tombol di atas tidak berfungsi, Anda dapat menyalin dan menempel link berikut ini di browser Anda:
        </p>
        <div class="verify-link">{{ $url }}</div>
        <p>
            Email ini dikirim secara otomatis. Mohon untuk tidak membalas email ini.
        </p>
        <div class="footer">
            &copy; {{ date('Y') }} Chrisstalie Derma Clinic<br>
            <span style="color:#317292;">Sehat dan Cantik Bersama Kami</span>
        </div>
    </div>
</body>

</html>
