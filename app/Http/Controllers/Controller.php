<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Function for iBAN checker / validation
     * @param string $iban
     * @return array | boolean
     */
    public function getIbanChecker_oldbkp26($iban) {
       
        $key = env("IBANCHECKER_KEY");
        $url = env('IBANCHECKER_URL');

        try {
            $ch = curl_init();
            $curlConfig = array(
                CURLOPT_URL => $url . "?format=json&api_key=" . $key . "&iban=" . $iban,
                CURLOPT_POST => false,
                CURLOPT_RETURNTRANSFER => true,
            );
            curl_setopt_array($ch, $curlConfig);
            $result = curl_exec($ch);
            curl_close($ch);

            $res = json_decode($result, true);
            $set = array();

            if ($res['validations']['chars']['code'] == 006 && $res['validations']['iban']['code'] == 001 && ($res['validations']['account']['code'] == 002 || $res['validations']['account']['code'] == 004) && $res['validations']['structure']['code'] == 005 && $res['validations']['length']['code'] == 003 && $res['validations']['country_support']['code'] == 007) {
                $set['country_iso'] = $res['bank_data']['country_iso'];
                $set['bic'] = $res['bank_data']['bic'];
                $set['bank'] = $res['bank_data']['bank'];
                $set['msg'] = 'IBAN is valid';
                $response = array('responseCode' => 200, 'responseDetails' => $res);
                return $response;
            } else {
                $set['msg'] = 'IBAN is not valid';
                $response = array('responseCode' => 201, 'responseDetails' => $set);
                return $response;
            }
        } catch (\Exception $e) {
            $response = array('responseCode' => 401, 'responseDetails' => $e->getMessage());
            return $response;
        }
    }

}
