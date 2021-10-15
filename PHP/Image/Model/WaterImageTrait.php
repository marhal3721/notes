<?php


namespace Image\Model;

trait WaterImageTrait
{
    public function getWaterImg() : string
    {
        return $this->waterImg;
    }

    public function getResizeType() : string
    {
        return $this->resizeType;
    }

    public function getFill() : int
    {
        return $this->fill;
    }

    public function getTransparency()
    {
        return $this->transparency;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function getXMargin()
    {
        return $this->xMargin;
    }

    public function getYMargin()
    {
        return $this->yMargin;
    }

    public function getRotate()
    {
        return $this->rotate;
    }

    /**
     * 图片水印平铺
     * @param string $imagePath 原图绝对路径
     * @param string $waterImagePath 缩略图绝对路径
     * @param int $angle 水印图片旋转角度
     * @param float $opacity 水印透明度 0-1
     */
    protected function fillPicWalter(string $imagePath, string $waterImagePath, $angle = -10, $opacity = 0.5)
    {
        //参数设置，值越大水印越稀（水印平铺的越少），相反...
        $leftRight = 0;  //每个水印的左右间距
        $topBottom = 0;  //每个水印的上下间距

        //水印透明度
        $opacity = $opacity * 100;

        //获取图片和水印的信息
        $imgInfo = getimagesize($imagePath);
        $sourceInfo = getimagesize($waterImagePath);

        //创建水印图像资源
        $fun   = 'imagecreatefrom' . image_type_to_extension($sourceInfo[2], false);
        $water = $fun($waterImagePath);
        //水印图片旋转
        $water = imagerotate($water, $angle, imageColorAllocateAlpha($water, 0, 0, 0, 127));
        //获取水印图片旋转后的宽度和高度
        $sourceInfo[0] = imagesx($water);
        $sourceInfo[1] = imagesy($water);

        //设定水印图像的混色模式
        imagealphablending($water, true);
        //添加水印
        $waterSrc = imagecreatetruecolor($sourceInfo[0], $sourceInfo[1]);
        // 调整默认颜色
        $color = imagecolorallocate($waterSrc, 255, 255, 255);
        imagefill($waterSrc, 0, 0, $color);

        //创建图片图像资源
        $fun   = 'imagecreatefrom' . image_type_to_extension($imgInfo[2], false);
        $thumb = $fun($imagePath);

        //定义平铺数据
        $xLength = $imgInfo[0] - 10; //x轴总长度
        $yLength = $imgInfo[1] - 10; //y轴总长度
        //循环平铺水印
        for ($x = 0; $x < $xLength; $x) {
            for ($y = 0; $y < $yLength; $y) {
                imagecopy($waterSrc, $thumb, 0, 0, $x, $y, $sourceInfo[0], $sourceInfo[1]);
                imagecopy($waterSrc, $water, 0, 0, 0, 0, $sourceInfo[0], $sourceInfo[1]);
                imagecopymerge($thumb, $waterSrc, $x, $y, 0, 0, $sourceInfo[0], $sourceInfo[1], $opacity);
                $y += $sourceInfo[1] + $topBottom;
            }
            $x += $sourceInfo[0] + $leftRight;
        }

        @unlink($imagePath);
        @unlink($waterImagePath);

        header("Content-type:image/jpeg");
        header('Cache-control: max-age='.ImageFactory::CACHE_CONTROL);
        imagejpeg($thumb);

        //销毁临时图片资源
        imagedestroy($waterSrc);
        //销毁水印资源
        imagedestroy($water);
    }
}
