<?php
return [
    // 异常类，可自定义
    'exception_class' => \Buer\Asset\Exceptions\AssetException::class,

    // 交易类型，数字不能改
    'type' => [
        1 => '加款',
        2 => '提现',
        3 => '冻结',
        4 => '解冻',
        5 => '扣款',
        6 => '退款',
        7 => '支出',
        8 => '收入',
    ],

    // 交易子类型，可自定义
    'sub_type' => [
        11 => '加款',
        21 => '提现',
        31 => '冻结',
        41 => '解冻',
        51 => '扣款',
        61 => '退款',
        71 => '支出',
        81 => '收入',
    ],
];
