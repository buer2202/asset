<?php
namespace Buer\Asset\Methods;

use Buer\Asset\Exceptions\AssetException;

// 转账
class Transfer
{
    public function __construct($fee, $subtype, $tradeNo, $remark, $expendUserId, $imcomeUserId, $adminUserId = 0, $tradeModel = null, $expendFrom = null)
    {
        new Expend($fee, $subtype, $tradeNo, $remark, $expendUserId, $adminUserId, $tradeModel, $expendFrom);
        new Income($fee, $subtype, $tradeNo, $remark, $imcomeUserId, $adminUserId, $tradeModel);
    }
}
