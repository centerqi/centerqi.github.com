---
layout: post
category : linux
tags : [c]
---
{% include JB/setup %}
1、所有未加 static 的函数与变量都是全局可见的。
2、如果加了 static 就会对齐它源文件隐藏,以下代码是编译无法通过的  

main.c 文件



        #include "header.h"
        int main()
        {
                printMsg("hello static");
                return 0;
        }

header.h 文件

        #include <stdio.h>

        static void printMsg(char *msg);


msg.c 文件  


        #include "header.h"
        void printMsg(char *msg){
                printf("%s\n", msg);
        }

gcc msg.c man.c 的时候，是会报错的，找不到printMsg函数 

[static wiki](http://de.wikibooks.org/wiki/C-Programmierung:_static_%26_Co. 'static wiki')  
[static 修饰词](http://www.csie.nctu.edu.tw/~skyang/static.zhtw.htm 'static 修饰词')  
[static inline](http://stackoverflow.com/questions/7762731/whats-the-difference-between-static-and-static-inline-function 'static inline')  
[static code](http://codingfreak.blogspot.com/2010/06/static-functions-in-c.html 'static code')  


