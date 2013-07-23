---
layout: post
category : linux
tags : [c]
---
{% include JB/setup %}

#proftpd
proftpd 增加用户比 vsftpd方便多了,一行命令就可以搞定
    
    sudo /usr/local/webserver/proftpd/bin/ftpasswd  --passwd --name=sam --uid=3003 --gid=3003 --home=/data/server/t.app/webroot/static/ --shell=/sbin/nologin --file=/usr/local/webserver/proftpd/etc/ftp.passwd

