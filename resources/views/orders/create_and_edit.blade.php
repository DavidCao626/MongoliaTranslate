@extends('layouts.app')

@section('content')

<div class="container">
    <div class="col-md-10 col-md-offset-1">
         
        <div class="panel panel-default">
            
            <div class="panel-heading">
                <h1>
                    <i class="glyphicon glyphicon-edit"></i> Order /
                    @if($order->id)
                        Edit #{{$order->id}}
                    @else
                        Create
                    @endif
                </h1>
            </div>

            @include('common.error')

            <div class="panel-body">
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
                @if($order->id)
                    <form action="{{ route('orders.update', $order->id) }}" method="POST" accept-charset="UTF-8">
                        <input type="hidden" name="_method" value="PUT">
                @else
                    <form action="{{ route('orders.store') }}" method="POST" accept-charset="UTF-8">
                @endif

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    
                <div class="form-group">
                    <label for="=id-field">id</label>
                    <input class="form-control" type="text" name="=id" id="=id-field" value="{{ old('id', $order->id ) }}" />
                </div> 
                <div class="form-group">
                    <label for="name-field">订单名称</label>
                    <input class="form-control" type="text" name="name" id="name-field" value="{{ old('name', $order->name ) }}" />
                </div> 
                <div class="form-group">
                	<label for="uuid-field">订单号码</label>
                	<input class="form-control" type="text" name="uuid" id="uuid-field" value="{{ old('uuid', $order->uuid ) }}" />
                </div> 
                <div class="form-group">
                	<label for="wechat_order_id-field">微信商户订单号码</label>
                	<input class="form-control" type="text" name="wechat_order_id" id="wechat_order_id-field" value="{{ old('wechat_order_id', $order->wechat_order_id ) }}" />
                </div> 
                <div class="form-group">
                	<label for="nickname-field">微信昵称</label>
                	<input class="form-control" type="text" name="nickname" id="nickname-field" value="{{ old('nickname', $order->nickname ) }}" />
                </div> 
                <div class="form-group">
                	<label for="opid-field">微信opid</label>
                	<input class="form-control" type="text" name="opid" id="opid-field" value="{{ old('opid', $order->opid ) }}" />
                </div> 
                <div class="form-group">
                	<label for="phone-field">支付人手机号码</label>
                	<input class="form-control" type="text" name="phone" id="phone-field" value="{{ old('phone', $order->phone ) }}" />
                </div> 
                <div class="form-group">
                    <label for="categories-field">翻译分类</label>
                    <input class="form-control" type="text" name="categories" id="categories-field" value="{{ old('categories', $order->categories ) }}" />
                </div> 
                <div class="form-group">
                	<label for="type-field">翻译方式</label>
                	<textarea name="type" id="type-field" class="form-control" rows="3">{{ old('type', $order->type ) }}</textarea>
                </div> 
                <div class="form-group">
                	<label for="body-field">翻译内容</label>
                	<input class="form-control" type="text" name="body" id="body-field" value="{{ old('body', $order->body ) }}" />
                </div> 
                <div class="form-group">
                	<label for="text_count-field">翻译字数</label>
                	<input class="form-control" type="text" name="text_count" id="text_count-field" value="{{ old('text_count', $order->text_count ) }}" />
                </div> 
                <div class="form-group">
                    <label for="unit_price-field">单价</label>
                    <input class="form-control" type="text" name="unit_price" id="unit_price-field" value="{{ old('unit_price', $order->unit_price ) }}" />
                </div> 
                <div class="form-group">
                    <label for="count_price-field">总价</label>
                    <input class="form-control" type="text" name="count_price" id="count_price-field" value="{{ old('count_price', $order->count_price ) }}" />
                </div> 
                <div class="form-group">
                    <label for="payment_state-field">支付状态</label>
                    <input class="form-control" type="text" name="payment_state" id="payment_state-field" value="{{ old('payment_state', $order->payment_state ) }}" />
                </div> 
                <div class="form-group">
                    <label for="paid_at-field">支付时间</label>
                    <input class="form-control" type="text" name="paid_at" id="paid_at-field" value="{{ old('paid_at', $order->paid_at ) }}" />
                </div> 
                <div class="form-group">
                    <label for="is_ok-field">是否处理</label>
                    <input class="form-control" type="text" name="is_ok" id="is_ok-field" value="{{ old('is_ok', $order->is_ok ) }}" />
                </div>

                    <div class="well well-sm">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a class="btn btn-link pull-right" href="{{ route('orders.index') }}"><i class="glyphicon glyphicon-backward"></i>  Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection