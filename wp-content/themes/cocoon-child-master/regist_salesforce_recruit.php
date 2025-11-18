<?php
session_start();

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

require_once("./sf_api/sf_api_func.php");

sf_regist_recruit($_POST);

header('Location: /form/recruit_done');
?>
