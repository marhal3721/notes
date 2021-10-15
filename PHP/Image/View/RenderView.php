<?php
namespace Image\View;

use Image\Model\ImageFactory;

class RenderView
{
    private $image;
    private $imageType;

    public function __construct($image, $imageType = 'PNG')
    {
        $this->image = $image;
        $this->imageType = $imageType;
    }

    public function __destruct()
    {
        unset($this->image);
        unset($this->imageType);
    }
    public function getImage()
    {
        return $this->image;
    }

    public function getImageType() : string
    {
        return $this->imageType;
    }

    public function render()
    {
        //输出原图
        header('Content-type: image/png');
        header('Cache-control: max-age='.ImageFactory::CACHE_CONTROL);

        $this->getImage()->blob($this->getImageType());
    }
}
