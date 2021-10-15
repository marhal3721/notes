<?php

namespace Image\ControllerOld;

use Marmot\Framework\Classes\Controller;
use Grafika\Grafika;

class ImageOperationController extends Controller
{
    use ImageTrait;
    use ImageResizeTrait;
    use ImageAddImgWaterTrait;
    use ImageCropTrait;

    public function cutImage()
    {
        $process = $this->getProcess();

        $fileCategory = $process['type'];

        if ($fileCategory == 'image') {
            # 单压缩输出
            if (!empty($process['resize']) && empty($process['watermark'])) {
                $this->resize($process);
            }

            # 处理水印
            if (!empty($process['watermark'])) {
                $this->water($process);
            }

            # 裁剪
            if (!empty($process['crop'])) {
                $this->crop($process);
            }

            list($resource, $path) = $this->getResourcePath();
            unset($resource);

            Grafika::createEditor()->open($image, $path);
            header('Content-type: image/png');
            header('Cache-control: max-age=300');
            $image->blob('PNG');
        }
    }
}
