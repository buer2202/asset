<?php

namespace Buer\Asset\Models;

use Illuminate\Database\Eloquent\Model;

class UserAssetDaily extends Model
{
    /**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function realNameIdent()
    {
        return $this->hasOne(RealNameIdent::class, 'user_id', 'user_id');
    }
}
