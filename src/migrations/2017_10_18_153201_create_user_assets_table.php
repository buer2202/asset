<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_assets', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->comment('users.id');
            $table->decimal('balance', 17, 4)->default(0)->comment('剩余金额');
            $table->decimal('frozen', 17, 4)->default(0)->comment('冻结金额');
            $table->decimal('total_recharge', 17, 4)->default(0)->comment('累计平台加款');
            $table->decimal('total_withdraw', 17, 4)->default(0)->comment('累计平台提现');
            $table->decimal('total_consume', 17, 4)->default(0)->comment('累计平台消费');
            $table->decimal('total_refund', 17, 4)->default(0)->comment('累计平台退款');
            $table->decimal('total_expend', 17, 4)->default(0)->comment('累计交易支出');
            $table->decimal('total_income', 17, 4)->default(0)->comment('累计交易收入');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            $table->primary('user_id');
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
        Schema::dropIfExists('user_assets');
    }
}
