<?php

use App\Enums\AvailableStatus;
use App\Enums\ImageResource;
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
];
