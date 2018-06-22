<?php

namespace Buer\Asset;

use Illuminate\Support\Facades\Facade;

class AssetFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'asset';
    }
}
