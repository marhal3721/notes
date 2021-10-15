<?php


namespace Image\ControllerOld;

use Grafika\Color;
use Grafika\Grafika;
use Marmot\Basecode\Classes\Request;
use Marmot\Core;

trait ImageTrait
{
    abstract public function getRequest()  : Request;

    //获取请求资源路径
    public function getResourcePath($resource = '') : array
    {
        $requestResource = $this->getRequest()->get('resource', '');
        $rootPath = APP_ROOT. 'public';

        $resource = empty($resource) ? $requestResource : $resource;

        return  [$resource, $rootPath . $resource];
    }

    //获取请求参数
    protected function getProcess() : array
    {
        $process = $this->getRequest()->get('x-qxy-process');

        $params = explode('/', $process);
        $fileCategory = array_shift($params);

        $info = [
            'type' => $fileCategory,
            'resize' => [],//压缩
            'quality' => [],//质量转换
            'watermark' => [],//水印
            'crop' => [],//裁剪
            'auto-orient' => [],//自适应方向
            'format' => [],//格式转换
            'rotate' => [],//旋转
        ];

        foreach ($params as $param) {
            $process = explode(',', $param);
            $type = array_shift($process);
            if ($type == 'resize') {
                $info['resize'] = $process;
            }
            if ($type == 'quality') {
                $info['quality'] = $process;
            }
            if ($type == 'watermark') {
                $info['watermark'] = $process;
            }
            if ($type == 'crop') {
                $info['crop'] = $process;
            }
            if ($type == 'auto-orient') {
                $info['auto-orient'] = $process;
            }
            if ($type == 'format') {
                $info['format'] = $process;
            }
            if ($type == 'rotate') {
                $info['rotate'] = $process;
            }
        }

        return $info;
    }
}
