<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhoneNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phone_numbers', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('aid')->comment('文件数据id');

            $table->string('phone')->index()->comment('手机号');
            $table->text('nr')->nullable()->comment('发送内容');
            $table->string('message')->index()->comment('返回消息');
            $table->integer('isok')->comment('是否发送成功');

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
        Schema::dropIfExists('phone_numbers');
    }
}
