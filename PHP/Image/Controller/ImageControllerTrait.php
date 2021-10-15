<?php
namespace Image\Controller;

use Marmot\Basecode\Classes\Request;

trait ImageControllerTrait
{
    abstract public function getRequest()  : Request;

    //获取请求参数
    public function getRequestParams():array
    {
        $request = $this->getRequest();

        //接收文件相对路径
        $resource = $request->get('resource', '');
        //处理参数
        $process = $request->get('x-qxy-process', '');

        return array($resource, $process);
    }

    //获取请求参数
    protected function getProcess(string $processString) : array
    {
        $params = explode('/', $processString);

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
