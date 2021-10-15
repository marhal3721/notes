<?php
namespace Image\Model;

use Grafika\Color;
use Image\ImageTrait;

class ResizeImage extends Image
{
    use ImageTrait;

    private $long;//缩放图的最长边
    private $proportion;//压缩比例
    private $zoomMode;//缩放模式
    private $color;//填充颜色

    public function __construct(array $process, $editor, $image)
    {
        parent::__construct($process, $editor, $image);
        $this->long = 0;
        $this->proportion = 0;
        $this->zoomMode = '';
        $this->color = '#fff';
        $this->getQuota($process);
    }

    //获取图片压缩参数
    protected function getQuota(array $process) : void
    {
        foreach ($process as $quota) {
            $quotas = explode('_', $quota);
            if (count($quotas) == 2) {
                list($key,$value) = $quotas;
                if ($key == 'm') {
                    $this->zoomMode = $value;
                }
                if ($key == 'h') {
                    $this->height = $value;
                }
                if ($key == 'w') {
                    $this->width = $value;
                }
                if ($key == 'color') {
                    $this->color = '#' . $value;
                }
                if ($key == 'l') {
                    $this->long = $value;
                }
                if ($key == 'p') {
                    $this->proportion = ($value > 100 || $value < 0) ? 100 : $value;
                }
            }
        }
    }

    public function __destruct()
    {
        parent::__destruct();
        unset($this->long);
        unset($this->proportion);
        unset($this->zoomMode);
        unset($this->color);
    }

    public function getLong() : int
    {
        return $this->long;
    }

    public function getProportion() : int
    {
        return $this->proportion;
    }

    public function getZoomMode() : string
    {
        return $this->zoomMode;
    }

    public function getColor() : string
    {
        return $this->color;
    }


    public function outPut() : array
    {
        $zoomMode = $this->getZoomMode();
        $image = $this->getImage();
        $editor = $this->getEditor();
        switch ($zoomMode) {
            case 'pad':
                list($editor, $image) = $this->pad();
                break;
            case 'fill':
                list($editor, $image) = $this->fill();
                break;
            case 'fixed':
                list($editor, $image) = $this->fixed();
                break;
            case 'lfit':
                list($editor, $image) = $this->lfit();
                break;
        }

        //按长边缩放
        //保证图片较长的一边不超过200px，等比缩放，缩放后不填充背景
        if (!empty($this->getLong())) {
            $long = $this->getLong();
            $editor = $this->getEditor();
            $editor->resizeFit($image, $long, $long);
            return [$editor, $image];
        }

        if ((!empty($this->getWidth()) || !empty($this->getHeight())) && empty($this->getZoomMode())) {
            $width = $this->getWidth();
            $height = $this->getHeight();

            $long = !empty($width) ? $width : (!empty($height) ? $height : 0);
            $editor = $this->getEditor();
            $editor->resizeFit($image, $long, $long);
            return [$editor, $image];
        }


        //按比例缩放
        if (!empty($this->getProportion())) {
            $imgWidth = $image->getWidth();
            $imgHeight = $image->getHeight();
            $proportion = $this->getProportion();

            $width = $imgWidth*($proportion/100);
            $height = $imgHeight*($proportion/100);

            $editor = $this->getEditor();
            $editor->resizeExact($image, $width, $height);
            return [$editor, $image];
        }

        return [$editor, $image];
    }


    // 居中剪裁。
    // 就是把较短的变缩放到200px，然后将长边的大于200px的部分居中剪裁掉，图片不会变形。
    protected function fill() : array
    {
        $image = $this->getImage();
        $editor = $this->getEditor();
        $editor->resizeFill($image, $this->getWidth(), $this->getHeight());
        return [$editor, $image];
    }

    //固定宽高缩放,会变形
    protected function fixed() : array
    {
        $image = $this->getImage();
        $editor = $this->getEditor();
        $editor->resizeExact($image, $this->getWidth(), $this->getHeight());
        return [$editor, $image];
    }

    //按宽高缩放缩放，会变形
    protected function lfit() : array
    {
        $image = $this->getImage();
        $width = $this->getWidth();
        $height = $this->getHeight();
        $editor = $this->getEditor();
        if (!empty($width)) {
            $editor->resizeExactWidth($image, $width);
        }

        if (!empty($height)) {
            $editor->resizeExactHeight($image, $height);
        }
        return [$editor, $image];
    }

    //固定宽高，缩放填充
    public function pad() : array
    {
        $resizeWidth = $this->getWidth();
        $resizeHeight = $this->getHeight();
        $imageObj = $this->getImage();

        if (empty($resizeWidth) || empty($resizeHeight)) {
            return $imageObj;
        }

        $editor = $this->getEditor();

        $blankEditor = $resultEditor = $this->getBlankEditor();
        $blackImage = $this->getBlankImage($resizeWidth, $resizeHeight);

        $blankEditor->fill($blackImage, new Color($this->getColor()));

        $imageW = $imageObj->getWidth();
        $imageH = $imageObj->getHeight();

        if ($resizeWidth > $imageW) {
            $resizeWidth = $imageW;
        }
        if ($resizeHeight > $imageH) {
            $resizeWidth = $imageH;
        }

        //保存的相对路径
        $savePath = ImageFactory::IMAGE_TMP_PATH.'/'.date('Y').'/'.date('m');
        //创建文件夹
        $this->createDirectory($savePath);
        //获取绝对路径
        $realPath = $this->getFileAbsolutePath($savePath);
        //最终保存的文件名称
        $fileName = $realPath . '/' .basename($imageObj->getImageFile());

        $editor->resizeFit($imageObj, $resizeWidth, $resizeHeight)
            ->blend($blackImage, $imageObj, 'normal', 1, 'center')
            ->save($blackImage, $fileName)
            ->free($imageObj);
        $blankEditor->free($blackImage);

        $editor = $resultEditor->open($image, $fileName);

        return [$editor,$image];
    }
}
