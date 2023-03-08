<?php

namespace App\Services;

class ResourceService
{
    public const OPT_IMAGE_RESOURCE_PREFIX = 'resource:image:';
    public const OPT_TEXT_RESOURCE_PREFIX = 'resource:text:';

    public function saveImage($key, $value)
    {
        opt(self::OPT_IMAGE_RESOURCE_PREFIX)->set($key, $value);
    }

    public function saveText($key, $value)
    {
        opt(self::OPT_TEXT_RESOURCE_PREFIX)->set($key, $value);
    }

    public function getImages($keys)
    {
        return opt(self::OPT_IMAGE_RESOURCE_PREFIX)->batchGet($keys);
    }

    public function getTexts($keys)
    {
        return opt(self::OPT_TEXT_RESOURCE_PREFIX)->batchGet($keys);
    }
}
