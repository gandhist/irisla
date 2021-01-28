<?php 

namespace Gandhist\Irisla;
/**
* بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ (Bismillahirrahmanirrahim)
* Dengan menyebut nama Allah yang Maha Pengasih lagi Maha Penyayang 
* Library ini di buat untuk 
* > melakukan transaksi payout ke bank lain menggunakan API secara real-time
* > get daftar bank Indonesia
* > validasi nomor rekening
* crafted by Gandhi Tabrani ¯\_(ツ)_/¯
*/
class Beneficiaries 
{
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
            $url = $this->url."beneficiaries/$param";
        }
        else {
            $url = $this->url."beneficiaries";
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
     * Send GET request
     * 
     * @param mixed[] $data_hash nullable
     */
    public function get($data_hash)
    {
        return self::remoteCall(null, $data_hash, 'get');
    }

    /**
     * fungsi post untuk buat daata penerima baru
     *
     * @param array $data
     * required 
     * ['name' => 'sandor',
     * 'account' => '134254',
     * 'bank' => 'bca',
     * 'alias_name' => 'sandr',
     * 'email' => 'sandor@clegane.com']
     * @return 
     */
    public function post($data_hash)
    {
        return self::remoteCall(null, $data_hash, 'post');
    }

    /**
     * fungsi post untuk buat daata penerima baru
     *
     * @param array $data
     * required 
     * ['name' => 'sandor',
     * 'account' => '134254',
     * 'bank' => 'bca',
     * 'alias_name' => 'sandr',
     * 'email' => 'sandor@clegane.com']
     * @return 
     */
    public function patch($param, $data_hash)
    {
        return self::remoteCall($param, $data_hash, 'patch');
    }
}
