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
      'your-email',
      'your-shitsumon'
    ];
    if (!validateRequiredFields($requiredFields, $_POST)) {
      exit(SPAM_ERR_MSG);
    }
// // 送信先のメールアドレス
$from = 'recruitment@tmgt.jp';

// フォームから送信されたデータを取得
$syubetsu = htmlspecialchars($_POST['syubetsu']);
$name = htmlspecialchars($_POST['your-name']);
$email = htmlspecialchars($_POST['your-email']);
$shitsumon = htmlspecialchars($_POST['your-shitsumon']);

if($syubetsu === '採用について') {
    // メールの件名
    $subject = '【株式会社TMGT】お問い合わせありがとうございます';

    // メール文面をバッファリング
    ob_start();

    include 'tm_sendmail_contact_mailtext_saiyo.php';

    // メールの本文
    $message = ob_get_clean();
}
if($syubetsu === '業務提携') {
    // メールの件名
    $subject = '【株式会社TMGT】業務提携に関する労災保険のご確認';

    // メール文面をバッファリング
    ob_start();

    include 'tm_sendmail_contact_mailtext_teikei.php';

    // メールの本文
    $message = ob_get_clean();
}

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
    header('Location: /form/contact_done');
    exit;
} else {
    echo 'メールの送信に失敗しました。';
}
?>
