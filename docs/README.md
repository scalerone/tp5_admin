## 1.public下.htaccess重写规则

### phpstudy配置

```
<IfModule mod_rewrite.c> 
Options +FollowSymlinks -Multiviews 
RewriteEngine on 
RewriteCond %{REQUEST_FILENAME} !-d 
RewriteCond %{REQUEST_FILENAME} !-f 
RewriteRule ^(.*)$ index.php [L,E=PATH_INFO:$1] 
</IfModule>
```

### wamp配置


```
<IfModule mod_rewrite.c>
  Options +FollowSymlinks -Multiviews
  RewriteEngine On
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]
</IfModule>
```


## 2.域名配置


```
<VirtualHost *:80>
   
   DocumentRoot "E:/workspace/tp5_admin/public/"
   ServerName admin.tpdemo.com

</VirtualHost>
<VirtualHost *:80>
   
   DocumentRoot "E:/workspace/tp5_admin/public/"
   ServerName www.tpdemo.com

</VirtualHost>
<VirtualHost *:80>
   
   DocumentRoot "E:/workspace/tp5_admin/upload/"
   ServerName static.tpdemo.com
<Directory "E:/workspace/wh_demo/upload/"> 
     Options FollowSymLinks 
    AllowOverride all
    Order allow,deny
    Allow from all
    DirectoryIndex index.html index.php
    </Directory>
</VirtualHost>
```

##  3.数据库问题
### 支持mysql5.7
会报Disable ONLY_FULL_GROUP_BY的错误
[错误及解决办法点我](http://stackoverflow.com/questions/23921117/disable-only-full-group-by)


1. vi  /etc/mysql/my.cnf
1. Add this to the end of the file

```
[mysqld]  
sql_mode = "STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"
```
1. sudo service mysql restart to restart MySQL

##  4.workerman的调试和访问
vendor下的workerman目录已经覆盖为window版，本地调试时切换到
项目根目录tp5_admin
window命令行输入命令
```
php server.php /home/worker
```