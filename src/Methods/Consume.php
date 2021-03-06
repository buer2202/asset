<?php
namespace Buer\Asset\Methods;

use Buer\Asset\Exceptions\AssetException;

// 消费/扣款（平台收益）
class Consume extends TradeBase
{
    // 前置操作
    public function beforeUser() {
        $this->fee = -abs($this->fee);

        // 指定交易类型
        $this->type = self::TRADE_TYPE_CONSUME;
    }

    // 更新用户余额
    public function updateUserAsset()
    {
        if ($this->expendFrom == 'frozen') {
            $afterFrozen = bcadd($this->userAsset->frozen, $this->fee);
            if ($afterFrozen < 0) {
                throw new AssetException('用户冻结金额不足');
            }

            $this->userAsset->frozen = $afterFrozen;
        } else {
            $afterBalance = bcadd($this->userAsset->balance, $this->fee);
            if ($afterBalance < 0) {
                throw new AssetException('用户剩余金额不足');
            }

            $this->userAsset->balance = $afterBalance;
        }

        $this->userAsset->total_consume = bcadd($this->userAsset->total_consume, abs($this->fee));

        if (!$this->userAsset->save()) {
            throw new AssetException('数据更新失败');
        }

        return true;
    }

    // 平台前置操作
    public function beforePlatform() {
        $this->fee = abs($this->fee);
    }

    // 更新平台资金
    public function updatePlatformAsset()
    {
        if ($this->expendFrom == 'frozen') {
            $afterFrozen = bcsub($this->platformAsset->frozen, $this->fee);
            if ($afterFrozen < 0) {
                throw new AssetException('平台冻结金额不足');
            }

            $this->platformAsset->frozen = $afterFrozen;
        } else {
            $afterBalance = bcsub($this->platformAsset->balance, $this->fee);
            if ($afterBalance < 0) {
                throw new AssetException('平台剩余金额不足');
            }

            $this->platformAsset->balance = $afterBalance;
        }

        $this->platformAsset->amount        = bcadd($this->platformAsset->amount, $this->fee);
        $this->platformAsset->total_consume = bcadd($this->platformAsset->total_consume, $this->fee);

        if (!$this->platformAsset->save()) {
            throw new AssetException('数据更新失败');
        }

        return true;
    }
}
