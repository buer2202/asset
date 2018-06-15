<?php
namespace Buer\Asset\Methods;

use Buer\Asset\Exceptions\AssetException;

// 退款
class Refund extends TradeBase
{
    // 前置操作
    public function beforeUser() {
        $this->fee = abs($this->fee);

        // 指定交易类型
        $this->type = self::TRADE_TYPE_REFUND;
    }

    // 更新用户余额
    public function updateUserAsset()
    {
        $this->userAsset->balance      = bcadd($this->userAsset->balance, $this->fee);
        $this->userAsset->total_refund = bcadd($this->userAsset->total_refund, $this->fee);

        if (!$this->userAsset->save()) {
            throw new AssetException('数据更新失败');
        }

        return true;
    }

    // 平台前置操作
    public function beforePlatform() {
        $this->fee = -abs($this->fee);
    }

    // 更新平台资金
    public function updatePlatformAsset()
    {
        $afterAmount = bcadd($this->platformAsset->amount, $this->fee);
        if ($afterAmount < 0) {
            throw new AssetException('平台资金不足');
        }

        $this->platformAsset->amount       = $afterAmount;
        $this->platformAsset->balance      = bcadd($this->platformAsset->balance, abs($this->fee));
        $this->platformAsset->total_refund = bcadd($this->platformAsset->total_refund, abs($this->fee));

        if (!$this->platformAsset->save()) {
            throw new AssetException('数据更新失败');
        }

        return true;
    }
}
