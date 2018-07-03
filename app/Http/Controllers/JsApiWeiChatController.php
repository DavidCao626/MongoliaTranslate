<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Log;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;
use Illuminate\Http\Request as httpRequest;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

use EasyWeChat\Factory;

class JsApiWeiChatController extends Controller
{
    public function BillOrder()
    {
        //$app = new Application(config('wechat.payment'));
        $wechat = app('wechat');
        $payment = $wechat->payment;
        //dd( $app );
        $bill = $payment->downloadBill('20161107'); // type: ALL
        //dd($bill);
        // or
        // $bill = $payment->downloadBill('20140603', 'SUCCESS'); // type: SUCCESS
        // bill 为 csv 格式的内容
        // 保存为文件
        echo(file_put_contents('bill-20161107.csv', $bill));
    }

    /*
     * 创建订单
     * Davidcao626@foxmail.com
     */
    public function OrderCreate(httpRequest $request)
    {
        Log::info("加载配置：".serialize(config('wechat.payment')));
        Log::info("开始生成订单：JsApiWeiChatController---->OrderCreate");
        
        $openId = $request->input('openid');  //获取opid
        $type=$request->type;
        if ($openId==null||$openId=="") {
            Log::error("openId获取失败");
            abort(401, '获取您的微信身份信息失败，请您关闭当前微信，重新打开进行续费.');
            //return "获取您的微信身份信息失败,请您关闭当前微信，重新打开进行续费.";
        }
        if ($type==null||$type=="") {
            Log::error("type获取失败");
            abort(401, '获取您的微信身份信息失败，请您关闭当前微信，重新打开进行续费.');
            //	return "获取您的微信身份信息失败,请您关闭当前微信，重新打开进行续费.";
        }
        
        
        $cname = "蒙古问翻译费用";//商品描述

        $merchant_id = config('wechat.payment.merchant_id');
        $out_orderid =$merchant_id.date("YmdHis").mt_rand(101, 999); //生成订单号

        /*
         * 获取续费价格
         */
        $total_fee=0;
        if ($request->input('yearfee')==1) {
            $total_fee=config('wechat.AppConfig.yearfee1');
        }
        if ($request->input('yearfee')==2) {
            $total_fee=config('wechat.AppConfig.yearfee2');
        }
        if ($request->input('yearfee')==3) {
            $total_fee=config('wechat.AppConfig.yearfee3');
        }
        if ($request->input('yearfee')==0) {
            $total_fee=1;
        }
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
            'total_fee'        => $total_fee,
            'attach'           => '1',
            'time_start'       =>date("YmdHis"),
            'time_expire'       =>date("YmdHis", time() + 600),


        ];
        //  Log::info('[创建订单]:'.serialize($attributes));

        $app = app('wechat');

        $payment = $app->payment;
        $order = new Order($attributes);//拼装订单
        $result = $payment->prepare($order);//通过微信接口生成订单
        //file_put_contents('order.log', $result);
        //dd(   $result );
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS') {
            $prepayId = $result->prepay_id;
            //生产那个订单后的逻辑
            Log::info('[生成订单'.$prepayId.'信息]：'.serialize($result));

            //2016.10.11添加用户订单记录信息
            $CofeeInfoJL =new \App\Http\Model\CofeeInfoJL;
            $CofeeInfoJL->openid = $request->openid;
            $CofeeInfoJL->copname_name = $request->copname_name==''?null:$request->copname_name;//公司全称
            $CofeeInfoJL->type = $request->type;
            $CofeeInfoJL->pk = $request->pk;
            $CofeeInfoJL->deadline = $request->deadline;
            $CofeeInfoJL->copname = $request->copname;
            $CofeeInfoJL->cname = $request->cname;
            $CofeeInfoJL->phone = $request->phone;
            $CofeeInfoJL->yearfee = $request->yearfee;
            $CofeeInfoJL->s3 = $request->s3;
            $CofeeInfoJL->area = $request->s4;
            $CofeeInfoJL->detailinfo = $request->detailinfo;
            $CofeeInfoJL->f9904 = $request->nsrbh;
            $CofeeInfoJL->f9905 =$request->zzjgdm==''?null:$request->zzjgdm;
            $CofeeInfoJL->f9907 = $request->dwbm==''?null:$request->dwbm;
            $CofeeInfoJL->out_trade_no = $attributes['out_trade_no'];
            $CofeeInfoJL->total_fee = $total_fee;
            $CofeeInfoJL->time_start = $attributes['time_start'];
            $CofeeInfoJL->time_expire =  $attributes['time_expire'];
            $CofeeInfoJL->status=0;//订单生成 未支付
            $CofeeInfoJL->save();
            //dd($s);

