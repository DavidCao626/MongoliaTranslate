@extends('layouts.app')

@section('content')

<div class="container">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1>订单 / 详情 #{{ $order->id }}</h1>
            </div>
<p class="bg-warning">翻译类别数字解释：  0:"牌匾印章",
1:"证照公函",
        2:"字词短句",
        3:"人名地名",
        4:"学术论文",
        5:"法律法规",
        6:"标书文件",
        7:"说明书"</p>
        <p class="bg-warning">翻译方式：  1:汉语<->传统蒙文；
2:"汉语<->西里尔蒙文"；
        3:"传统蒙文<->西里尔蒙文",</p>
            <div class="panel-body">
                <div class="well well-sm">
                    <div class="row">
                        <div class="col-md-6">
                            <a class="btn btn-link" href="{{ route('orders.index') }}"><i class="glyphicon glyphicon-backward"></i> Back</a>
                        </div>
                        <div class="col-md-6">
                             <a class="btn btn-sm btn-warning pull-right" href="{{ route('orders.edit', $order->id) }}">
                                <i class="glyphicon glyphicon-edit"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>

                
</p> <label>订单名称</label>
<p>
	{{ $order->name }}
</p> <label>订单号码</label>
<p>
	{{ $order->uuid }}
</p> <label>微信商户内部订单号码</label>
<p>
	{{ $order->wechat_order_id }}
</p> <label>支付微信昵称</label>
<p>
	{{ $order->nickname }}
</p> <label>微信opid</label>
<p>
	{{ $order->opid }}
</p> <label>支付人手机号码</label>
<p>
	{{ $order->phone }}
</p> <label>翻译类别</label>
<p>
	{{ $order->categories }}
</p> <label>翻译方式</label>
<p>
	{{ $order->type }}
</p> <label>Body</label>
<p>
	{{ $order->body }}
</p> <label>翻译字数</label>
<p>
	{{ $order->text_count }}
</p> <label>单价</label>
<p>
	{{ $order->unit_price }}
</p> <label>总价</label>
<p>
	{{ $order->count_price }}
</p> <label>订单状态</label>
<p>
	{{ $order->payment_state }}
</p> <label>Paid_at</label>
<p>
	{{ $order->paid_at }}
</p> <label>是否已经处理</label>
<p>
	{{ $order->is_ok }}
</p>
            </div>
        </div>
    </div>
</div>

@endsection
