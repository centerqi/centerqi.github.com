---
layout: post
category : pig 
tags : [pig]
---
{% include JB/setup %}


同事反应,最近在计算某一个商品被浏览的次数和浏览的人数的时候, UV与PV小。
如果真是这样，那就有点搞笑了,文件的内容如下

    android,u1,taobao1
    android,u1,taobao1
    ,u2,taobao2

第一列为平台,第二列为userID, 第三列为商品ID
可以看出,taobao1的商品浏览次数为 2, 浏览人数为 1
taobao2的商品浏览次数为 1,浏览次数为 1


但是用 pig 脚本处理后的结果简直不能相信
pig脚本如下

    RR = LOAD '/user/www/udc/output/bugfind/sample.txt' USING PigStorage(',') as (platform, machineID,  productID);
    RB = GROUP RR BY (productID);
    RES = FOREACH RB{
                    ITEMUV = DISTINCT RR.machineID;
                    GENERATE flatten(group) ,COUNT(ITEMUV),COUNT(RR);
    };
    DUMP RES;

pig 输出结果

    (taobao1,1,2)
    (taobao2,1,0)

发现原来taobao2的 UV为 1, 而PV为0.

用EXPLAIN看了一下 MAPREDUCER的实现

    #--------------------------------------------------
    # Map Reduce Plan                                  
    #--------------------------------------------------
    MapReduce node scope-26
    Map Plan
    RB: Local Rearrange[tuple]{bytearray}(false) - scope-46
    |   |
    |   Project[bytearray][0] - scope-47
    |
    |---RES: New For Each(false,false,false)[bag] - scope-28
        |   |
        |   Project[bytearray][0] - scope-29
        |   |
        |   POUserFunc(org.apache.pig.builtin.Distinct$Initial)[tuple] - scope-30
        |   |
        |   |---1-5: New For Each(false)[tuple] - scope-32
        |       |   |
        |       |   Project[bytearray][1] - scope-31
        |       |
        |       |---Project[bag][1] - scope-33
        |   |
        |   POUserFunc(org.apache.pig.builtin.COUNT$Initial)[tuple] - scope-34
        |   |
        |   |---Project[bag][1] - scope-35
        |
        |---Pre Combiner Local Rearrange[tuple]{Unknown} - scope-48
            |
            |---RR: New For Each(false,false,false)[bag] - scope-7
                |   |
                |   Project[bytearray][0] - scope-1
                |   |
                |   Project[bytearray][1] - scope-3
                |   |
                |   Project[bytearray][2] - scope-5
                |
                |---RR: Load(/user/www/udc/output/bugfind/sample.txt:PigStorage(',')) - scope-0--------
    Combine Plan
    RB: Local Rearrange[tuple]{bytearray}(false) - scope-50
    |   |
    |   Project[bytearray][0] - scope-51
    |
    |---RES: New For Each(false,false,false)[bag] - scope-36
        |   |
        |   Project[bytearray][0] - scope-37
        |   |
        |   POUserFunc(org.apache.pig.builtin.Distinct$Intermediate)[tuple] - scope-38
        |   |
        |   |---Project[bag][1] - scope-39
        |   |
        |   POUserFunc(org.apache.pig.builtin.COUNT$Intermediate)[tuple] - scope-40
        |   |
        |   |---Project[bag][2] - scope-41
        |
        |---POCombinerPackage[tuple]{bytearray} - scope-44--------
    Reduce Plan
    RES: Store(fakefile:org.apache.pig.builtin.PigStorage) - scope-25
    |
    |---RES: New For Each(true,false,false)[bag] - scope-24
        |   |
        |   Project[bytearray][0] - scope-12
        |   |
        |   POUserFunc(org.apache.pig.builtin.COUNT)[long] - scope-15
        |   |
        |   |---POUserFunc(org.apache.pig.builtin.Distinct$Final)[bag] - scope-27
        |       |
        |       |---Project[bag][1] - scope-42
        |   |
        |   POUserFunc(org.apache.pig.builtin.COUNT$Final)[long] - scope-22
        |   |
        |   |---Project[bag][2] - scope-43
        |
        |---POCombinerPackage[tuple]{bytearray} - scope-52--------
    Global sort: false
    ----------------

可以看出 他是用 COUNT去做处理的，我们看一下 COUNT的源代码


    @Override
    public Long exec(Tuple input) throws IOException {
        try {
            DataBag bag = (DataBag)input.get(0);
            if(bag==null)
                return null;

            Iterator it = bag.iterator();
            long cnt = 0;
            while (it.hasNext()){
                    Tuple t = (Tuple)it.next();
                    if (t != null && t.size() > 0 && t.get(0) != null )
                            cnt++;
            }
            return cnt;
        } catch (ExecException ee) {
            throw ee;
        } catch (Exception e) {
            int errCode = 2106;                
            String msg = "Error while computing count in " + this.getClass().getSimpleName();
            throw new ExecException(msg, errCode, PigException.BUG, e);
        }
    }

    
 我们一看pig的内部实现，道理就很清楚了，会计算的时候，看第一个字段是会为null,如果为null,就不会计算在内。
