<?php

namespace Image\Model;

class CropImage extends Image
{
    private $xDistance;//指定裁剪起点横坐标（默认左上角为原点）。
    private $yDistance;//指定裁剪起点横坐标（默认左上角为原点）。
    private $position;//设置裁剪的原点位置。原点按照九宫格的形式分布，一共有九个位置可以设置，为每个九宫格的左上角顶点。

    public function __construct(array $process, $editor, $image)
    {
        parent::__construct($process, $editor, $image);
        $this->xDistance = 0;
        $this->yDistance = 0;
        $this->position = ImageFactory::CROP_POSITION_MAP['center'];

        $this->getQuota($process);
    }

    //获取图片压缩参数
    protected function getQuota(array $process) : void
    {
        $image = $this->getImage();
        $imageWidth = $image->getWidth();
        $imageHeight = $image->getHeight();

        $this->width = ($this->width == 0 ? $imageWidth : $this->width);
        $this->height = ($this->height == 0 ? $imageHeight : $this->height);

        foreach ($process as $quota) {
            $quotas = explode('_', $quota);
            if (count($quotas) == 2) {
                list($key,$value) = $quotas;
                if ($key == 'w') {
                    $this->width = ($value == 0 ? $this->getImage()->getWidth() : $value);
                }
                if ($key == 'h') {
                    $this->height = ($value == 0 ? $this->getImage()->getHeight() : $value);
                }
                if ($key == 'x') {
                    $this->xDistance = $value;
                }
                if ($key == 'y') {
                    $this->yDistance = $value;
                }
                if ($key == 'g') {
                    $this->position = in_array($value, ImageFactory::CROP_POSITION_MAP)
                        ? $value
                        : $this->position;
                }
            }
        }
    }

    public function outPut() : array
    {
        $editor = $this->getEditor();
        $image = $this->getImage();

        $editor->crop(
            $image,
            $this->getWidth(),
            $this->getHeight(),
            $this->getPosition(),
            $this->getXDistance(),
            $this->getYDistance()
        );

        return [$editor, $image];
    }

    public function getPosition() : string
    {
        return $this->position;
    }

    public function getXDistance() : int
    {
        return $this->xDistance;
    }

    public function getYDistance() : int
    {
        return $this->yDistance;
    }
}
