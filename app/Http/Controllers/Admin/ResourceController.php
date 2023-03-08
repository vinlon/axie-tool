<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ImageResource;
use App\Enums\TextResource;
use App\Http\Controllers\Controller;
use App\Services\ResourceService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

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

    /** 上传资源图片 */
    public function uploadImage()
    {
        $file = request()->file('file');
        $publicStorage = Storage::disk();
        $path = $publicStorage->putFile('resource', $file);

        return [
            'image_url' => $publicStorage->url($path),
        ];
    }

    /** 查询图片资源列表 */
    public function listImageResources()
    {
        $result = [];
        $values = $this->resourceService->getImages(ImageResource::getValues());

        foreach (ImageResource::getInstances() as $inst) {
            $key = $inst->value;
            $value = Arr::get($values, $key, '');
            $result[] = [
                'key' => $key,
                'description' => $inst->description,
                'image_url' => $value,
            ];
        }

        return $result;
    }

    /** 保存图片资源 */
    public function saveImageResource()
    {
        $params = request()->only(ImageResource::getValues());
        foreach ($params as $key => $imageUrl) {
            $this->resourceService->saveImage($key, $imageUrl);
        }
    }

    /** 查询富文本资源列表 */
    public function listTextResources()
    {
        $result = [];
        $values = $this->resourceService->getTexts(TextResource::getValues());

        foreach (TextResource::getInstances() as $inst) {
            $key = $inst->value;
            $value = Arr::get($values, $key, '');
            $result[] = [
                'key' => $key,
                'description' => $inst->description,
                'content' => $value,
            ];
        }

        return $result;
    }

    /** 保存富文本 */
    public function saveTextResource()
    {
        request()->validate([
            'key' => 'required',
            'content' => 'required',
        ]);
        $this->resourceService->saveText(request()->key, request()->get('content', ''));
    }
}
