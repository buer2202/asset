<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatformAssetDailiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platform_asset_dailies', function (Blueprint $table) {
            $table->date('date')->primary()->comment('结算日期');
            $table->decimal('amount', 17, 4)->comment('平台资金');
            $table->decimal('managed', 17, 4)->comment('平台托管资金');
            $table->decimal('balance', 17, 4)->comment('用户剩余金额');
            $table->decimal('frozen', 17, 4)->comment('用户冻结金额');
            $table->decimal('recharge', 17, 4)->comment('当日用户加款');
            $table->decimal('total_recharge', 17, 4)->comment('累计用户加款');
            $table->decimal('withdraw', 17, 4)->comment('当日用户提现');
            $table->decimal('total_withdraw', 17, 4)->comment('累计用户提现');
            $table->decimal('consume', 17, 4)->comment('当日用户消费');
            $table->decimal('total_consume', 17, 4)->comment('累计用户消费');
            $table->decimal('refund', 17, 4)->comment('当日退款给用户');
            $table->decimal('total_refund', 17, 4)->comment('累计退款给用户');
            $table->unsignedInteger('trade_quantity')->comment('当日用户成交次数');
            $table->unsignedInteger('total_trade_quantity')->comment('累计用户成交次数');
            $table->decimal('trade_amount', 17, 4)->comment('当日用户成交金额');
            $table->decimal('total_trade_amount', 17, 4)->comment('累计用户成交金额');

            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('platform_asset_dailies');
    }
}
