<?php

namespace Buer\Asset\Events;

use Buer\Asset\Models\PlatformAmountFlow;
use Buer\Asset\Models\UserAmountFlow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class AssetTradeCompletedEvent
{
    use Dispatchable, SerializesModels;

    public $platformAmountFlow;
    public $userAmountFlow;

    public function __construct(PlatformAmountFlow $platformAmountFlow, UserAmountFlow $userAmountFlow)
    {
        $this->platformAmountFlow = $platformAmountFlow;
        $this->userAmountFlow = $userAmountFlow;
    }
}
