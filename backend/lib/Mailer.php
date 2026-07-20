<?php

require_once __DIR__ . '/phpmailer/Exception.php';
require_once __DIR__ . '/phpmailer/PHPMailer.php';
require_once __DIR__ . '/phpmailer/SMTP.php';

class Mailer {
    public static function send(string $to, string $subject, string $htmlBody): bool {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            $host = defined('SMTP_HOST') ? SMTP_HOST : '';
            $port = defined('SMTP_PORT') ? (int)SMTP_PORT : 587;
            $user = defined('SMTP_USER') ? SMTP_USER : '';
            $pass = defined('SMTP_PASS') ? SMTP_PASS : '';
            $from = defined('SMTP_FROM') ? SMTP_FROM : 'noreply@astroshreehari.com';
            $fromName = defined('SITE_NAME_EN') ? SITE_NAME_EN : 'Astro Shree Hari';

            if (!empty($host) && !empty($user)) {
                $mail->isSMTP();
                $mail->Host = $host;
                $mail->Port = $port;
                $mail->SMTPAuth = true;
                $mail->Username = $user;
                $mail->Password = $pass;
                $mail->SMTPSecure = $port === 465 ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            }

            $mail->setFrom($from, $fromName);
            $mail->addAddress($to);
            $mail->addReplyTo(defined('ADMIN_EMAIL') ? ADMIN_EMAIL : $from, $fromName);

            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body = $htmlBody;
            $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>', '</div>', '</li>'], "\n", $htmlBody));

            $mail->send();
            return true;

        } catch (\Throwable $e) {
            error_log("Mailer failed: " . $e->getMessage());
            return false;
        }
    }

    public static function clientConfirmation(array $d): string {
        $name = htmlspecialchars($d['name'] ?? '', ENT_QUOTES, 'UTF-8');
        $date = htmlspecialchars($d['preferred_date'] ?? '', ENT_QUOTES, 'UTF-8');
        $time = htmlspecialchars($d['preferred_time'] ?? '', ENT_QUOTES, 'UTF-8');
        $mode = htmlspecialchars($d['consultation_mode'] ?? '', ENT_QUOTES, 'UTF-8');
        $meetingUrl = htmlspecialchars($d['meeting_url'] ?? '', ENT_QUOTES, 'UTF-8');
        $serviceType = htmlspecialchars($d['service_type'] ?? '', ENT_QUOTES, 'UTF-8');

        $sl = ['kundali'=>'जन्मकुण्डली विश्लेषण','marriage'=>'विवाह तथा गुण मिलान','grahadasha'=>'ग्रहदशा परामर्श','vastu'=>'वास्तु परामर्श','pooja'=>'वैदिक कर्मकाण्ड','general'=>'सामान्य परामर्श'];
        $ml = ['phone'=>'फोन','whatsapp'=>'WhatsApp','video'=>'भिडियो परामर्श','inperson'=>'प्रत्यक्ष'];
        $serviceLabel = $sl[$serviceType] ?? $serviceType;
        $modeLabel = $ml[$mode] ?? $mode;

        $vr = $meetingUrl ? '<tr><td class="l" style="width:110px">भिडियो लिङ्क</td><td class="v"><a href="' . $meetingUrl . '" style="color:#671817">' . $meetingUrl . '</a></td></tr>' : '';

        return self::doc(
            self::header('परामर्श अनुरोध पुष्टि') . '
<tr><td class="c">
<h2 class="h">तपाईंको अनुरोध प्राप्त भयो</h2>
<p class="p">धन्यवाद ' . $name . ' ज्यू। तपाईंको परामर्श अनुरोध सफलतापूर्वक प्राप्त भएको छ। हामी २४ घण्टाभित्र सम्पर्क गर्नेछौं।</p>
<table class="t"><tr><td class="l" style="width:110px">सेवा</td><td class="v">' . $serviceLabel . '</td></tr><tr><td class="l">मिति</td><td class="v">' . $date . '</td></tr><tr><td class="l">समय</td><td class="v">' . $time . '</td></tr><tr><td class="l">माध्यम</td><td class="v">' . $modeLabel . '</td></tr>' . $vr . '</table>
<div class="nb"><strong>सम्पर्क:</strong> +977 9844639228 &nbsp;|&nbsp; <strong>इमेल:</strong> astroshreehari3m@gmail.com</div>
</td></tr>' . self::footer()
        );
    }

    public static function adminNotification(array $d): string {
        $name = htmlspecialchars($d['name'] ?? '', ENT_QUOTES, 'UTF-8');
        $phone = htmlspecialchars($d['phone'] ?? '', ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars($d['email'] ?? '', ENT_QUOTES, 'UTF-8');
        $date = htmlspecialchars($d['preferred_date'] ?? '', ENT_QUOTES, 'UTF-8');
        $time = htmlspecialchars($d['preferred_time'] ?? '', ENT_QUOTES, 'UTF-8');
        $mode = htmlspecialchars($d['consultation_mode'] ?? '', ENT_QUOTES, 'UTF-8');
        $serviceType = htmlspecialchars($d['service_type'] ?? '', ENT_QUOTES, 'UTF-8');
        $message = htmlspecialchars($d['message'] ?? '', ENT_QUOTES, 'UTF-8');
        $meetingUrl = htmlspecialchars($d['meeting_url'] ?? '', ENT_QUOTES, 'UTF-8');
        $birthDate = htmlspecialchars($d['birth_date'] ?? '', ENT_QUOTES, 'UTF-8');
        $birthTime = htmlspecialchars($d['birth_time'] ?? '', ENT_QUOTES, 'UTF-8');
        $birthPlace = htmlspecialchars($d['birth_place'] ?? '', ENT_QUOTES, 'UTF-8');

        $sl = ['kundali'=>'जन्मकुण्डली विश्लेषण','marriage'=>'विवाह तथा गुण मिलान','grahadasha'=>'ग्रहदशा परामर्श','vastu'=>'वास्तु परामर्श','pooja'=>'वैदिक कर्मकाण्ड','general'=>'सामान्य परामर्श'];
        $ml = ['phone'=>'फोन','whatsapp'=>'WhatsApp','video'=>'भिडियो परामर्श','inperson'=>'प्रत्यक्ष'];
        $serviceLabel = $sl[$serviceType] ?? $serviceType;
        $modeLabel = $ml[$mode] ?? $mode;

        $vr = $meetingUrl ? '<tr><td class="l">भिडियो लिङ्क</td><td class="v"><a href="' . $meetingUrl . '" style="color:#671817">' . $meetingUrl . '</a></td></tr>' : '';

        $bb = ($birthDate || $birthPlace) ? '
<h3 class="sh">जन्म विवरण</h3>
<table class="t"><tr><td class="l">मिति</td><td class="v">' . $birthDate . '</td></tr><tr><td class="l">समय</td><td class="v">' . $birthTime . '</td></tr><tr><td class="l">स्थान</td><td class="v">' . $birthPlace . '</td></tr></table>' : '';

        return self::doc(
            self::header('नयाँ परामर्श अनुरोध') . '
<tr><td class="c">
<div class="alert"><strong>कारबाही आवश्यक:</strong> ' . $name . ' ले "' . $modeLabel . '" माध्यमबाट परामर्श अनुरोध गरेको। कृपया चाँडै सम्पर्क गर्नुहोस्।</div>
<h3 class="sh">सम्पर्क विवरण</h3>
<table class="t"><tr><td class="l">नाम</td><td class="v">' . $name . '</td></tr><tr><td class="l">फोन</td><td class="v"><a href="tel:' . $phone . '" style="color:#671817;text-decoration:none">' . $phone . '</a></td></tr><tr><td class="l">इमेल</td><td class="v">' . ($email ?: '—') . '</td></tr><tr><td class="l">सेवा</td><td class="v">' . $serviceLabel . '</td></tr><tr><td class="l">मिति</td><td class="v">' . $date . '</td></tr><tr><td class="l">समय</td><td class="v">' . $time . '</td></tr><tr><td class="l">माध्यम</td><td class="v">' . $modeLabel . '</td></tr>' . $vr . '</table>' . $bb . '
<h3 class="sh">प्रश्न / समस्या</h3>
<div class="msg">' . $message . '</div>
<div class="ok"><strong>✅ Google Calendar</strong> मा इभेन्ट सिर्जना गरिएको छ। <a href="https://calendar.google.com" style="color:#0b8d4e">Calendar खोल्नुहोस् →</a></div>
</td></tr>' . self::footer()
        );
    }

    private static function header(string $title): string {
        return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" class="hdr"><tr><td>
<img src="https://www.astroshreehari.com/assets/shreehari-icon-192.png" alt="" width="48" height="48" style="border-radius:50%;border:2px solid #d8a443;margin-bottom:6px">
<h1 class="site">श्रीहरि ज्योतिष परामर्श केन्द्र</h1>
<p class="tag">' . $title . '</p>
</td></tr></table>';
    }

    private static function footer(): string {
        return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" class="ftr"><tr><td>
<p class="f1">श्रीहरि ज्योतिष परामर्श केन्द्र &bull; कमल-३, केरखा, झापा</p>
<p class="f2">© 2026 <a href="https://www.astroshreehari.com" style="color:#d8a443;text-decoration:none">Astro Shree Hari</a></p>
</td></tr></table>';
    }

    private static function doc(string $body): string {
        return '<!DOCTYPE html>
<html lang="ne">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body{margin:0;padding:0;background:#f9f0df;font-family:"Noto Sans Devanagari",system-ui,sans-serif}
  table{border-collapse:collapse;width:100%;max-width:600px;margin:0 auto}
  .hdr{background:linear-gradient(135deg,#3d0d0d,#671817);padding:28px 24px;text-align:center}
  .site{margin:6px 0 0;font-family:"Tiro Devanagari Sanskrit",serif;color:#d8a443;font-size:20px;font-weight:400}
  .tag{margin:2px 0 0;color:#f0c978;font-size:12px;opacity:.85}
  .c{padding:28px 24px;background:#fffaf1}
  .h{margin:0 0 4px;font-family:"Tiro Devanagari Sanskrit",serif;color:#671817;font-size:18px;font-weight:400}
  .p{margin:0 0 20px;color:#76665f;font-size:14px}
  .sh{margin:20px 0 6px;font-size:14px;color:#671817}
  .t{border:1px solid #e8c889;border-radius:10px;overflow:hidden;font-size:14px;width:100%}
  .l{padding:10px 16px;background:#f9f0df;font-weight:600;color:#2c1a16;white-space:nowrap;vertical-align:top}
  .v{padding:10px 16px;color:#2c1a16}
  .nb{margin:20px 0 0;padding:14px 18px;background:#f9f0df;border-radius:10px;border-left:4px solid #d8a443;font-size:13px;color:#76665f}
  .alert{margin:0 0 16px;padding:10px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;font-size:13px;color:#991b1b}
  .msg{margin:0;padding:12px 16px;background:#f9f0df;border-radius:10px;border-left:4px solid #671817;font-size:13px;color:#2c1a16;white-space:pre-wrap}
  .ok{margin:20px 0 0;padding:12px 16px;background:#f0fdf4;border-radius:10px;border-left:4px solid #0b8d4e;font-size:13px;color:#166534}
  .ftr{background:linear-gradient(135deg,#671817,#3d0d0d);padding:20px 24px;text-align:center}
  .f1{margin:0 0 4px;color:#f0c978;font-size:13px}
  .f2{margin:0;color:#76665f;font-size:11px}
</style></head>
<body>
<table>' . $body . '</table>
</body>
</html>';
    }
}
