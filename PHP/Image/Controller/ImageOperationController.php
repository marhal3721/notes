<?php

namespace Image\Controller;

use Image\Model\CropImage;
use Image\Model\ResizeImage;
use Image\Model\WaterImage;
use Marmot\Core;
use Marmot\Framework\Classes\Controller;
use Grafika\Grafika;

use Image\View\RenderView;
use Image\ImageTrait;
use Marmot\Framework\Controller\WebTrait;

class ImageOperationController extends Controller
{
    use ImageControllerTrait;
    use ImageTrait;
    use WebTrait;

    public function cutImage()
    {
        list($resource, $processString) = $this->getRequestParams();
        $process = $this->getProcess($processString);

        //打开原图
        $editor = Grafika::createEditor();
        $path = $this->getFileAbsolutePath($resource);
        $editor->open($image, $path);

        $fileCategory = $process['type'];

        if ($fileCategory == 'image') {
            //压缩处理
            if (!empty($process['resize'])) {
                $resize = new ResizeImage($process['resize'], $editor, $image);
                list($editor, $image) = $resize->outPut();
            }
            if (!empty($process['crop'])) {
                $crop = new CropImage($process['crop'], $editor, $image);
                list($editor, $image) = $crop->outPut();
            }

            if (!empty($process['watermark'])) {
                $water = new WaterImage($process['watermark'], $editor, $image);
                list($editor, $image) = $water->outPut();
            }

            $this->getRenderView($image)->render();
            $editor->free($image);
        }



        return false;
    }

    public function getRenderView($image)
    {
        return new RenderView($image);
    }
}
