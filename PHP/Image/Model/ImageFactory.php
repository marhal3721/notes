<?php


namespace Image\Model;

interface ImageFactory
{

    //处理后的图片缓存地址
    const IMAGE_TMP_PATH = '/img/tmp';

    //图片浏览器缓存时间
    const CACHE_CONTROL = 300;

    //文件根目录
    const ROOT_PATH = APP_ROOT.'public';

    //阿里云位置与Grafika对照映射关系
    const OSS_QXY_POSITION_MAP = [
        'nw' => 'top-left',//左上
        'north' => 'top-center',//中上
        'ne' => 'top-right',//右上
        'west' => 'center-left',//左中
        'center' => 'center',//中部
        'east' => 'center-right',//右中
        'sw' => 'bottom-left',//左下
        'south' => 'bottom-center',//中下
        'se' => 'bottom-right',//右下
        'smart' => 'smart',
    ];

    //水印是否铺满
    const WATER_IS_FULL = [
        'YES' => 1,
        'NO' => 0
    ];

    const CROP_POSITION_MAP = [
        'nw' => 'top-left',//：左上
        'north' => 'top-center',//：中上
        'ne' => 'top-right',//：右上
        'west' => 'center-left',//：左中
        'center' => 'center',//：中部
        'east' => 'center-right',//：右中
        'sw' => 'bottom-left',//：左下
        'south' => 'bottom-center',//：中下
        'se' => 'bottom-right',//：右下
    ];

    //支持的图片处理类型
    const HANDLER_IMAGE_TYPE = [
        'RESIZE' => 'resize',
        'WATER_MARK' => 'watermark',
        'CROP' => 'crop',
    ];
}
