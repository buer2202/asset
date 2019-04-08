<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatformAmountFlowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platform_amount_flows', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('users.id');
            $table->unsignedInteger('admin_user_id')->comment('admin_users.id');
            $table->integer('trade_type')->comment('交易类型:  1.用户加款 2.用户提现 3.用户冻结 4.用户解冻 5.用户消费 6.退款给用户 7.用户支出 8.用户收入');
            $table->integer('trade_subtype')->comment('交易子类型: 11.自动加款 12.手动加款 21.手动提现 31.提现冻结 32.抢单冻结 41.提现解冻 42.抢单解冻 51.消费手续费 61.手续费退款 71.订单集市支出 81.订单集市收入');
            $table->string('trade_no')->default('')->comment('相关单号');
            $table->decimal('fee', 17, 4)->default(0)->comment('金额');
            $table->string('remark')->default('')->comment('备注说明');
            $table->decimal('amount', 17, 4)->comment('平台资金（例如手续费之类的收入）');
            $table->decimal('managed', 17, 4)->comment('平台托管资金（用户之间交易时，钱存在这里）');
            $table->decimal('balance', 17, 4)->comment('用户剩余总金额');
            $table->decimal('frozen', 17, 4)->comment('用户冻结总金额');
            $table->decimal('total_recharge', 17, 4)->comment('累计用户加款');
            $table->decimal('total_withdraw', 17, 4)->comment('累计用户提现');
            $table->decimal('total_consume', 17, 4)->comment('累计用户消费');
            $table->decimal('total_refund', 17, 4)->comment('累计退款给用户');
            $table->unsignedInteger('total_trade_quantity')->comment('累计用户成交次数');
            $table->decimal('total_trade_amount', 17, 4)->comment('累计用户成交金额');
            $table->dateTime('created_at');
            $table->string('flowable_type', 150)->default('')->comment('多态关联模型名');
            $table->unsignedInteger('flowable_id')->default(0)->comment('多态关联对象id');

            $table->index('user_id');
            $table->index('trade_type');
            $table->index('trade_subtype');
            $table->index('trade_no');
            $table->index('created_at');
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
        Schema::dropIfExists('platform_amount_flows');
    }
}
