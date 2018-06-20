## Laravel Asset 1.1
用户资金管理系统，需要有users表。

## 框架要求
Laravel >= 5.1

## 安装
```
composer require buer/asset
```

## 配置
1.在 config/app.php 注册 ServiceProvider 和 Facade (Laravel 5.5 无需手动注册)
```
'providers' => [
    // ...
    Buer\Asset\Providers\AssetServiceProvider::class,
],
'aliases' => [
    // ...
    'Asset' => Buer\Asset\Facades\Asset::class,
],
```

## 数据迁移
```
php artisan migrate
```

laravel5.4迁移时因编码问题会抛异常索引key过长
解决问题，2个办法：
1.升级MySql版本到5.5.3以上。
2.手动配置迁移命令migrate生成的默认字符串长度，在AppServiceProvider中调用Schema::defaultStringLength方法来实现配置：

```
use Illuminate\Support\Facades\Schema;

public function boot()
{
   Schema::defaultStringLength(191);
}
```

## 拷贝配置文件
```
php artisan vendor:publish
```

## 程序调用
使用Asset门面
```
use Asset;

#...

Asset::recharge('金额', 1, '订单号', '备注', '用户ID', '管理员ID', '关联模型对象'); // 加款
Asset::freeze('金额', 2, '订单号', '备注', '用户ID', '管理员ID', '关联模型对象'); // 冻结
Asset::withdraw('金额', 3, '订单号', '备注', '用户ID', '管理员ID', '关联模型对象'); // 提现
Asset::unfreeze('金额', 4, '订单号', '备注', '用户ID', '管理员ID', '关联模型对象'); // 解冻
Asset::consume('金额', 5, '订单号', '备注', '用户ID', '管理员ID', '关联模型对象'); // 从余额扣款
Asset::consume('金额', 5, '订单号', '备注', '用户ID', '管理员ID', '关联模型对象', 'frozen'); // 从冻结扣款
Asset::refund('金额', 6, '订单号', '备注', '用户ID', '管理员ID', '关联模型对象'); // 退款
Asset::expend('金额', 7, '订单号', '备注', '用户ID', '管理员ID', '关联模型对象'); // 从余额支出
Asset::expend('金额', 7, '订单号', '备注', '用户ID', '管理员ID', '关联模型对象', 'frozen'); // 从冻结支出
Asset::income('金额', 8, '订单号', '备注', '用户ID', '管理员ID', '关联模型对象'); // 收入
```

## 日结
每天需要执行一次日结命令，执行时间不限:
```
php artisan daily-settlement:user-asset
php artisan daily-settlement:platform-asset
```
若漏做，可以使用日期参数补做:
```
php artisan daily-settlement:user-asset 20180620
php artisan daily-settlement:platform-asset 20180620
```

## 用户对账
剩余金额 + 冻结金额 = 累计平台加款 + 累计平台退款 + 累计交易收入 - 累计平台提现 - 累计平台消费 - 累计交易支出

## 平台对账
累计用户加款 - 累计用户提现 = 平台资金 + 托管资金 + 用户总余额 + 用户总冻结
