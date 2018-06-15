<?php

namespace Buer\Asset\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function asset()
    {
        return $this->hasOne(UserAsset::class);
    }
}
