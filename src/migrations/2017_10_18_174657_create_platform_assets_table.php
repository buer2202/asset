<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatformAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platform_assets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('amount', 17, 4)->default(0)->comment('平台资金（例如手续费之类的收入）');
            $table->decimal('managed', 17, 4)->default(0)->comment('平台托管资金（用户之间交易时，钱存在这里）');
            $table->decimal('balance', 17, 4)->default(0)->comment('用户剩余金额');
            $table->decimal('frozen', 17, 4)->default(0)->comment('用户冻结金额');
            $table->decimal('total_recharge', 17, 4)->default(0)->comment('累计用户加款');
            $table->decimal('total_withdraw', 17, 4)->default(0)->comment('累计用户提现');
            $table->decimal('total_consume', 17, 4)->default(0)->comment('累计用户消费');
            $table->decimal('total_refund', 17, 4)->default(0)->comment('累计退款给用户');
            $table->unsignedInteger('total_trade_quantity')->default(0)->comment('累计用户成交次数');
            $table->decimal('total_trade_amount', 17, 4)->default(0)->comment('累计用户成交金额');
            $table->dateTime('created_at')->nullable()->default(null);
            $table->dateTime('updated_at')->nullable()->default(null);

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
        Schema::dropIfExists('platform_assets');
    }
}
