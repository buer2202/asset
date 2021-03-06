<?php
namespace Buer\Asset\Models\Relations;

use Buer\Asset\Models\UserAmountFlow;
use Buer\Asset\Models\PlatformAmountFlow;
use Buer\Asset\Models\ProcessOrder;

// 流水关联模型多态关联方法
trait AssetAmountMorphMany {

    public function userAmountFlows()
    {
        return $this->morphMany(UserAmountFlow::class, 'flowable');
    }

    public function platformAmountFlows()
    {
        return $this->morphMany(PlatformAmountFlow::class, 'flowable');
    }

    public function processOrders()
    {
        return $this->morphMany(ProcessOrder::class, 'orderable');
    }
}
