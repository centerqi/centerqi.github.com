---
layout: post
category : java 
tags : [java]
---
{% include JB/setup %}

看java 并发这一块，用到了不少多线程，正好读azkaban的源码也读到这一志。
记录之，以防后来查询。


起因的代码如下

1、为什么能在主进程之中做Thread.sleep()呢？

原来应该是这样理解的

jvm应该是一个进程，main方法，仅仅是一个线程（有人也叫主线程），因此，调用 Thread.sleep()，也能对Main这个线程进行sleep

其实也可以获取 main线程的相关信息



    Thread t = Thread.currentThread();
    System.out.println("Current thread: " + t);


Thread.currentThread 就能获取本线程的相关信息



    public class SleepMessages {

        public static void main(String[] args)  throws InterruptedException{

            String importantInfo[] = {
                    "Mares eat oats",
                    "Does eat oats",
                    "Little lambs eat ivy",
                    "A kid will eat ivy too"
            };
            
            for (int i = 0; i < importantInfo.length; i++) {
                Thread.sleep(4000);
                System.out.println(importantInfo[i]);
            }
        }

    }

