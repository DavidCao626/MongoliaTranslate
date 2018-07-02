<?php

namespace App\Http\Controllers;

use Log;
use GuzzleHttp\Client;

class WeChatController extends Controller
{
    private $api_url= "http://47.94.8.17:8124";

    private function getBody($body)
    {
        $client = new Client();

        $requests = $client->request('GET', $this->api_url, [
            'query' => ['body' => $body]
        ])->getbody()->getContents();
        return json_decode($requests,true)["body"];
    }

    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
        Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志
        $app = app('wechat.official_account');

        $app->server->push(function ($message) {
            switch ($message['MsgType']) {
        case 'event':
            return  '欢迎关注我，智能语音翻译系统上线试运行中。您可以试着对我说一句普通话或者发一段文字，我将尝试为您翻译😄';
            break;
        case 'text':
            return '翻译结果(新蒙文)：'.$this->getBody($message["Content"]);
           // return  '欢迎关注我，智能语音翻译系统上线试运行中。您可以试着对我说一句话普通话，我将尝试为您翻译😄';


            break;
        case 'image':
            return '对不起，我暂时不能识别图片内容。';
            break;
        case 'voice':
            return '翻译结果(新蒙文)：'.$this->getBody($message["Recognition"]);
            break;
        case 'video':
            return '收到视频消息';
            break;
        case 'location':
            return '收到坐标消息';
            break;
        case 'link':
            return '收到链接消息';
            break;
        case 'file':
            return '收到文件消息';
        // ... 其它消息
        default:
            return '收到其它消息';
            break;
    }
        });
        return $app->server->serve();
    }
}
