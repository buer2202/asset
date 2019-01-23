<?php
namespace Buer\Asset;

use Buer\Asset\Exceptions\AssetException;

// 资产
class Asset
{
    protected $object = null; // 当前对象

    // 魔术方法允许的方法-资产变动
    protected $classes = [
        'recharge' => Methods\Recharge::class, // 加款
        'withdraw' => Methods\Withdraw::class, // 提现
        'freeze'   => Methods\Freeze::class,   // 冻结
        'unfreeze' => Methods\Unfreeze::class, // 解冻
        'consume'  => Methods\Consume::class,  // 扣款
        'refund'   => Methods\Refund::class,   // 退款
        'expend'   => Methods\Expend::class,   // 支出
        'income'   => Methods\Income::class,   // 收入
        'transfer' => Methods\Transfer::class, // 转账
    ];

    // 魔术方法允许的方法-资产查询
    protected $methods = [
        'getUserAsset',
        'getUserAmountFlow',
        'getPlatformAsset',
        'getPlatformAmountFlow',
    ];

    public function __construct()
    {
        // 设置bc数学函数的默认小数点保留位数
        bcscale(4);
    }

    /**
     * @param float $fee 金额
     * @param int $subtype 子类型
     * @param string $tradeNo 单号
     * @param string $remark 备注
     * @param int $userId 用户id
     * @param int $adminUserId 管理员id
     * @param int $tradeModel 关联订单模型，用于更新流水的多态关联
     * @param int $expendFrom 扣款来源。调用expend和consume方法时有效。可选项: balance (余额), frozen (冻结)。默认从余额扣款
     */
    public function __call($name, $arguments)
    {
        $exception = config('asset.exception_class');

        if (isset($this->classes[$name])) {
            try {
                $this->object = new $this->classes[$name](
                    $arguments[0],
                    $arguments[1],
                    $arguments[2],
                    $arguments[3],
                    $arguments[4],
                    $arguments[5] ?? 0,
                    $arguments[6] ?? null,
                    $arguments[7] ?? null
                );
            }
            catch (AssetException $e) {
                throw new $exception($e->getMessage());
            }
        } elseif (in_array($name, $this->methods)) {
            if (empty($this->object)) {
                throw new $exception('进行资产操作后，才能获取资产信息');
            }

            return $this->object->$name();

        } else {
            throw new $exception('不存在该资产方法');
        }

        return true;
    }
}
