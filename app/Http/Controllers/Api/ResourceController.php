<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ResourceService;

class ResourceController extends Controller
{
    /** @var ResourceService */
    private $resourceService;

    /**
     * ResourceController constructor.
     */
    public function __construct(ResourceService $resourceService)
    {
        $this->resourceService = $resourceService;
    }

    /** 查询富文本资源 */
    public function getTexts()
    {
        $keys = explode(',', request()->keys ?: '');

        return $this->resourceService->getTexts($keys);
    }

    /** 查询图片资源 */
    public function getImages()
    {
        $keys = explode(',', request()->keys ?: '');

        return $this->resourceService->getImages($keys);
    }
}
