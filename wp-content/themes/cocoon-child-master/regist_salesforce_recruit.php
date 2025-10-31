<?php
// // 送信先のメールアドレス
$from = 'recruitment@tmgt.jp';

// フォームから送信されたデータを取得
$syokusyu = $_POST['job_postings'];
$name = $_POST['your-name'];
$furigana = $_POST['your-furigana'];
$birthday = $_POST['your-birthday'];
$address = $_POST['your-address'];
$email = $_POST['your-email'];
$tel = $_POST['your-tel'];
$shitsumon = $_POST['your-shitsumon'];



header('Location: /form/recruit_done');
?>
