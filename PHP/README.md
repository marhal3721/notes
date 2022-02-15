# 日常笔记

---

## 目录

- [获取前12个月全部月份](#获取前12个月全部月份)
- [下载文件](#下载文件)
- [获取两个时间区间的月份](#获取两个时间区间的月份)
- [mysql单个插入](#mysql单个插入)
- [mysql更新](#mysql更新)
- [mysql批量更新](#mysql批量更新)

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

## mysql单个插入

```php
/**
 * 单个插入
 * @param $table string 表名
 * @param array $data 数据 [field=>value]
 * @return string
 */
function getInsertSql(string $table, array $data): string
{
    $cols = $colsStr = $colsArr = array();
    $vals = $valsStr = $valsArr = array();
    foreach ($data as $key => $val) {
        if (is_array($val)) {
            $colsArr[] = '`' . $key . '`';
            $valsArr[] = "'" . json_encode($val, JSON_UNESCAPED_UNICODE) . "'";
        } else {
            $colsStr[] = '`' . $key . '`';
            if ($val === NULL) {
                $valsStr[] = NULL;
            }else{
                $valsStr[] = "'" . trim(addslashes($val)) . "'";
            }
        }
    }
    $cols = array_merge($colsStr, $colsArr);
    $vals = array_merge($valsStr, $valsArr);

    //使用implode PHP打印数组中的NULL字段
    $vals = array_map(function ($val) { return $val != null ? $val : 'null';}, $vals);

    $sql = "INSERT INTO `{$table}` (";
    $sql .= implode(",", $cols) . ") VALUES (";
    $sql .= implode(",", $vals) . ")";

    return $sql;
}
```

## mysql更新
```php
/**
 * 简化操作for update
 * @param $table string 表名
 * @param array $data 更新的数据 [字段=>值]
 * @param string $whereSqlArrOrStr where条件，可以是数组也可以是字符串
 * @return string
 * @remark 没有处理 is not null/is null/in /等非=操作
 */
function getUpdateSql(string $table, array $data, $whereSqlArrOrStr = ""): string
{

    $set = array();
    foreach ($data as $key => $val) {
        if (is_array($val)) {
            $set[] = $key . "='" . json_encode($val, JSON_UNESCAPED_UNICODE) . "'";
        } else {
            $set[] = $key . "='" . trim(trim(addslashes($val))) . "'";
        }
    }

    $where = $comma = '';
    if (empty($whereSqlArrOrStr)) {
        $where = '1';
    } elseif (is_array($whereSqlArrOrStr)) {
        foreach ($whereSqlArrOrStr as $key => $value) {
            $where .= $comma . '`' . $key . '`' . '=\'' . $value . '\'';
            $comma = ' AND ';
        }
    } else {
        $where = $whereSqlArrOrStr;
    }

    $sql = "UPDATE `{$table}` SET ";
    $sql .= implode(",", $set);
    $sql .= " WHERE " . $where;
    return $sql;
}
```

## mysql批量更新
```php
/**
 * 获取批量插入的sql
 * @param $table 表名
 * @param array $fields 字段
 * @param array $data 数据 字段=>value
 * @return string
 */
function getInsertAllSql($table, array $fields, array $data)
{

    $sqls = '';
    foreach ($data as $v) {
        $tmp = [];
        foreach ($fields as $field) {
            if (isset($v[$field])) {
                $tmp[$field] = $v[$field];
            }
        }

        if (!empty($tmp)) {
            $sqls .= getInsertSqlForValue($tmp);
        }
    }
    foreach ($fields as &$field) {
        $field = '`' . $field . '`';
    }

    $sql = "INSERT INTO `{$table}` (";
    $sql .= implode(",", $fields) . ") VALUES ";
    $sql .= $sqls;
    return rtrim($sql, ',');

}

/**
 * getInsertAllSql的辅助函数
 * @param array $data
 * @return string
 */
function getInsertSqlForValue(array $data): string
{

    $valsStr = $valsArr = array();
    foreach ($data as $val) {

        if (is_array($val)) {
            $valsArr[] = "'" . json_encode($val, JSON_UNESCAPED_UNICODE) . "'";
        } else {
            $valsStr[] = "'" . trim(addslashes($val)) . "'";
        }
    }

    $vals = array_merge($valsStr, $valsArr);

    return "(" . implode(",", $vals) . "),";

}
```