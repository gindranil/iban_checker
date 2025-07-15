<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IbanCheckerController extends Controller
{
    public function checkForValidIban(Request $request){

        $iban = !empty($request->iban) ? $request->iban : '';

        $messages = [
            'iban.required' => 'The :attribute field is required',
        ];

        $validator = \Validator::make($request->all(), [
                    'iban' => 'required',
                        ], $messages);

        if ($validator->fails()) {
            $errMsg = $validator->errors();
            try {
                $response = array('responseCode' => 108, 'responseDetails' => 'Request validation', 'responseData' =>$errMsg);
                return $response;
            } catch (\EncryptException $e) {
                $response = array('responseCode' => 108, 'responseDetails' => $e->getMessage());
                return $response;
            }
        }

        $iban = !empty($request->iban) ? $request->iban : '';

        try {
            $getDataFromApi = $this->getIbanChecker($iban);

            $bicToCheck = !empty($getDataFromApi['responseDetails']['bank_data']['bic']) ? $getDataFromApi['responseDetails']['bank_data']['bic'] : '';

            if(empty($bicToCheck)) {
                $response = array('responseCode' => 400, 'responseDetails' => 'IBAN Checker', 'responseData' => $getDataFromApi);
            }
            else
            {
                $response = array('responseCode' => 200, 'responseDetails' => 'IBAN Checker', 'responseData' => $getDataFromApi);
            }
            return $response;
       } catch (\EncryptException $e) {
            $response = array('responseCode' => 401, 'responseDetails' => $e->getMessage());
            return $response;
       }
    }
}
