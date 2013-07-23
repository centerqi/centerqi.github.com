---
layout: post
category : python
tags : [python]
---
{% include JB/setup %}

上次配置django nginx的时候，感觉配置有些问题，今天重新搜集了一些资料，整理了一下  

#nginx
可以配置简单一点，把nginx变成一个透明的转发就可以了  
只是配置了一个转发功能  

        server {
          listen 8013;
          server_name yourname.com;
          location / {
            uwsgi_pass 127.0.0.1:9098;
            include uwsgi_params;
          }
        }

#uwsgi
把一些与application相关的功能，放到uwsgi这一块来配置  

    wsgi -s 127.0.0.1:9098 -d uwsgi1.log --chdir /data/server/t.app/nav/ --module nav.wsgi:application

#django
django的目录如下
|-nav
   |---nav



[django nginx configure](https://docs.djangoproject.com/en/dev/howto/deployment/wsgi/uwsgi/)
[django startproject](https://docs.djangoproject.com/en/dev/ref/django-admin/#django-admin-startproject)  
[django setup](https://gist.github.com/3094281)
