<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Log;
use Illuminate\Http\Request as httpRequest;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

use EasyWeChat;
use App\Http\Models;
use Overtrue\EasySms\EasySms;

class JsApiWeiChatController extends Controller
{
    public function sendsms($order,$username,$tel)
    {
        $config = [
            'timeout' => 5.0,
            'default' => [
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,
                'gateways' => [
                    'yunpian'
                ],
            ],
            'gateways' => [
                'yunpian' => [
                    'api_key' => 'f9d20eb8eeaada21df807bf06512dcfe',
                ]
            ],
        ];

        $easySms = new EasySms($config);

//14747339706
        try {
            $easySms->send(15034933020, [
                'content' => '【蒙古颂】新翻译".$name."元订单，用户名：".$username."，手机号：".$tel." , 请尽快联系。',
                'template' => '2383958',
                'data' => [
                    'code' => 6379
                ],
            ]);
        } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
            $message = $exception->getException('yunpian')->getMessage();
            dd($message);
        }
    }
    /*
     * 创建订单
     * Davidcao626@foxmail.com
     */
    public function OrderCreate(httpRequest $request)
    {
       

        // 已经登录过
        $user = session('wechat.oauth_user');
        $openId = $user['default']->id;  //获取opid获取opid
        

        $type=$request->phone;
        $p=$request->count_price/30 ;
       
        if ($openId==null||$openId=="") {
            Log::error("openId获取失败");
            abort(401, '获取您的微信身份信息失败，请您关闭当前微信，重新打开进行续费.');
        }
        if ($p==null||$p=="") {
            Log::error("支付失败");
            abort(401, '支付失败.');
        }

        if ($type==null||$type=="") {
            Log::error("手机号码错误");
            abort(401, '手机号码错误.');
        }
        
        
        $cname = "[". $p/100 ."元]翻译订单[来自微信名称：". $user['default']->nickname ."，手机号码：". $type;//商品描述

        $merchant_id = config('wechat.payment.default.mch_id');
        $out_orderid =$merchant_id.date("YmdHis").mt_rand(101, 999); //生成订单号

        
        //设置seesion状态
        if (session($out_orderid)=="") {
            session($out_orderid, "no");
        }
        /*
         * 创建订单
         */
        $attributes = [
            'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
            'openid'           => $openId,
            'body'             => $cname,
            'out_trade_no'     => $out_orderid,
            'total_fee'        => $p,
            'time_start'       =>date("YmdHis"),
            'time_expire'       =>date("YmdHis", time() + 600),


        ];
        //  Log::info('[创建订单]:'.serialize($attributes));

        $app = EasyWeChat::payment();
        

        $result = $app->order->unify($attributes);

        //file_put_contents('order.log', $result);
        //dd($result);
        if ($result['return_code'] == 'SUCCESS' && $result['return_msg'] == 'OK') {
            $prepayId = $result['prepay_id'];
            //生产那个订单后的逻辑
            Log::info('[生成订单'.$prepayId.'信息]：'.serialize($result));

            //2016.10.11添加用户订单记录信息
            $Order =new \App\Models\Order;
            $Order->opid = $openId;
            $Order->wechat_order_id = $prepayId;
            $Order->uuid = $out_orderid;
            $Order->nickname = $user['default']->nickname;
            $Order->categories = $request->categories;
            $Order->type = $request->type;
            $Order->name = $cname;
            $Order->phone = $type;
            $Order->body = $request->body;
            $Order->text_count = $request->text_count;
            $Order->unit_price = $request->unit_price;
          
            //$order->paid_at = date("Y-m-d H:i:s");
            $Order->count_price = $p;
            $Order->payment_state=0;//订单生成 未支付
           
            $Order->save();
            //dd($s);
            $app = EasyWeChat::payment();

            $json =  $app->jssdk->bridgeConfig($prepayId); // 返回 json 字符串，如果想返回数组，传第二个参数 false
            session([$out_orderid => $json]);
           
            // 返回 json 字符串，如果想返回数组，传第二个参数 false
            // 返回 json 字符串，如果想返回数组，传第二个参数 false
           
            return response()->json([
                'message' => 'ok',
                'body'=> $out_orderid,
                'state' => '1'
            ]);
        } else {
            //生成订单失败
            Log::error('[生成微信订单失败]:'.$result);
            return response()->json([
                'message' => '生成微信订单失败!请重新提交！',
                'state' => '0'
            ]);
        }
    }
    public function wechatJsapi(httpRequest $request)
    {
        // dd($request->orderID);
        if (!$request->orderID) {
            Log::error("orderID");
            abort(401, '获取订单信息失败，请您关闭当前微信，重新打开进行续费.');
        }
        
        $user = $request->session()->get('wechat.oauth_user');
        $openId = $user['default']->id;
        $orderID= $request->orderID;
        $order = \App\Models\Order::where(['uuid' => $orderID])->first();

        if (!$order) { // 如果订单不存在
            Log::warning('订单丢失了!没找到...');
            return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
        }
        return view('JsApiWeiChat', ['order'=> $order,'out_orderid' => $orderID, 'openId' => $openId, 'json' => session($orderID)]);
    }



    /*
     * 支付成功后的回调函数
     * Davidcao626@foxmail.com
     */
    public function OrderNotify()
    {
        $app = EasyWeChat::payment();

      
        $response = $app->handlePaidNotify(function ($message, $fail) {
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            Log::info('开始处理微信订单回调...'.serialize($message));
            
            $order = \App\Models\Order::where(['uuid'=>$message['out_trade_no']])->first();

            if (!$order) { // 如果订单不存在
                Log::warning('订单丢失了!没找到...');
                return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }


            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            // $order = $app->order->queryByOutTradeNumber($message['out_trade_no']);

            if (!$order || $order->payment_state==1) { // 如果订单不存在 或者 订单已经支付过了
                return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }

            ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////

            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if (array_get($message, 'result_code') === 'SUCCESS') {
                    $order->paid_at = date("Y-m-d H:i:s"); // 更新支付时间为当前时间
                    $order->payment_state = 1;
                    $this->sendsms($order->count_price, $order->nickname, $order->phone);
                // 用户支付失败
                } elseif (array_get($message, 'result_code') === 'FAIL') {
                    $order->status = 2;
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }

            $order->save(); // 保存订单

            return true; // 返回处理完成
        });
        Log::Debug('订单所有回调处理结束...'.$response);
        return $response;
    }
}
