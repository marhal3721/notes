<?php

namespace Image\ControllerOld;

use Grafika\Grafika;

trait ImageCropTrait
{
    //获取图片压缩参数
    protected function getImageCropQuota(array $process) : array
    {
        //指定裁剪宽度
        //指定裁剪高度。
        //指定裁剪起点横坐标（默认左上角为原点）。
        //指定裁剪起点横坐标（默认左上角为原点）。
        //设置裁剪的原点位置。原点按照九宫格的形式分布，一共有九个位置可以设置，为每个九宫格的左上角顶点。
        $width = $height = $xDistance = $yDistance = 0;
        //位置类型
        $position = ImageFactory::CROP_POSITION_MAP['center'];
        foreach ($process as $quota) {
            list($key,$value) = explode('_', $quota);
            if ($key == 'w') {
                $width = $value;
            }
            if ($key == 'h') {
                $height = $value;
            }
            if ($key == 'x') {
                $xDistance = $value;
            }
            if ($key == 'y') {
                $yDistance = $value;
            }
            if ($key == 'g') {
                $position = in_array($value, ImageFactory::CROP_POSITION_MAP) ? $value : $position;
            }
        }

        return [$width,$height, $xDistance, $yDistance, $position];
    }

    protected function crop(array $process)
    {
        list($resource, $path) = $this->getResourcePath();
        unset($resource);
        list($width,$height, $xDistance, $yDistance, $position) = $this->getImageCropQuota($process['crop']);
        $editor = Grafika::createEditor();
        $editor->open($image, $path)->crop($image, $width, $height, $position, $xDistance, $yDistance);
        header('Content-type:image/png');
        header('Cache-control: max-age='.ImageFactory::CACHE_CONTROL);
        $image->blob('PNG');
        $editor->free($image);
    }
}
