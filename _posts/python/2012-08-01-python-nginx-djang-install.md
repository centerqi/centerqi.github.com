---
layout: post
category : python
tags : [python]
---
{% include JB/setup %}

一直用python处理一些文本，还没用python写过web程序，感觉python写web程序比较麻烦，还得依赖于很多框架，这是我最不喜欢的，因为框架  
就得让你去按它的规矩办事，让你失去了一些自由, php不存在这种情况，用 fastcgi的话，写几行代码扔上去，就可以了  

想了解python web 方面的一些知识，首先搭建一个环境 python很多机器默认是有的，只是版本的问题， python 的web框架我选择了 django, 原因很简单  
自己多一点  


因为不喜欢用apt或者yum之类的，所有的东西都喜欢自己make  


#nginx
nginx 的安装 ，先下载，然后解压, 因为 nginx如果要支持rewrite的话，要pcre库，这个可以先下载安装  
贴一下configure参数  


    ./configure --prefix=/usr/local/webserver/nginx/  --http-log-path=/data/logs/nginx/access_log --error-log-path=/data/logs/nginx/error_log --with-pcre=/home/rd/down/pcre-8.20/ --with-http_realip_module     --with-http_stub_status_module     --without-http_userid_module     --http-client-body-temp-path=/usr/local/webserver/nginx/cache/client_body     --http-proxy-temp-path=/usr/local/webserver/nginx/cache/proxy    --http-fastcgi-temp-path=/usr/local/webserver/nginx/cache/fastcgi     --http-uwsgi-temp-path=/usr/local/webserver/nginx/cache/uwsgi     --http-scgi-temp-path=/usr/local/webserver/nginx/cache/scgi --pid-path=/data/logs/nginx/nginx.pid

#uwsgi
uwsgi是必须安装的，这就是麻烦的地方，不过安装很简单 

    python uwsgiconfig.py --build
    python setup.py install

#django
django安装也是行命令  

    python setup.py install

#nginx配置
监听商品是 8012, app所在的目录为/data/server/t.app/, app的名字为 www
这样配置有一个好处，就是可以配置虚拟主机


        server {
                listen       8012;
                location / {
                include uwsgi_params;
                uwsgi_pass 127.0.0.1:9099;
                uwsgi_param UWSGI_PYHOME /data/server/t.app/www;  
                uwsgi_param UWSGI_SCRIPT wsgi;  
                uwsgi_param UWSGI_CHDIR /data/server/t.app/www;  
                }
        }

重启nginx 

    nginx -s reload


#生成站点

    django-admin.py startproject www 

有一点比较不爽，它会生成www/www/两个文件夹  
    
    cp ./www/www/* /data/server/t.app/wwww/  

#启动uwsgi

    uwsgi -s 127.0.0.1:9099 -d uwsgi.log --vhost


搭建完毕，访问http://xxx.xx.xx.xxx:8012/www 会出现一个如下页面

>It worked!
>Congratulations on your first Django-powered page.

