<?php


namespace Image\ControllerOld;

use Grafika\Color;
use Grafika\Grafika;
use Marmot\Core;

trait ImageResizeTrait
{
    abstract protected function getResourcePath($resource = '') : array;

    //获取图片压缩参数
    protected function getImageResizeQuota(array $process) : array
    {
        //宽、高、长边、比例
        $width = $height = $long = $proportion = 0;
        //压缩类型
        $resizeType = '';
        //颜色
        $color = '#fff';
        foreach ($process as $quota) {
            list($key,$value) = explode('_', $quota);
            if ($key == 'm') {
                $resizeType = $value;
            }
            if ($key == 'h') {
                $height = $value;
            }
            if ($key == 'w') {
                $width = $value;
            }
            if ($key == 'color') {
                $color = '#' . $value;
            }
            if ($key == 'l') {
                $long = $value;
            }
            if ($key == 'p') {
                $proportion = ($value > 100 || $value < 0) ? 100 : $value;
            }
        }

        return [$resizeType,$width, $height, $color, $long, $proportion];
    }

    /**
     * @param string $output show 0 渲染输出到浏览器|save 1 保存并返回路径
     */
    protected function resize(array $process, $output = 0, $file = '')
    {
        $editor = Grafika::createEditor();

        if (empty($file)) {
            list($file, $path) = $this->getResourcePath();
        }

        if (!empty($file)) {
            list($file, $path) = $this->getResourcePath($file);
        }

        if (!file_exists($path)) {
            Core::setLastError(RESOURCE_NOT_EXIST);
            return false;
        }


        if ($output == 1) {
            $file = ltrim($file, '/');
            $file = str_replace('/', '_', $file);

            $fileName = '_resize_' . implode(',', $process['resize']) . '_' . $file;
            $fileName = ltrim($fileName, '_');
            $filePath = '/img/tmp/' . $fileName;

            list($fileTmpPath, $savePath) = $this->getResourcePath($filePath);

            if (file_exists($savePath)) {
                return $fileTmpPath;
            }
        }

        $editor->open($image, $path);
        $outImage = $image;
        //获取指定宽高和颜色
        list($resizeType,$width, $height, $color, $long, $proportion) =
            $this->getImageResizeQuota($process['resize']);

        //固定宽高，缩放填充
        if ($resizeType == 'pad') {
            $blankEditor = Grafika::createEditor();
            $blackImage = Grafika::createBlankImage($width, $height);
            $blankEditor->fill($blackImage, new Color($color));

            $imageW = $image->getWidth();
            $imageH = $image->getHeight();
            $resizeWidth = $width;
            $resizeHeight = $height;
            if ($width > $imageW) {
                $resizeWidth = $imageW;
            }
            if ($height > $imageH) {
                $resizeWidth = $imageH;
            }
            $editor->resizeFit($image, $resizeWidth, $resizeHeight)
                ->blend($blackImage, $image, 'normal', 1, 'center');

            $outImage = $blackImage;
            $editor->free($image);
        }

        // 居中剪裁。
        // 就是把较短的变缩放到200px，然后将长边的大于200px的部分居中剪裁掉，图片不会变形。
        if ($resizeType == 'fill') {
            $editor->resizeFill($image, 100, 100);
            $outImage = $image;
        }

        //固定宽高缩放,会变形
        if ($resizeType == 'fixed') {
            $editor->resizeExact($image, $width, $height);
            $outImage = $image;
        }

        //按宽高缩放缩放，会变形
        if ($resizeType == 'lfit') {
            if (!empty($width)) {
                $editor->resizeExactWidth($image, $width);
            }

            if (!empty($height)) {
                $editor->resizeExactHeight($image, $height);
            }
            $outImage = $image;
        }

        //按长边缩放
        //保证图片较长的一边不超过200px，等比缩放，缩放后不填充背景
        if (!empty($long)) {
            $editor->resizeFit($image, $long, $long);
            $outImage = $image;
        }

        if ((!empty($width) || !empty($height)) && empty($resizeType)) {
            $long = !empty($width) ? $width : (!empty($height) ? $height : 0);
            $editor->resizeFit($image, $long, $long);
            $outImage = $image;
        }

        //按比例缩放
        if (!empty($proportion)) {
            $imgWidth = $image->getWidth();
            $imgHeight = $image->getHeight();

            $width = $imgWidth*($proportion/100);
            $height = $imgHeight*($proportion/100);
            $editor->resizeExact($image, $width, $height);
            $outImage = $image;
        }

        if ($output == 0) {
            //输出原图
            header('Content-type: image/png');
            header('Cache-control: max-age=20');
            $outImage->blob('PNG');
            unset($editor);
        }

        if ($output == 1) {
//            //保存并返回路径
//
//            $file = ltrim($file,'/');
//            $file = str_replace('/','_',$file);
//
//            $fileName = '_resize_'.implode(',',$process['resize']).'_'.$file;
//            $fileName = ltrim($fileName,'_');
//            $filePath = '/img/tmp/'. $fileName;

            list($file, $savePath) = $this->getResourcePath($filePath);
            unset($file);
            $editor->save($outImage, $savePath);
            $editor->free($outImage);
            unset($editor);
            return $filePath;
        }

        return '';
    }
}
