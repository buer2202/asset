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
            throw new AssetException('用户余额不足');
        }

        $this->userAsset->balance = $afterBalance;
        $this->userAsset->frozen  = bcadd($this->userAsset->frozen, abs($this->fee));

        if (!$this->userAsset->save()) {
            throw new AssetException('数据更新失败');
        }

        return true;
    }

    // 更新平台资金
    public function updatePlatformAsset()
    {
        $afterBalance = bcadd($this->platformAsset->balance, $this->fee);
        if ($afterBalance < 0) {
            throw new AssetException('平台余额不足');
        }

        $this->platformAsset->balance = $afterBalance;
        $this->platformAsset->frozen  = bcadd($this->platformAsset->frozen, abs($this->fee));

        if (!$this->platformAsset->save()) {
            throw new AssetException('数据更新失败');
        }

        return true;
    }
}
