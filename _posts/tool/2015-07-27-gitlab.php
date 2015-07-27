---
layout: post
category : tool 
tags : [tool]
---
{% include JB/setup %}


朋友是一家创业公司的技术带头人，人不多，开发大概就8个人左右，我看他们公司在用gitlab这东东做代码管理，自己亲自体验了一下，发现确实不错。

他的经历是这样的，当只有他自己写代码的时候，代码托管到了bitbucket，唯一的好处是能建私有的项目，差出是服务器在国外，速度很慢，偶尔访问不了。

成员达到6时，发现bitbucket要收费，有最大帐号限制，说价格也不贵，本来打算花钱买帐号，但是大家感觉速度太慢，严重影响效率，还有就是开发机的帐号，

管理开发机帐号也很头痛，所以就打算用gitlab+ldap这一套来处理。


ldap很方便的管理开发机的帐号认证，gitlab很方便的来管理代码。


我自己特意搭建了一个，搭建过程中遇到的问题总结如下。

1. 其实官网的说明已经很不错了，就是在运行如下命令的时候，下载速度只有几k,我特意找到他的下载连接，先从日志下载，发现下载能到 10mb，然后再copy回中国的服务器。

    sudo yum install gitlab-ce

2. ldap的设置，默认的配置文件在 

    /etc/gitlab/gitlab.rb 

    设置代码如下
        
    label: 'LDAP'
    host: '127.0.0.1'
    port: 389
    uid: 'uid'
    method: 'plain' # "tls" or "ssl" or "plain"
    bind_dn: 'cn=admin,ou=People,dc=testname,dc=com'
    password: 'youpassword'
    allow_username_or_email_login: true
    base: 'dc=testname,dc=com'
    user_filter: ''


3. 更改gitlab的默认端口

    external_url 'http://idc02-test-task-02:1700'

4. 更改root密码

    sudo gitlab-rails console


    user = User.find_by(email: 'admin@local.host')

    user.password = 'secret_pass'

    user.password_confirmation = 'secret_pass'

    user.save

5. 如果ldap用户第一次登入，会被bloked，管理员得unbloked后，才能登入。



