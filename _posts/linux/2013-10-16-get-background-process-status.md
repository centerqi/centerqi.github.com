---
layout: post
category : linux
tags : [c]
---
{% include JB/setup %}

要获取后台近程的返回值，然后根据退出状态做不同的处理。

hadoop跑任务经常挂，但是以前没有监控，还得一个一个去手工排查。

hadoop job 有一个特点，就是不知道他什么时候开始去调度执行，也不知道什么时候能跑完，所以把监控加到哪个地方都不太好

发现就是 hadoop 执行完成后，他的返回状态可以知道是否执行成功了。

就加到他的执行完成的后面，但是都会放后台去执行，同事告诉我一个写法，比较省事。

execstatus 是一个执行函数，他在执行完成job后，会调用execstatus 

重点是吧启动job的命令与退出的状态一同放到了后台,加了()的原因

shell ()中不能写 if之类的语句，只能写命令

如启动多个任务

    for (( c=0; c < ${countDay}; c++ ))
    do
            mydate="`date --date="$START +$c day" +%Y-%m-%d`"; 
            output=$HDFS_OUTPUT/${mydate}
            /usr/local/webserver/hadoop-1.1.2/bin/hadoop112 dfs -rmr $output
            rm $DATA_PATH/${mydate}  -rf

            echo ${mydate}
            (/usr/local/webserver/pig/bin/pig112 -M  \
            -p input="/user/www/hdc/output/meta/${mydate}" \
            -p output="$output" \
            -p exportpath="$DATA_PATH/${mydate}/merge.csv" \
            -p jobname="${MODE_NAME}_${mydate}" $MODE_PATH/$MODE_NAME.pig >/dev/null 2>&1;
            execstatus $? "$output" 
            )&
    done

[shell 中各种括号的用法](http://my.oschina.net/xiangxw/blog/11407 'shell 中各种括号的用法')


