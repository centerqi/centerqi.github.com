1、cogroup是对多个关系进行group
[root@localhost pig]$ cat a.txt
uidk  12  3
hfd 132 99
bbN 463 231
UFD 13  10
 
 [root@localhost pig]$ cat b.txt
 908 uidk  888
 345 hfd 557
 28790 re  00000

 grunt> A = LOAD 'a.txt' AS (acol1:chararray, acol2:int, acol3:int);
 grunt> B = LOAD 'b.txt' AS (bcol1:int, bcol2:chararray, bcol3:int);
 grunt> C = COGROUP A BY acol1, B BY bcol2;
 grunt> DUMP C;
 (re,{},{(28790,re,0)})
 (UFD,{(UFD,13,10)},{})
 (bbN,{(bbN,463,231)},{})
 (hfd,{(hfd,132,99)},{(345,hfd,557)})
 (uidk,{(uidk,12,3)},{(908,uidk,888)})

 每一行输出的第一项都是分组的key，第二项和第三项分别都是一个包（bag），其中，第二项是根据前面的key找到的A中的数据包，第三项是根据前面的key找到的B中的数据包。
 来看看第一行输出：“re”作为group的key时，其找不到对应的A中的数据，因此第二项就是一个空的包“{}”，“re”这个key在B中找到了对应的数据（28790    re    00000），因此第三项就是包{(28790,re,0)}。
 其他输出数据也类似。


2、可以对2个以后数据集进行分组
    grunt> A = LOAD 'a.lst' AS (platForm, channel, reqType, reqValue);
    grunt> B = LOAD 'a.lst' AS (platForm, channel, reqType, reqValue);
    grunt> C = LOAD 'a.lst' AS (platForm, channel, reqType, reqValue);

    E = cogroup A BY channel, B BY channel, C by channel;
    describe E;
    E: {group: bytearray,A: {(platForm: bytearray,channel: bytearray,reqType: bytearray,reqValue: bytearray)},
        B: {(platForm: bytearray,channel: bytearray,reqType: bytearray,reqValue: bytearray)},
        C: {(platForm: bytearray,channel: bytearray,reqType: bytearray,reqValue: bytearray)}
        }

