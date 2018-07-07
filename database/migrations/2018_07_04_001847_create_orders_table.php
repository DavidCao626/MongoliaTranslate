<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->string('name')->comment('订单名字');
            $table->string('uuid')->comment('产品订单号码');
            $table->string('wechat_order_id')->comment('微信内部商户订单号码');
          $table->string('nickname')->index()->comment('微信昵称');
            $table->string('opid')->index()->comment('微信用户opid');
            $table->string('phone')->index()->comment('用户联系电话');
            $table->bigInteger('categories')->index()->comment('订单分类');
            $table->string('type')->index()->comment('订单翻译类别');
            $table->text('body')->comment('订单翻译内容')->nullable();
            $table->text('text_count')->comment('翻译字数');
            $table->unsignedInteger('unit_price')->comment('翻译单价');
            $table->unsignedInteger('count_price')->comment('翻译总价');
            $table->unsignedInteger('payment_state')->comment('订单状态')->default(0);
            $table->dateTime('paid_at')->comment('成功支付时间')->nullable();;
            $table->boolean('is_ok')->comment('成功支付时间')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
