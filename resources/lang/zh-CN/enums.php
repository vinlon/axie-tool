<?php

use App\Enums\AvailableStatus;
use App\Enums\ImageResource;
use App\Enums\PurchaseStatus;
use App\Enums\TextResource;

return [
    ImageResource::class => [
        ImageResource::TEST => '测试专用',
    ],
    TextResource::class => [
        TextResource::TEST => '测试专用',
    ],
    AvailableStatus::class => [
        AvailableStatus::ENABLED => '启用',
        AvailableStatus::DISABLED => '禁用',
    ],

    PurchaseStatus::class => [
        PurchaseStatus::DONE => '购买成功',
        PurchaseStatus::FAIL => '购买失败',
        PurchaseStatus::WAITING => '确认中',
    ]
];
