---
layout: post
category : java 
tags : [java]
---
{% include JB/setup %}


公司有A，B机房，一般大一点的公司，都会做容灾，A机房的一个程序要调用INTERNET的POP3服务，不知道为什么，去A机房的线路被挖断了，但是A,B机房通过内网是可以通的。



用nginx的mail模块，做了一个代理，让A机房的评论服务先访问B机房的NGINX认证模块，因为B机房是可以通外网的。




总结如下，说不定哪天又被挖断了，挖断了，没一天是服务没法正常的。





auth_http是发送接收http请求的脚本，让这个pop3auth.php访问外网。




nginx配置如下





    imap {
            auth_http  10.2.8.50:8888/pop3auth.php;
            pop3_capabilities  "TOP"  "USER";
            imap_capabilities  "IMAP4rev1"  "UIDPLUS";

            server {
                    listen     8110;
                    protocol   pop3;
                    proxy      on;
            }

            server {
                    listen     8143;
                    protocol   imap;
                    proxy      on;
            }
    }




php代码如下




    $username=$_SERVER["HTTP_AUTH_USER"] ;
    $userpass=$_SERVER["HTTP_AUTH_PASS"] ;
    $protocol=$_SERVER["HTTP_AUTH_PROTOCOL"] ;

    header_remove("X-Powered-By");

    include('auth_pop3.php');
    if (auth_pop3::authValidateUser($username,$userpass)){
        pass();
    }else{
        fail();
    }


    function fail(){
        header("Auth-Status: Invalid login or password");
    }

    function pass(){
        header("Auth-Status: +OK");
        //header("Auth-Server: $server");
        //header("Auth-Port: $port");
    }


[nginx 安装imap模块](http://wiki.nginx.org/NginxImapProxyExample 'nginx 安装imap 模块')
[nginx imap 认证](http://wiki.nginx.org/ImapAuthenticateWithApachePhpScript 'nginx imap 认证')


