<?php
// アクセストークンを取得後に表示するコールバックURLページ
// アクセストークンをサーバ上のjsonファイルに保存し
// APIが利用可能な状態になる

require_once 'settings.php';

// 認証コードを取得
$code = $_GET['code'];

if (!$code) {
    exit("コードが渡されていません。");
}

// トークンリクエストのパラメータ
$params = [
    'grant_type' => 'authorization_code',
    'code' => $code,
    'client_id' => CONSUMER_KEY,
    'client_secret' => CONSUMER_SECRET,
    'redirect_uri' => CALLBACK_URL,
];

// cURLでPOSTリクエストを送信
$ch = curl_init(OAUTH_URL);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'cURLエラー: ' . curl_error($ch);
    curl_close($ch);
    exit;
}

curl_close($ch);

// 結果をデコード
$data = json_decode($response, true);

// 結果確認と保存
if (isset($data['access_token'])) {
    echo "<h2>アクセストークン取得成功！</h2>";
    echo "Access Token: " . htmlspecialchars($data['access_token']) . "<br>";
    echo "Instance URL: " . htmlspecialchars($data['instance_url']) . "<br>";

    // 保存用データ
    $tokenData = [
        'access_token' => $data['access_token'],
        'refresh_token' => isset($data['refresh_token']) ? $data['refresh_token'] : null,
        'instance_url' => $data['instance_url'],
        'issued_at' => date('Y-m-d H:i:s'),
    ];

    // ファイルに保存（例: token.json）
    file_put_contents(__DIR__ . '/' . TOKEN_FILE, json_encode($tokenData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    echo "<p>トークン情報を <code>".TOKEN_FILE."</code> に保存しました。</p>";

} else {
    echo "<h2>アクセストークンの取得に失敗しました。</h2>";
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}
?>
