<?php

namespace Buer\Asset\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessOrder extends Model
{
    protected $guarded = ['created_at', 'updated_at'];

    public function tradeOrder()
    {
        return $this->morphTo();
    }
}
