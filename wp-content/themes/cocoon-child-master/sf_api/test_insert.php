<?php
require_once 'SalesforceAPI.php';

try {
    $sf = new SalesforceAPI();

    $insertData = [
        'LastName__c' => 'テストアカウント_',
        'FIrstName__c' => date('Ymd_His'),
    ];
    $result = $sf->insert('Moushikomi__c', $insertData);
    echo "✔ 挿入成功: Moushikomi__c ID = " . $result['id'] . "\n";

    echo "Salesforce 組織でレコードを確認してください。\n";
    echo "このIDを控えてください: " . $result['id'] . "\n";

} catch (Exception $e) {
    echo "エラー: " . $e->getMessage() . "\n";
}
