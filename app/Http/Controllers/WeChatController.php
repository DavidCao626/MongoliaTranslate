<?php

namespace App\Http\Controllers;

use Log;

class WeChatController extends Controller
{

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
            return '收到事件消息';
            break;
        case 'text':
            return '收到文字消息';

            break;
        case 'image':
            return '对不起，我暂时不能识别图片内容。';
            break;
        case 'voice':
            return $message["Recognition"];
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
