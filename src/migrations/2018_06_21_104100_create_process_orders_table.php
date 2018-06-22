<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 进行中的订单
        Schema::create('process_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_no', 30)->comment('订单号');
            $table->decimal('amount', 11, 4)->comment('金额');
            $table->unsignedInteger('user_id')->comment('用户id');
            $table->string('orderable_type', 150)->default('')->comment('多态关联模型名');
            $table->unsignedInteger('orderable_id')->default(0)->comment('多态关联对象id');
            $table->timestamps();

            $table->unique('order_no');
            $table->index('user_id');
            $table->index('orderable_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('process_orders');
    }
}
