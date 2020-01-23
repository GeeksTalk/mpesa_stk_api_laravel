<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use SmoDav\Mpesa\Laravel\Facades\STK;

class MpesaController extends Controller
{
    public function initSTK(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'integer'],
            'phone_no' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!$this->verifySafaricomPhoneNo($value)) {
                    return $fail(__('Only Safaricom numbers are supported for payments'));
                }
            }],
        ]);
        $uniqueCode = $this->random_number_string(5);
        $phoneNo = '254' . substr($request->phone_no, -9);
        $mpesa_response = STK::request(intval($request->amount))
            ->from($phoneNo)
            ->usingReference(env('APP_NAME'),$uniqueCode)
            ->push();

        return $this->handleSTKrequestResp($mpesa_response);
    }

    private function verifySafaricomPhoneNo($phone)
    {
        $phone_no = '0'.substr($phone, -9);
        if (!preg_match('/^(0){1}[7]{1}([0-2]{1}[0-9]{1}|[9,4]{1}[0-9]{1})[0-9]{6}/',$phone_no))
        {
            return false;
        }
        else{
            return true;
        }
    }

    private function random_number_string($length)
    {
        $faker = Str::random($length);
        return $faker;
    }

    private function handleSTKrequestResp($mpesa_response)
    {
        if($mpesa_response){
            $payload = json_decode(json_encode($mpesa_response));
//            dd($payload);
//            Log::info(json_encode($payload));
            if (Arr::has($payload,'ResponseCode')) {
                if ($payload->ResponseCode == 0) {
                    return response()->json([
                        'data' => $payload,
                        'message' => 'Enter your Mpesa PIN to complete the order'],200);
                } else{
                    return response()->json(['message' => 'Error Occurred while initiating payment, try again'], 400);
                }
            }else{
                return response()->json(['message' => 'Error Occurred while initiating payment, try again'], 400);
            }
        }
        return response()->json(['message' => 'Error Occurred while initiating payment, try again'], 400);
    }

    public function receiveMpesaCallback()
    {
        $payload =  file_get_contents('php://input');
        if(!$payload){
            Log::error('PAYMENT >> MPESA: No Body sent');
            return response('ERROR: NO REQUEST');
        }
        //the body
        Log::info('BODY >>>>> '. $payload);

        $result = json_decode($payload);
        if($result->Body->stkCallback->ResultCode == 0) {
            return response('SUCCESS: PAYMENT MADE ');
        }
        return response('ERROR: NO PAYMENT MADE !');
    }
}
