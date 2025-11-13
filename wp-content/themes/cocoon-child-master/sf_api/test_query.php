<?php
require_once 'SalesforceAPI.php';

try {
    $sf = new SalesforceAPI();

    // SOQLクエリを記述
    $soql = "SELECT Id, Name, CreatedDate FROM Account ORDER BY CreatedDate DESC LIMIT 3";

    $result = $sf->query($soql);

    echo "<h2>SOQL実行結果</h2>";
    echo "<p>".$soql."</p>";
    echo "<pre>";
    $i = 0;
    foreach ($result as $record) {
        echo '['.($i++).']';
        print_r($record);
    }
    echo "</pre>";

} catch (Exception $e) {
    echo "<h2>エラー発生:</h2><pre>";
    echo htmlspecialchars($e->getMessage());
    echo "</pre>";
}
?>
