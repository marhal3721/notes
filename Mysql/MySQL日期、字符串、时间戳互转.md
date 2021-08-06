# MySQL日期、字符串、时间戳互转

* 时间转字符串
```sql
select date_format(now(), '%Y-%m-%d');
# 2021-08-05  
```

* 时间转时间戳
```sql
select unix_timestamp(now());  
  
# 1628226999 
```

* 字符串转时间
```sql
select str_to_date('2021-08-05 12:12:12', '%Y-%m-%d %H:%i:%s') 
  
# 2021-08-05 12:12:12
```

* 字符串转时间戳
```sql
select unix_timestamp('2021-08-05');  
  
# 1628092800 
```

* 格式化时间戳
```sql
select from_unixtime(unix_timestamp(now()),'%Y-%m-%d %H:%i:%s');  
  
# 2021-08-06 13:21:21
```

* 