<?
namespace Sotbit\Checkcompany;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages ( __FILE__ );

class Curl {
    private $apiKey = '';
    private $secretKey = '';

    public function setApiKey($key){
        $this->apiKey = $key;
    }
    public function setSecretKey($key){
        $this->secretKey = $key;
    }
    public function prepareRequest($curl, $data) {
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Token ' . $this->apiKey,
            'X-Secret: ' . $this->secretKey,
        ));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    }
    public function executeRequest($url, $data) {
        $result = false;
        if ($curl = curl_init($url)) {
            $this->prepareRequest($curl, $data);
            $result = curl_exec($curl);
            $result = json_decode($result, true);
            curl_close($curl);
        }
        return $result['suggestions'];
    }
}
