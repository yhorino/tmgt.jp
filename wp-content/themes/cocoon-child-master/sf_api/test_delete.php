<?php
require_once 'SalesforceAPI.php';

try {
    if(!isset($_GET['id']) || $_GET['id'] == ''){
        echo 'idをセットしてください';
        exit;
    }

    $sf = new SalesforceAPI();

    $accountId = $_GET['id'];

    $sf->delete('Moushikomi__c', $accountId);
    echo "✔ 削除成功: Moushikomi__c ID = {$accountId}\n";
    echo "Salesforce 組織でレコードが削除されたことを確認してください。\n";

} catch (Exception $e) {
    echo "エラー: " . $e->getMessage() . "\n";
}
