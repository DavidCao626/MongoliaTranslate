<?php

namespace App\Handlers;


use Overtrue\EasySms\EasySms;

class SmsSendsHandler
{
    public function sends($phone, $body)
    {

        try {
            $result = $easySms->send($phone, [
                'data' => "【呼市人社局】" . $body
            ]);
        } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
            $message = $exception->getException('submail')->getMessage();
            return [
                'isok'=> '0',
                'message'=>$message
            ];
           
            
        }


        return [
            'isok'=>'1',
            'message' => $phone
        ];
    }
}