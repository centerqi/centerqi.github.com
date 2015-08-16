---
layout: post
category : linux
tags : [arduino]
---
{% include JB/setup %}

在淘宝上买了一块arduino的板子，在mac上找不到驱动，google了很久，发现usb转串口用的是ch341系列的技术。

知道这个就好办了.

第一步，下载驱动 

[驱动下载](http://www.wch.cn/download/CH341SER_MAC_ZIP.html 'http://www.wch.cn/download/CH341SER_MAC_ZIP.html')


第二步，运行命令

    sudo nvram boot-args="kext-dev-mode=1"


第三步，重启系统。

发现国人的芯片水平还是相当丰富的，我在淘宝店买的，最后问卖家，卖家对自己的板子的相关芯片非常清楚。

我特意查了ch341系列的芯片，发现都是国产的，为什么命名为 ch341，是国标吗？还是没搞清楚。


好像几个老外也买了便宜的国产芯片，也遇到了我相同的问题

[http://0xcf.com/2015/03/13/chinese-arduinos-with-ch340-ch341-serial-usb-chip-on-os-x-yosemite/](http://0xcf.com/2015/03/13/chinese-arduinos-with-ch340-ch341-serial-usb-chip-on-os-x-yosemite/ 'http://0xcf.com/2015/03/13/chinese-arduinos-with-ch340-ch341-serial-usb-chip-on-os-x-yosemite/')
