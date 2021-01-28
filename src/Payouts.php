<?php

namespace Gandhist\Irisla;


class Payouts {
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
        $server_key = $this->server_key;
        $url = $this->url."payouts";
        if($param) {
            $server_key = env('IRIS_API_KEY_APPROVER');
            $url = $this->url."payouts/$param";
        }
        $curl_options = array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Basic ' . base64_encode($server_key . ':')
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
     * fungsi post untuk buat daata penerima baru
     *
     * @param array $data
     * required 
     * {
     *   "payouts": [
     *       {
     *       "beneficiary_name": "Jon Snow",
     *       "beneficiary_account": "1172993826",
     *       "beneficiary_bank": "bni",
     *       "beneficiary_email": "beneficiary@example.com",
     *       "amount": "100000.00",
     *       "notes": "Payout April 17"
     *       },
     *       {
     *       "beneficiary_name": "John Doe",
     *       "beneficiary_account": "112673910288",
     *       "beneficiary_bank": "mandiri",
     *       "amount": "50000.00",
     *       "notes": "Payout May 17"
     *       }
     *   ]
     *   }
     * @return 
     * {
     * "payouts": [
     *     {
     *     "status": "queued",
     *     "reference_no": "1d4f8423393005"
     *     },
     *     {
     *     "status": "queued",
     *     "reference_no": "10438f2b393005"
     *     }
     *   ]
     * }
     */
    public function create($data_hash)
    {
        return self::remoteCall(null, $data_hash, 'post');
    }

    /**
     * Undocumented function
     *
     * @param array $data
     * {
     *   "reference_nos": ["10438f2b393005", "1d4f8423393005", "1d2e1123425937"],
     *   "otp": "335163" optional (based on config)
     *   } 
     * @return array
     * {
     *   "status": "ok"
     *   }
     */
    public function approve($data_hash){
        return self::remoteCall('approve', $data_hash, 'post');
    }

    /**
     * Reject payout
     * @param array $data
     * {
     *   "reference_nos": ["10438f2b393005", "1d4f8423393005", "1d2e1123425937"],
     *   "reject_reason": "Reason to reject payouts"
     *   }
     * @return void $data
     * {
     *  "status": "ok"
     *  }
     */
    public function reject($data_hash){
        return self::remoteCall('reject', $data_hash, 'post');
    }

    /**
     * details payout
     * @param string $reference_no
     * @return void $data
     * {
     *  "amount": "200000.00",
     *  "beneficiary_name": "Ryan Renolds",
     *  "beneficiary_account": "33287352",
     *  "bank": "Bank Central Asia ( BCA )",
     *  "reference_no": "83hgf882",
     *  "notes": "Payout June 17",
     *  "beneficiary_email": "beneficiary@example.com",
     *  "status": "queued",
     *  "created_by": "John Doe",
     *  "created_at": "2017-01-11T00:00:00Z",
     *  "updated_at": "2017-01-11T00:00:00Z"
     *  }
     */
    public function details($reference_no){
        return self::remoteCall($reference_no, null, 'get');
    }


}