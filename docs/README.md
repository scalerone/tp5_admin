
###  1.数据库问题
#### 1.1 mysql5.7问题
会报Disable ONLY_FULL_GROUP_BY的错误
[错误及解决办法点我](http://stackoverflow.com/questions/23921117/disable-only-full-group-by)


1. vi  /etc/mysql/my.cnf
1. Add this to the end of the file

```
[mysqld]  
sql_mode = "STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"
```
3. sudo service mysql restart to restart MySQL

