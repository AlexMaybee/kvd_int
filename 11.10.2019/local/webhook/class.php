<?
class addWebForm
{

    public function createLead ( $data = [] ) {

        $parts = parse_url($data['URL_UTM']);
        parse_str($parts['query'], $query);

        $queryData = http_build_query(array(
            'fields' => array(
                "EMAIL" =>   array(array("VALUE" => $data['EMAIL'], "VALUE_TYPE" => "WORK")),
                "PHONE" =>   array(array("VALUE" => $data['PHONE'], "VALUE_TYPE" => "WORK")),
                "TITLE" => $data['NAME'].' '.$data['LAST_NAME'],
                "SOURCE_ID" => 'WEB',
                "STATUS_ID" => 'NEW',
                "NAME" => $data['NAME'],
                "LAST_NAME" => $data['LAST_NAME'],
                "SOURCE_DESCRIPTION" => $data['URL_UTM'],
                "UTM_CAMPAIGN" => $query['utm_campaign'],
                "UTM_CONTENT" => $query['utm_content'],
                "UTM_MEDIUM" => $query['utm_medium'],
                "UTM_SOURCE" => $query['utm_source'],
                "UTM_TERM" => $query['utm_term'],
                "UF_CRM_1538485077587" => $data['CITY'],
                "UF_CRM_1539700596" => $data['POST_INDEX'],
                "UF_CRM_1540907735" => $_SERVER['SERVER_ADDR'],
                "UF_CRM_1540907748" => self::getLocationInfoByIp(),
            ),
        ));
        $url = 'crm.lead.add.json';
        $res = self::sendRequestBitrix24($queryData, $url);
        return $res;
    }

    private function getLocationInfoByIp(){
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = @$_SERVER['REMOTE_ADDR'];
        $result  = array('country'=>'', 'city'=>'');
        if(filter_var($client, FILTER_VALIDATE_IP)){
            $ip = $client;
        }elseif(filter_var($forward, FILTER_VALIDATE_IP)){
            $ip = $forward;
        }else{
            $ip = $remote;
        }
        $ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));
        if($ip_data && $ip_data->geoplugin_countryName != null){
            $result['country'] = $ip_data->geoplugin_countryCode;
            $result['city'] = $ip_data->geoplugin_city;
        }
        return $result;
    }

    public function sendRequestBitrix24($queryData, $url)
    {
        $queryUrl = 'https://b24.sherp.ua/rest/1/p3hqgc3foa7uyi4i/' . $url;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $queryUrl,
            CURLOPT_POSTFIELDS => $queryData,
        ));

        $result = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($result, 1);
        return $result;
    }
}
?>