            $json = $payment->configForPayment($prepayId); // 返回 json 字符串，如果想返回数组，传第二个参数 false

            return view('JsApiWeiChat', [
                'json' => $json,'out_orderid' => $out_orderid,'openId' => $openId,'pk' => $request->pk,'copname' => $request->copname,'nsrbh' => $request->nsrbh
                ,'zzjgdm' => $request->zzjgdm,'dwbm' => $request->dwbm,'deadline' => $request->deadline,'yearfee' => $request->yearfee,'cname' => $request->cname,'phone' => $request->phone,'s3' =>  $request->s3,'s4' => $request->s4,'detailinfo' => $request->detailinfo,'type' => $request->type
            ]);
        } else {
            //生成订单失败
            Log::error('[生成微信订单失败]:'.$result);
            return '生成微信订单失败!请重新缴费！';
        }
    }

    /*
     * 支付成功后的回调函数
     * Davidcao626@foxmail.com
     */
    public function OrderNotify()
    {
        //$app = app('wechat');
        $app = Factory::officialAccount(config('wechat.payment'));

        dd($app);
        $response = $app->payment->handleNotify(function ($notify, $successful) {
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            Log::Debug('开始处理微信订单回调...'.serialize($notify));
            $CofeeInfoJL = \App\Http\Model\CofeeInfoJL::where(['out_trade_no'=>$notify->out_trade_no])->first();

            if (!$CofeeInfoJL) { // 如果订单不存在
                Log::warning('订单丢失了!没找到...');
                return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }


            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = 查询订单($message['out_trade_no']);

            if (!$order || $order->paid_at) { // 如果订单不存在 或者 订单已经支付过了
                return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }

            ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////

            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if (array_get($message, 'result_code') === 'SUCCESS') {
                    $order->paid_at = time(); // 更新支付时间为当前时间
                    $order->status = 'paid';

                // 用户支付失败
                } elseif (array_get($message, 'result_code') === 'FAIL') {
                    $order->status = 'paid_fail';
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
    /*
 * 将订单记录表的成功支付信息添加到 成功支付表中
 */
    public function OrderJLToOrderInfo($notify)
    {
        $CofeeInfoJL = \App\Http\Model\CofeeInfoJL::where('out_trade_no', $notify->out_trade_no)->where('status', 0) ->first();
        $CofeeInfoJL->paid_at = $notify->time_end; // 更新支付时间为微信服务器支付成功
        $CofeeInfoJL->status = 1;//已经支付
        $CofeeInfoJL->save();
        //dd( $CofeeInfoJL );
        if ($CofeeInfoJL) {
            $CofeeInfo = new \App\Http\Model\CofeeInfo;
            $CofeeInfo->openid = $CofeeInfoJL->openid;
            $CofeeInfo->copname_name =$CofeeInfoJL->copname_name;
            $CofeeInfo->pk = $CofeeInfoJL->pk;
            $CofeeInfo->order_id = $CofeeInfoJL->out_trade_no;
            $CofeeInfo->copname = $CofeeInfoJL->copname;
            $CofeeInfo->f9904 = $CofeeInfoJL->f9904;
            $CofeeInfo->f9905 =$CofeeInfoJL->f9905==null?'':$CofeeInfoJL->f9905;
            $CofeeInfo->f9907 =$CofeeInfoJL->f9907==null?'':$CofeeInfoJL->f9907;
            $CofeeInfo->deadline = $CofeeInfoJL->deadline;
            $CofeeInfo->cname = $CofeeInfoJL->cname;
            $CofeeInfo->phone = $CofeeInfoJL->phone;
            $CofeeInfo->yearfee = $CofeeInfoJL->yearfee;
            $CofeeInfo->city = $CofeeInfoJL->s3;
            $CofeeInfo->area = $CofeeInfoJL->area;
            $CofeeInfo->flag = $CofeeInfoJL->status;
            $CofeeInfo->street = $CofeeInfoJL->detailinfo;
            $CofeeInfo->type = $CofeeInfoJL->type;
            $CofeeInfo->ems = '';
            session($CofeeInfo->order_id, 'yes');
            if ($CofeeInfo->save()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    public function GetOrderForNotify($notify)
    {
        //将订单记录表的成功支付信息添加到 成功支付表中
        if ($this->OrderJLToOrderInfo($notify)) {
            //Log::info(""$notify->out_trade_no);
            //更新日期算法
            $CofeeInfo = \App\Http\Model\CofeeInfo::where('order_id', $notify->out_trade_no)->where('flag', 1) ->first();
            if ($CofeeInfo) {
                $deadline = $CofeeInfo['deadline'];
                $newdate = date("Y-m-d", strtotime($deadline." +  ".$CofeeInfo['yearfee']." year "));
                $CofeeInfo->deadline=$newdate;
                Log::info("新续费日期".$newdate);
                if ($CofeeInfo->save()!=null) {
                    if ($this->HttpForUpdateCert($CofeeInfo)) {
                        return  $this->HttpInsertAmountInfo($CofeeInfo);
                    }
                }
            }
        }
        Log::info("订单同步更新失败");
        return false;
    }
    public function HttpInsertAmountInfo($CofeeInfo)
    {
        $client = new Client(['base_uri' => 'http://58.18.170.116:8888', 'timeout'  => 12]);
        if ($CofeeInfo["yearfee"]==1) {
            $cfee=200;
        } elseif ($CofeeInfo["yearfee"]==2) {
            $cfee=400;
        } elseif ($CofeeInfo["yearfee"]==3) {
            $cfee=500;
        } elseif ($CofeeInfo["yearfee"]==0) {
            $cfee=8888;
            $CofeeInfo["deadline"]="2018-11-30";
        }
        $str1=mb_convert_encoding('缴费更新', 'utf-8', 'GBK,UTF-8,ASCII');
        $str2=mb_convert_encoding('微信平台', 'utf-8', 'GBK,UTF-8,ASCII');

        $url='http://58.18.170.116:8888/service/CxfService/UserInfo/UserInfoService/insertAmountInfo?paymentType='.$str1.'&paymentSource='.$str2.'&amount='.$cfee.'&newDate='.urlencode($CofeeInfo["deadline"]).'&updateTime='.urlencode(date('Y-m-d H:i:s', time())).'&pk='.$CofeeInfo["pk"];
        Log::info("HttpInsertAmountInfo请求开始".$url);

        $response = $client->request('GET', $url, [
            'headers'=>[
                'userName'=>'nmgcaSKPT',
                'userPwd'=>'nmgcaUser!@#$%',
                'content-type'=>'application/x-www-form-urlencoded',
                'charset'=>'UTF-8'
            ]
        ])->getBody();
        Log::info('CAxf 插入用户续费response:'.strval($response));
        $committed1=json_decode(strval($response), true);
        if ($committed1['flag']==1) {
            Log::info('CAxf 用户续费：插入信息成功');
            return true;
        } else {
            Log::error('CAxf 用户续费：插入信息失败');
            return false;
        }
    }

    public function HttpForUpdateCert($CofeeInfo)
    {
        $client = new Client(['base_uri' => 'http://58.18.170.116:8888', 'timeout'  => 12]);
        $url='http://58.18.170.116:8888/service/CxfService/UserInfo/UserInfoService/updateCert?PK='.$CofeeInfo["pk"].'&newDate='.$CofeeInfo["deadline"];


        Log::info("HttpForUpdateCert请求开始".$url);
        $response= $client->request('GET', $url, [
            'headers'=>[
                'userName'=>'nmgcaSKPT',
                'userPwd'=>'nmgcaUser!@#$%',
                'content-type'=>'application/x-www-form-urlencoded',
                'charset'=>'UTF-8'
            ]
        ])->getBody();
        Log::info('CAxf 更新用户续费response:'.strval($response));
        if (json_decode(strval($response), true)['flag']==1) {
            Log::info('CAxf 用户续费：更新信息成功');
            return true;
        } else {
            Log::error('CAxf用户续费：更新信息失败');
            return false;
        }
    }
}
