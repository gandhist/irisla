<?php

namespace Gandhist\Irisla;


class Transactions {
    private $url;
    private $server_key;

    public function __construct(){
        $this->url = env('IRIS_END_POINT');
        $this->server_key = env('IRIS_API_KEY_CREATOR');
    }

    /**
     * fungsi remote curl
     *
     * @param string  $param
     * @param mixed[] $data_hash
     * @param string  $method post get patch
     * @return array 
     */
    public function remoteCall($param, $data_hash, $method){
        $ch = curl_init();
        if($param) {
            $url = $this->url.$param;
        }
        else {
            $url = $this->url;
        }
        $curl_options = array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Basic ' . base64_encode($this->server_key . ':')
            ),
            CURLOPT_RETURNTRANSFER => 1
        );

        switch ($method) {
            case 'post':
                $curl_options[CURLOPT_POST] = 1;
                $body = json_encode($data_hash);
                $curl_options[CURLOPT_POSTFIELDS] = $body;
                break;
            case 'patch':
                $curl_options[CURLOPT_CUSTOMREQUEST] = 'PATCH';
                $body = json_encode($data_hash);
                $curl_options[CURLOPT_POSTFIELDS] = $body;
                break;
            default:
                break;
        }
        curl_setopt_array($ch, $curl_options);
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        if ($result === false) {
            throw new \Exception('CURL Error: ' . curl_error($ch), curl_errno($ch));
        } else {
            $result = json_decode($result, true);
            $data = [
                'url' => $info['url'],
                'http_code' => $info['http_code'],
                'primary_ip' => $info['primary_ip'],
                'local_ip' => $info['local_ip'],
            ];
        }
        return array_merge($data,$result);
    }

        /**
     * GET PAYOUTS DETAILS
     * @param array $data
     * [
     *  "from_date": "2016-08-11",
     *  "to_date": "2016-08-12"
     *  ]
     * @return array object
     * [
     *   {
     *       "account": "Permata virtual account number",
     *       "type": "Topup",
     *       "amount": "300000.00",
     *       "status": "credit",
     *       "created_at": "2016-08-09T17:00:00Z"
     *   },
     *   {
     *       "reference_no": "1e4d9943929504",
     *       "beneficiary_name": "Test Benefeciary",
     *       "beneficiary_account": "7202",
     *       "account": "PT. Bank Central Asia Tbk.",
     *       "type": "Payout",
     *       "amount": "45000.00",
     *       "status": "debit",
     *       "created_at": "2016-08-09T17:00:00Z"
     *   }
     *   ]
     */
    public function history($param){
        $from_date = $param['from_date'];
        $to_date = $param['to_date'];
        $param = "statements?from_date=$from_date&to_date=$to_date";
        return self::remoteCall($param, null, 'get');
    }

    /**
     * GET top up channel
     * @return array object
     * [
     *  {
     *      "id":1,
     *      "virtual_account_type":"mandiri_bill_key",
     *      "virtual_account_number":"991385480006"
     *  },
     *  {
     *      "id":2,
     *      "virtual_account_type":"permata_virtual_account_number",
     *      "virtual_account_number":"8778003756104047"
     *  }
     *  ]
     */
    public function top_up_channel(){
        return self::remoteCall("channels", null, 'get');
    }

    /**
     * get balance aggregator
     */
    public function balance_aggregator(){
        return self::remoteCall('balance', null, 'get');
    }

    /**
     * get bank accounts facilitator
     */
    public function bank_accounts(){
        return self::remoteCall('bank_accounts', null, 'get');
    }

    /**
     * get balance facilitator
     */
    public function balance_facilitator($bank_account_id){
        $param = "balance/$bank_account_id/balance";
        return self::remoteCall($param, null, 'get');
    }


}