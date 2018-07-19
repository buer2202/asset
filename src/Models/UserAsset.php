<?php

namespace Buer\Asset\Models;

use Illuminate\Database\Eloquent\Model;

class UserAsset extends Model
{
    protected $guarded = ['created_at', 'updated_at'];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';
}
