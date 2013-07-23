---
layout: post
category : python
tags : [python]
---
{% include JB/setup %}

放假在家，看了一下 goagent 的代码，发现代码写的还是很不错的，  
感觉最重要的一点就是 DNS 解析部分，看完代码后，也纠正了我很多错误的认识  
xxx.appspot.com 是无法直接访问的，但是他的 ip 是没有墙的  

goagent 基本的原理很简单，先从dns server 解析某 xxx.appspot.com 的ip  
然后建立一个http（https）的连接，连接的内容是加密了的，这样可以不被墙  

#dns解析
特意看了一下dns 协议包，在 tcp/ip详解 第一卷 里面有详细的介绍  

        import sys
        import os
        import struct
        import socket
        import re

        host = 'www.baidu.com'
        host = 'www.facebook.com'
        host = 'centerqi.appspot.com'

        index = os.urandom(2)
        hoststr = ''.join(chr(len(x))+x for x in host.split('.'))
        data = '%s\x01\x00\x00\x01\x00\x00\x00\x00\x00\x00%s\x00\x00\x01\x00\x01' % (index, hoststr)
        data = struct.pack('!H', len(data)) + data
        address_family = socket.AF_INET
        sock = None

        dnsserver = '8.8.8.8'
        sock = socket.socket(family=address_family)
        sock.connect((dnsserver, 53))
        sock.sendall(data)
        rfile = sock.makefile('rb')
        size = struct.unpack('!H', rfile.read(2))[0]
        data = rfile.read(size)
        ips = ['.'.join(str(ord(x)) for x in s) for s in re.findall('\xC0.\x00\x01\x00\x01.{6}(.{4})', data)]

        print ips

[dns pack formate](http://www.tcpipguide.com/free/t_DNSMessageHeaderandQuestionSectionFormat.htm)
