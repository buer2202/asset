<?php
namespace Buer\Asset\Methods;

use Buer\Asset\Exceptions\AssetException;

// 冻结
class Freeze extends TradeBase
{
    // 前置操作
    public function beforeUser() {
        $this->fee = -abs($this->fee);

        // 指定交易类型
        $this->type = self::TRADE_TYPE_FREEZE;
    }

    // 更新用户余额
    public function updateUserAsset()
    {
        $afterBalance = bcadd($this->userAsset->balance, $this->fee);
        if ($afterBalance < 0) {
            throw new AssetException("The user's remaining balance is insufficient");
        }

        $this->userAsset->balance = $afterBalance;
        $this->userAsset->frozen  = bcadd($this->userAsset->frozen, abs($this->fee));

        if (!$this->userAsset->save()) {
            throw new AssetException("Failed to update the user's asset");
        }

        return true;
    }

    // 更新平台资金
    public function updatePlatformAsset()
    {
        $afterBalance = bcadd($this->platformAsset->balance, $this->fee);
        if ($afterBalance < 0) {
            throw new AssetException("The platform's remaining balance is insufficient");
        }

        $this->platformAsset->balance = $afterBalance;
        $this->platformAsset->frozen  = bcadd($this->platformAsset->frozen, abs($this->fee));

        if (!$this->platformAsset->save()) {
            throw new AssetException("Failed to update the platform's asset");
        }

        return true;
    }
}
