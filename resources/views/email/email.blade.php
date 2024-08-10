<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Forgot Password</title>
    <style>
        body {
            background-color: #ddd;
        }

        .invoice-box {
            font-size: 12px;
            max-width: 600px;
            background-color: #fff;
            margin: auto;
            padding: 30px;
            border-bottom: 3px solid #0059ff;
            line-height: 24px;
            font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
            color: #555;
            min-height: 85vh;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table td.third {
            text-align: right;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        .invoice {
            padding: 1rem;
        }

        #scan {
            float: right;
        }

        #scan img {
            max-width: 100%;
            height: auto;
        }

        @media print {
            .invoice-box {
                border: 0;
            }
        }

        .preserve-whitespace {
            white-space: pre-wrap;
        }

        .reset-button {
            display: inline-block;
            padding: 8px 16px;
            font-size: 0.9rem;
            color: #ffffff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 25px;
            /* margin-top: 1rem; */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .reset-button:hover {
            background-color: #0056b3;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="invoice">
            <h1 style="color: black;margin: 0px;">Hello Chandru,</h1>
            <div id="email-content" class="preserve-whitespace" style="margin: 0.5rem 0 0; font-size: 0.9rem;">
                We received a request to reset your password. Click the button below to reset your password:
            </div>
            <div style="text-align: center; margin: 0rem 0;">
                <a href="${resetLink}" class="reset-button">Reset Password</a>
            </div>
            <div id="email-content" class="preserve-whitespace" style="margin: 0.5rem 0 0; font-size: 0.9rem;">
                If you did not request a password reset, please ignore this email. Your password will not be changed.
            </div>
            <hr />
            <p style="margin: 2rem 0 0;">Best regards,</p>
            <h4 style="margin: 0;">Masergy</h4>
            <p style="margin: 0;">masergy@gmail.com</p>
            <p style="margin: 0;">+65 2345 6543</p>
            <p style="margin: 0 0 2rem 0;"><a href="https://masergy.com.sg/">https://masergy.com.sg/</a></p>
            <hr />
        </div>
    </div>
</body>

</html>
