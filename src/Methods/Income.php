<?php
namespace Buer\Asset\Methods;

use Buer\Asset\Exceptions\AssetException;
use Buer\Asset\Models\ProcessOrder;

// 交易收入（资金内部流动）
class Income extends TradeBase
{
    // 前置操作
    public function beforeUser() {
        $this->fee = abs($this->fee);

        // 指定交易类型
        $this->type = self::TRADE_TYPE_INCOME;

        // 维护处理中的订单表
        $processOrder = ProcessOrder::where('order_no', $this->tradeNo)->lockForUpdate()->first();
        if (empty($processOrder)) {
            throw new AssetException('不存在该处理中的订单');
        }

        $processOrder->amount = bcadd($processOrder->amount, $this->fee);
        if ($processOrder->amount > 0) {
            throw new AssetException('订单额度不足');
        } elseif ($processOrder->amount == 0) {
            $processOrder->delete(); // 删掉
        } else {
            if (!$processOrder->save()) {
                throw new AssetException('维护处理中的订单失败');
            }
        }
    }

    // 更新用户余额
    public function updateUserAsset()
    {
        $this->userAsset->balance      = bcadd($this->userAsset->balance, $this->fee);
        $this->userAsset->total_income = bcadd($this->userAsset->total_income, $this->fee);

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
        $afterManaged = bcadd($this->platformAsset->managed, $this->fee);
        if ($afterManaged < 0) {
            throw new AssetException('平台资金不足');
        }

        $this->platformAsset->managed              = $afterManaged;
        $this->platformAsset->balance              = bcadd($this->platformAsset->balance, abs($this->fee));
        $this->platformAsset->total_trade_quantity = bcadd($this->platformAsset->total_trade_quantity, 1);
        $this->platformAsset->total_trade_amount   = bcadd($this->platformAsset->total_trade_amount, abs($this->fee));

        if (!$this->platformAsset->save()) {
            throw new AssetException('数据更新失败');
        }

        return true;
    }
}
