<?php

namespace Buer\Asset\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformAssetDaily extends Model
{
    /**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'date';

    protected $casts = ['date' => 'string'];
}
