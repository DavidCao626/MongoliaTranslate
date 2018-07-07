<?php

namespace App\Http\Controllers;

use Log;
use GuzzleHttp\Client;
use EasyWeChat;

class WeChatController extends Controller
{
    private $api_url= "http://47.94.8.17:8124";

    private function getBody($body)
    {
        $client = new Client();

        $requests = $client->request('GET', $this->api_url, [
            'query' => ['body' => $body]
        ])->getbody()->getContents();
        return json_decode($requests, true)["body"];
    }
    public function addmenu()
    {
        $officialAccount = EasyWeChat::officialAccount();

        $buttons = [
            [
                "type" => "view",
                "name" => "慈善捐款",
                "url"  => "http://www.shop976.com/cishan/index.html"
            ],
            [
                "name"       => "汉蒙翻译",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "免费翻译系统",
                        "url"  => "http://wx.shop976.com/home/"
                    ],
                    [
                        "type" => "view",
                        "name" => "汉语->新蒙文",
                        "url"  => "http://wx.shop976.com/home/?tpl=fy&mod=nmw"
                    ],
                    [
                        "type" => "view",
                        "name" => "Кирилл->汉",
                        "url" => "http://wx.shop976.com/home/?tpl=fy&mod=wmg"
                    ]
                ]
            ],
            [
                "name"=> "Aya tukh",
                "sub_button" => [
                    [
                        "type"=> "view",
                        "name"=> "Tsag agaar",
                        "url" => "http://www.shop976.com/Games/tianqi/index.php"
                    ],
                    [
                        "type" => "view",
                        "name" => "Кирилл->汉",
                        "url"  => "http://wx.shop976.com/home/?tpl=fy&mod=wmg"
                    ],
                    [
                        "type" => "view",
                        "name" => "daily horoscope",
                        "url" => "http://www.shop976.com/Games/zurhai.php"
                    ]
                ]
            ]
        ];
        return  $officialAccount->menu->create($buttons);
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
                    return  '欢迎关注我，智能语音(汉语-新蒙文)翻译系统上线试运行中。您可以试着对我说一句普通话或者发一段文字，我将尝试为您翻译😄';
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
                    return '';
                    break;
                case 'location':
                    return '';
                    break;
                case 'link':
                    return '';
                    break;
                case 'file':
                    return '';
                // ... 其它消息
                default:
                    return '';
                    break;
            }
        });
        return $app->server->serve();
    }
}
