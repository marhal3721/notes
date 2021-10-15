<?php

namespace Image;

use Image\Model\ImageFactory;

trait ImageTrait
{
    //获取文件绝对路径
    protected function getFileAbsolutePath(string $fileRelativePath) : string
    {
        return ImageFactory::ROOT_PATH . $fileRelativePath;
    }

    protected function createDirectory($dirRelativePath = '') : bool
    {
        if (empty($dir)) {
            return true;
        }

        $dirRelativePath = rtrim($dirRelativePath, '/');
        @mkdir(ImageFactory::ROOT_PATH.$dirRelativePath, 0777, true);

        return true;
    }
}
