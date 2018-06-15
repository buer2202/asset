<?php

namespace Buer\Asset\Exceptions;

use Exception;

class AssetException extends Exception
{
    public function errorMessage()
    {
        $errorMsg = '异常文件: ' . $this->getFile() . ' 行: ' . $this->getLine() . ' 信息: ' . $this->getMessage();

        return $this->getMessage();
    }
}
