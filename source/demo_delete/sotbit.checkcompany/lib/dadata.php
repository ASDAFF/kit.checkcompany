<?
namespace Sotbit\Checkcompany;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages ( __FILE__ );

class Dadata extends Curl {

    private $module = 'sotbit.checkcompany';
    private $party = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/party";
    public function __construct() {
        $this->setApiKey(\COption::GetOptionString($this->module, "API_KEY"));
        $this->setSecretKey(\COption::GetOptionString($this->module, "SECRET_KEY"));
    }
    public function send($query) {
        $data = $this->executeRequest($this->party, array('query'=>$query));
        $result = array();
        foreach ($data as $item) {
            $result[] = array(
                'company_name' => $item['value'],
                'inn' => $item['data']['inn'],
                'kpp' => $item['data']['kpp'],
                'ogrn' => $item['data']['ogrn'],
                'address' => $item['data']['address']['value'],
                'name' => $item['data']['management']['name']
            );
        }
        return $result;
    }
}