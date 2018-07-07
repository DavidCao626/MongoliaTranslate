@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div>
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

        
        
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1>
                    <i class="glyphicon glyphicon-align-justify"></i> 所有订单
                    {{-- <a class="btn btn-success pull-right" href="{{ route('orders.create') }}"><i class="glyphicon glyphicon-plus"></i> Create</a> --}}
                    
                </h1>
            </div>
            
        <br/>
            <div class="panel-body">
                @if($orders->count())
                
                    <table class="table table-condensed table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>订单名称</th> <th>订单号</th> <th>微信商户订单</th> <th>微信名称</th><th>手机号码</th> <th>翻译类别</th> 
                                <th>翻译方式</th> <th>翻译内容</th> <th>翻译字数</th> 
                                <th>价格</th> <th>支付状态</th>  <th>支付成功时间</th>  <th>是否翻译完成</th>
                                <th class="text-right">操作</th>
                            </tr>
                        </thead>

                        <tbody>
                            @if ($orders )
                                
                            @foreach($orders as $order)
                                <tr>

                                    <td>{{$order->name}}</td> <td>{{$order->uuid}}</td> <td>{{$order->wechat_order_id}}</td> 
                                    <td>{{$order->nickname}}</td>
                                     <td>{{$order->phone}}</td> <td>{{$order->categories}}</td> 
                                     <td>{{$order->type}}</td> <td>{{$order->body}}</td> <td>{{$order->text_count}}</td> <td>{{$order->unit_price}}</td> <td>{{$order->count_price}}</td> <td>{{$order->payment_state}}</td> <td>{{$order->paid_at}}</td> <td>{{$order->is_ok}}</td>
                                    
                                    <td class="text-right">
                                        <a class="btn btn-xs btn-primary" href="{{ route('orders.show', $order->id) }}">
                                            <i class="glyphicon glyphicon-eye-open"></i> 
                                        </a>
                                        
                                        <a class="btn btn-xs btn-warning" href="{{ route('orders.edit', $order->id) }}">
                                            <i class="glyphicon glyphicon-edit"></i> 
                                        </a>

                                        <form action="{{ route('orders.destroy', $order->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete? Are you sure?');">
                                            {{csrf_field()}}
                                            <input type="hidden" name="_method" value="DELETE">

                                            <button type="submit" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach

                            @endif
                        </tbody>
                    </table>
                    {!! $orders->render() !!}
                @else
                    <h3 class="text-center alert alert-info">Empty!</h3>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection