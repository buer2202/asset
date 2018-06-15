<?php
namespace Buer\Asset\Methods;

use Buer\Asset\Exceptions\AssetException;

// 提现
class Withdraw extends TradeBase
{
    // 前置操作
    public function beforeUser() {
        $this->fee = -abs($this->fee);

        // 指定交易类型
        $this->type = self::TRADE_TYPE_WITHDRAW;
    }

    // 更新用户余额
    public function updateUserAsset()
    {
        $afterFrozen = bcadd($this->userAsset->frozen, $this->fee);
        if ($afterFrozen < 0) {
            throw new AssetException('用户冻结金额不足');
        }

        $this->userAsset->frozen         = $afterFrozen;
        $this->userAsset->total_withdraw = bcadd($this->userAsset->total_withdraw, abs($this->fee));

        if (!$this->userAsset->save()) {
            throw new AssetException('数据更新失败');
        }

        return true;
    }

    // 更新平台资金
    public function updatePlatformAsset()
    {
        $afterFrozen = bcadd($this->platformAsset->frozen, $this->fee);
        if ($afterFrozen < 0) {
            throw new AssetException('平台冻结金额不足');
        }

        $this->platformAsset->frozen         = $afterFrozen;
        $this->platformAsset->total_withdraw = bcadd($this->platformAsset->total_withdraw, abs($this->fee));

        if (!$this->platformAsset->save()) {
            throw new AssetException('数据更新失败');
        }

        return true;
    }
}
