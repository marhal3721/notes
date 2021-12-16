# 日常笔记

---

## 目录

- [获取前12个月全部月份](#获取前12个月全部月份)
- [下载文件](#下载文件)
- [获取两个时间区间的月份](#获取两个时间区间的月份)

## <a name="获取前12个月全部月份">获取前12个月全部月份</a>

```php
//包含本月
$months = array();
for ($offset = 0; $offset < 12; $offset++) {
    $months[] = date('Y-m', strtotime(date( 'Y-m-01' ).'-'.$offset.' months'));
}

//不包含本月
$months = array();
for ($offset = 1; $offset <= 12; $offset++) {
    $months[] = date('Y-m', strtotime(date( 'Y-m-01' ).'-'.$offset.' months'));
}
```

## <a name="下载文件">下载文件</a>
```php
// $filePath为绝对地址
protected function downloadFile(string $filePath)
{
    ob_start();
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($filePath));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
}
```

## <a name="获取两个时间区间的月份">获取两个时间区间的月份</a>

```php
protected function getMonth($startTime = 0, $endTime = 0) : array
{
    $startTime = $startTime > 0 ? $startTime : time();
    $endTime = $endTime > 0 ? $endTime : time();

    $startMonth = date('Y-m',$startTime);
    $months = array();
    $months[] = $startMonth; // 不需要开始月时不需要此行;
    while( ($startTime = strtotime('+1 month', $startTime)) <= $endTime){
        $months[] = date('Y-m',$startTime);
    }

    return $months;
}

```