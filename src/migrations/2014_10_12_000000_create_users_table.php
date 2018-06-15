<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->string('api_secret', 60)->comment('api密钥')->unique();
            $table->tinyInteger('status')->default(1)->comment('状态 0.禁用 1.正常');

            $table->tinyInteger('type')->default(1)->comment('用户类型: 1.个人 2.企业');
            $table->string('company', 20)->nullable()->default(null)->comment('企业名');
            $table->string('license', 20)->nullable()->default(null)->comment('营业执照号');
            $table->string('real_name', 20)->default('')->comment('真实姓名');
            $table->string('id_number', 20)->comment('身份证号');
            $table->string('phone', 20)->comment('手机号码');
            $table->string('qq', 20)->comment('联系qq');
            $table->tinyInteger('certification')->default(0)->comment('实名认证：0.未认证 1.审核中 2.审核未通过 3.审核通过');
            $table->timestamps();

            $table->index('certification');
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
        Schema::dropIfExists('users');
    }
}
