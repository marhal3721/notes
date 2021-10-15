<?php


namespace Image\Model;

use Image\ImageTrait;

class WaterImage extends Image
{
    use ImageTrait, WaterImageTrait;

    private $text;//水印文字
    private $color;//水印文字颜色
    private $size;//水印文字大小

    private $waterImg;//水印图片
    private $fill;//是否铺满 0 否 1是
    private $transparency;//水印透明度
    private $xMargin;//水平边距
    private $yMargin;//垂直边距
    private $position;//位置
    private $resizeType;//水印图片压缩类型
    private $rotate;//水印旋转

    public function __construct(array $process, $editor, $image)
    {
        parent::__construct($process, $editor, $image);

        $this->text = '';
        $this->waterImg = '';
        $this->color = '';

        $this->size = 0;//文字大小
        $this->fill = ImageFactory::WATER_IS_FULL['NO'];
        $this->transparency = 1.0;//透明度
        $this->xMargin = 0;
        $this->yMargin = 0;
        $this->position = ImageFactory::OSS_QXY_POSITION_MAP['nw'];
        $this->resizeType = 'fixed';
        $this->rotate = 0;
        $this->getQuota($process);
    }

    public function __destruct()
    {
        parent::__destruct();
        unset($this->text);
        unset($this->color);
        unset($this->size);
        unset($this->waterImg);
        unset($this->fill);
        unset($this->transparency);
        unset($this->xMargin);
        unset($this->yMargin);
        unset($this->position);
        unset($this->resizeType);
        unset($this->rotate);
    }

    //获取图片压缩参数
    protected function getQuota(array $process) : void
    {
        $image = $this->getImage();
        $imageHeight = $image->getHeight();
        $imageWidth = $image->getWidth();

        $this->width = ($this->width == 0 ? $imageWidth : $this->width);
        $this->height = ($this->height == 0 ? $imageHeight : $this->height);

        foreach ($process as $quota) {
            $quotas = explode('_', $quota);
            if (count($quotas) == 2) {
                list($key,$value) = $quotas;
                if ($key == 'w') {
                    $this->width = $value;
                }
                if ($key == 'h') {
                    $this->height = $value;
                }
                if ($key == 'text') {
                    $this->text = base64_decode($value);
                }
                if ($key == 'image') {
                    $this->waterImg = base64_decode($value);
                }
                if ($key == 'color') {
                    $this->color = $value;
                }
                if ($key == 'size') {
                    $this->size = $value;
                }
                if ($key == 'fill') {
                    $this->fill = $value;
                }
                if ($key == 't') {
                    $this->transparency = $value <= 0 ? 1 : $value;
                }
                if ($key == 'x') {
                    $this->xMargin = $value;
                }
                if ($key == 'y') {
                    $this->yMargin = $value;
                }
                if ($key == 'g') {
                    $this->position = ImageFactory::OSS_QXY_POSITION_MAP[$value] ?? $this->position;
                }
                if ($key == 'm') {
                    $this->resizeType = $value;
                }
                if ($key == 'rt') {
                    $this->rotate = $value;
                }
            }
        }
    }

    /**
     * 压缩水印图片
     */
    protected function resizeWaterImage() : array
    {
        $blankEditor = $this->getBlankEditor();
        $absolutePath = $this->getFileAbsolutePath($this->getWaterImg());

        if (file_exists($absolutePath)) {
            $blankEditor->open($waterImg, $this->getFileAbsolutePath($this->getWaterImg()));

            $width = $this->getWidth();
            $height = $this->getHeight();

            if (!empty($width) || !empty($height)) {
                $resize = [];
                if (!empty($width)) {
                    $resize[] = 'w_' . $width;
                }
                if (!empty($height)) {
                    $resize[] = 'h_' . $height;
                }
                if (!empty($resizeType)) {
                    $resize[] = 'm_' . $this->getResizeType();
                }

                $resizeObj = new ResizeImage($resize, $blankEditor, $waterImg);

                return $resizeObj->outPut();
            }
        }

        return array();
    }

    public function outPut(): array
    {
        $waterImage = $this->resizeWaterImage();

        $editor = $this->getEditor();
        $image = $this->getImage();

        if (!empty($waterImage)) {
            list($waterEditor, $waterImg) = $waterImage;
            # 图片水印铺满
            if ($this->getFill() == ImageFactory::WATER_IS_FULL['YES']) {
                //保存的相对路径
                $savePath = ImageFactory::IMAGE_TMP_PATH.'/'.date('Y').'/'.date('m').'/'.date('d-H:i:s');
                //创建文件夹
                $this->createDirectory($savePath);
                //获取绝对路径
                $realPath = $this->getFileAbsolutePath($savePath);
                //最终保存的水印文件名称
                $waterFileName = $realPath . '/' . basename($waterImg->getImageFile());
                //最终保存的压缩文件名称
                $resizeFileName = $realPath . '/' . basename($image->getImageFile());

                //保存原图
                $editor->save($image, $resizeFileName)->free($image);
                //保存水印
                $waterEditor->save($waterImg, $waterFileName)->free($waterImg);

                $this->fillPicWalter($resizeFileName, $waterFileName, $this->getRotate(), $this->getTransparency());
            }

            # 单个图片水印
            // 合并图片
            $editor->blend(
                $image,
                $waterImg,
                'normal',
                $this->getTransparency(),
                $this->getPosition(),
                $this->getXMargin(),
                $this->getYMargin()
            );

            $waterEditor->free($waterImg);
        }

        return array($editor,$image);
    }
}
