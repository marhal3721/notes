<?php


namespace Image\ControllerOld;

use Grafika\Grafika;

trait ImageAddImgWaterTrait
{
    abstract protected function resize(array $process, $output = 0, $file = '');

    /**
     * 压缩水印图片
     * @param $waterImg string 水印图片绝对路径
     * @param $width int 水印宽
     * @param $height int 水印高
     * @param $resizeType string 水印压缩类型
     * @return string
     */
    protected function resizeWaterImage(string $waterImg, int $width, int $height, string $resizeType) : string
    {
        if (!empty($width) || !empty($height)) {
            $resize = ['resize' => []];
            if (!empty($width)) {
                $resize['resize'][] = 'w_' . $width;
            }
            if (!empty($height)) {
                $resize['resize'][] = 'h_' . $height;
            }
            if (!empty($resizeType)) {
                $resize['resize'][] = 'm_' . $resizeType;
            }
            $waterImg = $this->resize($resize, 1, $waterImg);
        }

        return $waterImg;
    }

    /**
     * 图片水印平铺
     * @param string $imagePath  原图绝对路径
     * @param string $waterImagePath  缩略图绝对路径
     * @param int $angle 水印图片旋转角度
     * @param float $opacity 水印透明度 0-1
     */
    protected function fillPicWalter($imagePath, $waterImagePath, $angle = -10, $opacity = 0.5)
    {
        //参数设置，值越大水印越稀（水印平铺的越少），相反...
        $ww = 0;  //每个水印的左右间距
        $hh = 0;  //每个水印的上下间距

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
        $src = imagecreatetruecolor($sourceInfo[0], $sourceInfo[1]);
        // 调整默认颜色
        $color = imagecolorallocate($src, 255, 255, 255);
        imagefill($src, 0, 0, $color);

        //创建图片图像资源
        $fun   = 'imagecreatefrom' . image_type_to_extension($imgInfo[2], false);
        $thumb = $fun($imagePath);

        //定义平铺数据
        $xLength = $imgInfo[0] - 10; //x轴总长度
        $yLength = $imgInfo[1] - 10; //y轴总长度
        //循环平铺水印
        for ($x = 0; $x < $xLength; $x) {
            for ($y = 0; $y < $yLength; $y) {
                imagecopy($src, $thumb, 0, 0, $x, $y, $sourceInfo[0], $sourceInfo[1]);
                imagecopy($src, $water, 0, 0, 0, 0, $sourceInfo[0], $sourceInfo[1]);
                imagecopymerge($thumb, $src, $x, $y, 0, 0, $sourceInfo[0], $sourceInfo[1], $opacity);
                $y += $sourceInfo[1] + $hh;
            }
            $x += $sourceInfo[0] + $ww;
        }

        header("Content-type:image/jpeg");
        header('Cache-control: max-age='.ImageFactory::CACHE_CONTROL);
        imagejpeg($thumb);

        //销毁临时图片资源
        imagedestroy($src);
        //销毁水印资源
        imagedestroy($water);
    }

    protected function getImageWaterQuota(array $waterProcess) : array
    {
        //文字 、 水印图片 、 文字颜色
        $text = $image = $color = '';
        $width = $height = 0;//水印图片宽高
        $size = 0;//文字大小
        $fill = 0;//是否铺满 0 否 1是
        $touMingDu = 1.0;//透明度
        $xMargin = $yMargin = 0;//水平边距、垂直边距
        $position = ImageFactory::OSS_QXY_POSITION_MAP['nw'];//位置
        $resizeType = 'fixed';//水印图片压缩类型
        $rotate = 0;//水印旋转
        foreach ($waterProcess as $quota) {
            list($key,$value) = explode('_', $quota);

            if ($key == 'w') {
                $width = $value;
            }
            if ($key == 'h') {
                $height = $value;
            }
            if ($key == 'text') {
                $text = base64_decode($value);
            }
            if ($key == 'image') {
                $image = base64_decode($value);
            }
            if ($key == 'color') {
                $color = $value;
            }
            if ($key == 'size') {
                $size = $value;
            }
            if ($key == 'fill') {
                $fill = $value;
            }
            if ($key == 't') {
                $touMingDu = $value <= 0 ? 1 : $value;
            }
            if ($key == 'x') {
                $xMargin = $value;
            }
            if ($key == 'y') {
                $yMargin = $value;
            }
            if ($key == 'g') {
                $position = ImageFactory::OSS_QXY_POSITION_MAP[$value] ?? ImageFactory::OSS_QXY_POSITION_MAP[$position];
            }
            if ($key == 'm') {
                $resizeType = $value;
            }
            if ($key == 'rt') {
                $rotate = $value;
            }
        }

        return [
            $width, $height, $resizeType,
            $image, $text, $color, $size,
            $fill, $touMingDu, $xMargin,
            $yMargin, $position,$rotate
        ];
    }

    /**
     * 获取原图editor
     * @param array $process
     * @return array
     */
    protected function getOriginalDrawingEditor(array $process) : array
    {
        # 原图
        $editor = Grafika::createEditor();
        list($file, $savePath) = $this->getResourcePath();
        $editor->open($image, $savePath);

        if (isset($process['resize']) && !empty($process['resize'])) {
            $file = $this->resize($process, 1);
            list($file, $savePath) = $this->getResourcePath($file);
            $editor->free($image);
            unset($editor);
            unset($image);

            $editor = Grafika::createEditor();
            $editor->open($image, $savePath);
        }
        unset($file);

        return [$editor,$image,$savePath];
    }

    protected function water($process)
    {
        list($editor, $image, $savePath) = $this->getOriginalDrawingEditor($process);

        # 处理水印
        list(
            $width,
            $height,
            $resizeType,
            $waterImg,
            $text,
            $color,
            $size,
            $fill,
            $touMingDu,
            $xMargin,
            $yMargin,
            $position,
            $rotate
            ) = $this->getImageWaterQuota($process['watermark']);

        # 图片水印
        if (!empty($waterImg)) {
            //处理水印
            $waterImg = $this->resizeWaterImage($waterImg, $width, $height, $resizeType);
            //获取水印图片的全路径
            list($waterFile, $waterFileSavePath) = $this->getResourcePath($waterImg);
            unset($waterFile);
            # 图片水印铺满
            if ($fill == ImageFactory::WATER_IS_FULL['YES']) {
                $this->fillPicWalter($savePath, $waterFileSavePath, $rotate, $touMingDu);
            }

            # 只添加一个水印
            $waterEditor = Grafika::createEditor();
            $waterEditor->open($waterImage, $waterFileSavePath);
            // 合并图片
            $editor->blend($image, $waterImage, 'normal', $touMingDu, $position, $xMargin, $yMargin);
            $waterEditor->free($waterImage);

            header('Content-type: image/png');
            header('Cache-control: max-age='.ImageFactory::CACHE_CONTROL);
            $image->blob('PNG');
            $editor->free($image);
            unset($editor);
        }

        # 文字水印
        if (!empty($text)) {
            unset($color);
            unset($size);
        }
        header('Content-type: image/png');
        header('Cache-control: max-age=300'.ImageFactory::CACHE_CONTROL);
        $image->blob('PNG');
    }
}
