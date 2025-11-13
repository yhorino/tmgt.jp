<?php
// Create with chatGPT
// https://chatgpt.com/share/687a04f6-c4b8-8006-8dd5-0f33fb2ebb32

require_once __DIR__ . '/settings.php';

class SalesforceAPI {
    private $tokenFile;
    private $tokenData;


    public function __construct($tokenFile = TOKEN_FILE) {
        $this->tokenFile = __DIR__ . '/' . $tokenFile;
        $this->loadToken();
    }

    // トークン関連＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝
    private function loadToken() {
        if (!file_exists($this->tokenFile)) {
            throw new Exception("トークンファイルが見つかりません: {$this->tokenFile}");
        }
        $this->tokenData = json_decode(file_get_contents($this->tokenFile), true);
    }

    private function saveToken() {
        $this->tokenData['issued_at'] = date('Y-m-d H:i:s');
        file_put_contents($this->tokenFile, json_encode($this->tokenData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    private function refreshAccessToken() {
        $params = [
            'grant_type' => 'refresh_token',
            'client_id' => CONSUMER_KEY,
            'client_secret' => CONSUMER_SECRET,
            'refresh_token' => $this->tokenData['refresh_token'],
        ];

        $ch = curl_init(OAUTH_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);

        if (!isset($data['access_token'])) {
            throw new Exception("アクセストークンの更新に失敗: " . json_encode($data));
        }

        $this->tokenData['access_token'] = $data['access_token'];
        $this->saveToken();
    }



    // アクセス関連＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝

    // SOQL実行
    // 返却値の形式例＝＝＝＝＝＝＝＝＝＝＝＝＝
    // SELECT Id, Name FROM Account
    // 
    // Array(
    // [0] => Array(
    //        [attributes] => Array(
    //                [type] => Account
    //                [url] => /services/data/v60.0/sobjects/Account/0017F00000E9bEnQAJ
    //            )
    //        [Id] => 0017F00000E9bEnQAJ
    //        [Name] => 株式会社テスト堀野
    //    )
    // ＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝
    public function query($soql) {
        $url = $this->tokenData['instance_url'] . SF_API_BASE . "/query?q=" . urlencode($soql);
        $response = $this->sendRequest("GET", $url, null, HTTP_OK);
        $data = json_decode($response['body'], true);
        return isset($data['records']) ? $data['records'] : [];
    }

    public function insert($object, $data) {
        $url = $this->tokenData['instance_url'] . SF_API_BASE . "/sobjects/{$object}/";
        $response = $this->sendRequest("POST", $url, $data, HTTP_CREATED);
        $result = json_decode($response['body'], true);

        if (isset($result['id'])) {
            return $result['id']; // IDだけ返す
        } else {
            throw new Exception("Insert失敗: " . $response['body']);
        }
    }

    public function update($object, $id, $data) {
        $url = $this->tokenData['instance_url'] . SF_API_BASE . "/sobjects/{$object}/{$id}";
        $this->sendRequest("PATCH", $url, $data, HTTP_NO_CONTENT);
        return true;
    }

    public function delete($object, $id) {
        $url = $this->tokenData['instance_url'] . SF_API_BASE . "/sobjects/{$object}/{$id}";
        $this->sendRequest("DELETE", $url, null, HTTP_NO_CONTENT);
        return true;
    }

    // 2,000件以上取得する場合に使う
    public function queryAll($soql) {
        $url = $this->tokenData['instance_url'] . SF_API_BASE . "/query?q=" . urlencode($soql);
        $allRecords = [];

        do {
            $response = $this->sendRequest("GET", $url, null, HTTP_OK);
            $data = json_decode($response['body'], true);

            if (!isset($data['records'])) {
                throw new Exception("レスポンスに'records'が含まれていません: " . $response['body']);
            }

            $allRecords = array_merge($allRecords, $data['records']);

            $url = isset($data['nextRecordsUrl'])
                ? $this->tokenData['instance_url'] . $data['nextRecordsUrl']
                : null;

        } while ($url);

        return $allRecords;
    }




    // アクセス関連の内部関数＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝

    private function sendRequest($method, $url, $data = null, $expectedCode = HTTP_OK, $retry = true) {
        $headers = [
            "Authorization: Bearer " . $this->tokenData['access_token'],
            "Content-Type: application/json",
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code === HTTP_UNAUTHORIZED && $retry) {
            $this->refreshAccessToken();
            return $this->sendRequest($method, $url, $data, $expectedCode, false);  // retry = false
        }

        if ($code !== $expectedCode) {
            throw new Exception("APIエラー: {$code} - {$body}");
        }

        return [
            'code' => $code,
            'body' => $body,
        ];
    }


}
?>
