<?php
require_once 'SalesforceAPI.php';

try {
    if(!isset($_GET['id']) || $_GET['id'] == ''){
        echo 'idをセットしてください';
        exit;
    }

    $sf = new SalesforceAPI();

    $accountId = $_GET['id'];

    $updateData = [
        'LastName__c' => '更新済みアカウント_',
        'FIrstName__c' => date('Ymd_His'),
    ];
    $sf->update('Moushikomi__c', $accountId, $updateData);
    echo "✔ 更新成功: Moushikomi__c ID = {$accountId}\n";
    echo "Salesforce 組織でレコードが更新されていることを確認してください。\n";

} catch (Exception $e) {
    echo "エラー: " . $e->getMessage() . "\n";
}
