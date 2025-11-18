<?php

define('SPAM_ERR_MSG', '入力内容に不備があります。お手数ですが、もう一度最初からお願いいたします。');

/**
 * 必須項目のサーバ側チェック関数
 *
 * @param array $requiredFields 必須項目の配列
 * @param array $inputData チェック対象のデータ（例: $_POST, $_GET）
 * @return bool すべての必須項目が設定されていれば true、1つでも未設定があれば false
 */
function validateRequiredFields($requiredFields, $inputData) {
    foreach ($requiredFields as $field) {
        if (!isset($inputData[$field]) || trim($inputData[$field]) === '') {
            return false;
        }
    }
    return true;
}

/* 使用例:
$requiredFields = ['name', 'email', 'message'];
if (!validateRequiredFields($requiredFields, $_POST)) {
    exit('必須項目が未入力です。');
}
*/

?>
