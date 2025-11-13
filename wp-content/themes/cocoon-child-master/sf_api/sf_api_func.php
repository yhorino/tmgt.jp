<?php 
require_once __DIR__ . '/SalesforceAPI.php';

// 下記のようにインクルードする
// 相対パスで指定しなおすこと
// require_once("./mailform_new/sf_api/sf_api_func.php");

/**
 * エラーメッセージを出して終了する
 *
 * @param string $code    終了コード
 * @param string $str     エラーメッセージ
 */
if (!function_exists('err_die')) {
    function err_die($code, $str)
    {
        global $_config;

        // メッセージを表示（ログに残す）
        fatal($str);
        // 終了する
        exit($code);
    }
    function fatal($str)
    {
        echo "Fatal Error: " . "$str\n";
    }
}

/* SELECT ************************************************/
/*
function sf_soql_select($select, $from, $where, $orderby){
    try {
        $sf = new SalesforceAPI();

        $select = mb_convert_encoding($select, 'UTF-8', 'auto');
        $from = mb_convert_encoding($from, 'UTF-8', 'auto');
        $where = mb_convert_encoding($where, 'UTF-8', 'auto');
        $orderby = mb_convert_encoding($orderby, 'UTF-8', 'auto');
        // SOQLクエリを記述
        $soql = "SELECT $select FROM $from WHERE $where $orderby";

        $result = $sf->query($soql);

        return json_decode(json_encode($result), true);

    } catch (Exception $e) {
        err_die(__LINE__, __FUNCTION__ . "["  . __LINE__ . "]: ". $e->getMessage());
    }
}
*/

/* INSERT ************************************************/
/*
function sf_sendmail($item) {
  try {
    $sf = new SalesforceAPI();

    $insertData = [
      "EmailFrom__c"	 => $item["emailfrom"],
      "EmailTo__c"	 => $item["emailto"],
      "EmailTitle__c"	 => $item["emailtitle"],
      "EmailBody__c"	 => $item["emailbody"]
    ];
    $result = $sf->insert('SendEmail__c', $insertData);

  } catch (Exception $e) {
    err_die(__LINE__, __FUNCTION__ . "["  . __LINE__ . "]: ". $e->getMessage());
  }
}

function sf_soql_insert($type, $insertitems){
  try {
    $sf = new SalesforceAPI();

    $insertData = $insertitems;
    $result = $sf->insert($type, $insertData);

  } catch (Exception $e) {
    err_die(__LINE__, __FUNCTION__ . "["  . __LINE__ . "]: ". $e->getMessage());
  }
}
*/
function sf_regist_recruit($item) {
  try {
    define('RECORDTYPEID_KYUJINOUBO', '012RA000002x8zhYAA');
    $sf = new SalesforceAPI();

    $insertData = [
      "ouboshokushu__c"	 => $item['job_postings'],
      "Name"	 => $item["your-name"],
      "shimei__c"	 => $item["your-name"],
      "hurigana__c"	 => $item["your-furigana"],
      "birthday__c"	 => $item["your-birthday"],
      "ikonozyusyo__c"	 => $item["your-address"],
      "mail__c"	 => $item["your-email"],
      "tell__c"	 => $item["your-tel"],
      "situmonyaiken__c"	 => $item["your-shitsumon"],
      "RecordTypeId"	 => RECORDTYPEID_KYUJINOUBO,
      "uketukebumon__c"	 => '株式会社TMGT',
      "OBOKEIRO__c"	 => 'TMサイト',
      "OBOBI__c"	 => date('Y-m-d'),
      "senkosutetasu__c"	 => 'フォームから応募→自動返信メール送信',
    ];
    $result = $sf->insert('saiyo__c', $insertData);

  } catch (Exception $e) {
    err_die(__LINE__, __FUNCTION__ . "["  . __LINE__ . "]: ". $e->getMessage());
  }
}


/* UPDATE ************************************************/
/*
function Update_Moushikomi_TyakusyukinNyukin($order_no) {
    try {
        $sf = new SalesforceAPI();

        $select = "Id";
        $from = "Moushikomi__c";
        $where = "order_no__c = '$order_no'";
        $orderby = "";
        $result = sf_soql_select($select, $from, $where, $orderby);
        $Id = $result[0]['Id'];
        if(empty($Id)) return;

        $updateData = [
        'tyakusyukin_nyukin__c' => true
        ];
        $sf->update('Moushikomi__c', $Id, $updateData);

    } catch (Exception $e) {
        err_die(__LINE__, __FUNCTION__ . "["  . __LINE__ . "]: ". $e->getMessage());
    }
}
*/


/* DELETE ************************************************/

