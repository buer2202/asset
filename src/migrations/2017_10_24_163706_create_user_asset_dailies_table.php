<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAssetDailiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_asset_dailies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date')->comment('结算日期');
            $table->unsignedInteger('user_id')->comment('用户id：users.id');
            $table->decimal('balance', 17, 4)->comment('剩余金额');
            $table->decimal('frozen', 17, 4)->comment('冻结金额');

            $table->decimal('recharge', 17, 4)->comment('当日往平台加款');
            $table->decimal('total_recharge', 17, 4)->comment('累计往平台加款');

            $table->decimal('withdraw', 17, 4)->comment('当日从平台提现');
            $table->decimal('total_withdraw', 17, 4)->comment('累计从平台提现');

            $table->decimal('consume', 17, 4)->comment('当日在平台消费');
            $table->decimal('total_consume', 17, 4)->comment('累计在平台消费');

            $table->decimal('refund', 17, 4)->comment('当日从平台收到退款');
            $table->decimal('total_refund', 17, 4)->comment('累计从平台收到退款');

            $table->decimal('expend', 17, 4)->comment('当日交易支出');
            $table->decimal('total_expend', 17, 4)->comment('累计交易支出');

            $table->decimal('income', 17, 4)->comment('当日交易收入');
            $table->decimal('total_income', 17, 4)->comment('累计交易收入');

            $table->index('date');
            $table->index('user_id');
            $table->index(['date', 'user_id']);
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
        Schema::dropIfExists('user_asset_dailies');
    }
}
