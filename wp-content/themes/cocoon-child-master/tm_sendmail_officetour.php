<?php

/*
if (!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] !== 'https://www.tmgt.jp/form_common/') {
    die('不正なアクセスです。');
}
 */

// スパム対策　入力チェック
$publicHtmlPath = str_replace('/private_html', '/public_html', $_SERVER['DOCUMENT_ROOT']);
include $publicHtmlPath.'/validateRequiredFields.php';

if (!empty($_SERVER['HTTP_REFERER'])) {
    // リファラの値
    $referer = $_SERVER['HTTP_REFERER'];

    // 正規表現でドメイン部分を抽出
    if (preg_match('/^(https?:\/\/)?([^\/]+)/i', $referer, $matches)) {
        $referrer_host = $matches[2]; // ドメイン部分が $matches[2] に入る

        // 許可するドメイン
        $allowed_domain = 'tmgt.jp';

        // ドメインが許可されたものかを確認
        if (strpos($referrer_host, $allowed_domain) === false) {
            die('不正なアクセスです。');
        } else {
            //echo 'リファラのドメインは許可されています: ' . $referrer_host;
        }
    } else {
        //die('リファラのドメインを解析できませんでした。');
    }
} else {
    // リファラがない場合の処理
    //die('リファラが送信されていません。');
    die('不正なアクセスです。');
}

    // スパム対策　入力チェック
    $requiredFields = [
      'your-name',
      'your-furigana',
      'your-birthday',
      'your-address',
      'your-email',
      'your-tel'
    ];
    if (!validateRequiredFields($requiredFields, $_POST)) {
      exit(SPAM_ERR_MSG);
    }
// // 送信先のメールアドレス
$from = 'recruitment@tmgt.jp';

// フォームから送信されたデータを取得
$date = $_POST['date'];
$name = $_POST['your-name'];
$furigana = $_POST['your-furigana'];
$birthday = $_POST['your-birthday'];
$address = $_POST['your-address'];
$email = $_POST['your-email'];
$tel = $_POST['your-tel'];
$syokusyu = $_POST['job_postings'];

// メールの件名
$subject = 'オフィスツアー（会社見学会）ご予約ありがとうございます！【身分証明書ご提示のお願い】';

// メールの本文
$message = "$name 様\n";
$message .= "\n";
$message .= "この度は、株式会社TMGTのオフィスツアー（会社見学会）にご興味をお持ちいただき、誠にありがとうございます。\n";
$message .= "\n";
$message .= "ご予約を確定させていただくために、身分証明書のご提示をお願いいたします。\n";
$message .= "運転免許証（両面）の画像をこちらのメールに返信ください。\n";
$message .= "\n";
$message .= "こちらのメール到着後、3日以内にお送りいただけますようお願いいたします。\n";
$message .= "※定員になり次第、受付を終了させていただく場合がございます。\n";
$message .= "\n";
$message .= "$name様の携帯電話に 0120-313-818 よりご連絡を差し上げることがあります。\n";
$message .= "お手数ではございますが、営業電話と区別していただくため、事前に電話番号のご登録をお願いいたします。\n";
$message .= "\n";
$message .= "ご入力いただいた内容は、下記のとおりです。\n";
$message .= "\n";
$message .= "-----------------------\n";
$message .= "希望日時：$date\n";
$message .= "お名前：$name\n";
$message .= "フリガナ：$furigana\n";
$message .= "生年月日：$birthday\n";
$message .= "住所：$address\n";
$message .= "メールアドレス：$email\n";
$message .= "電話番号：$tel\n";
$message .= "希望職種：$syokusyu\n";
$message .= "-----------------------\n";
$message .= "\n";
$message .= "何かご不明な点がございましたら、お気軽に下記の連絡先までお問い合わせください。\n";
$message .= "\n";
$message .= "=================================\n";
$message .= "　株式会社ＴＭＧＴ\n";
$message .= "\n";
$message .= "　〒486-0945　\n";
$message .= "　愛知県春日井市勝川町六丁目140番地\n";
$message .= "　王子不動産勝川ビル2F\n";
$message .= "    TEL：0120-313-818\n";
$message .= "    Email：recruitment@tmgt.jp\n";
$message .= "    公式サイト：https://www.tmgt.jp/\n";
$message .= "=================================\n";

// 添付ファイルの処理
$boundary = md5(uniqid(time()));

$headers = "From: 株式会社TMGT <$from>\r\n";
$headers .= "Reply-To: $from\r\n";
$headers .= "Cc: $from\r\n";  // BCCに応募者のメールアドレスを追加
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

$email_message = "--$boundary\r\n";
$email_message .= "Content-Type: text/plain; charset=UTF-8\r\n";
$email_message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$email_message .= $message . "\r\n\r\n";

// $files = array($rirekisyo, $syokumukeirekisyo);

// foreach ($files as $file) {
//     if (is_uploaded_file($file['tmp_name'])) {
//         $file_content = chunk_split(base64_encode(file_get_contents($file['tmp_name'])));
//         $email_message .= "--$boundary\r\n";
//         $email_message .= "Content-Type: " . $file['type'] . "; name=\"" . $file['name'] . "\"\r\n";
//         $email_message .= "Content-Transfer-Encoding: base64\r\n";
//         $email_message .= "Content-Disposition: attachment; filename=\"" . $file['name'] . "\"\r\n\r\n";
//         $email_message .= $file_content . "\r\n\r\n";
//     }
// }

$email_message .= "--$boundary--";

// メールを送信
if (mail($email, $subject, $email_message, $headers)) {
    header('Location: /form/officetour_done');
    exit;
} else {
    echo 'メールの送信に失敗しました。';
}
?>
