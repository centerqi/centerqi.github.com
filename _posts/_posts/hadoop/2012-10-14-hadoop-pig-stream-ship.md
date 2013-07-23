---
layout: post
category : hadoop 
tags : [hadoop]
---
{% include JB/setup %}

hadoop pig stream  
pig 中的 stream 非常用用，一般是先用 perl, python, php, shell 对日志生成符合pig的数据格式后，再 用pig 来处理  

在脚本中，如果要加载一个文件做为依赖文件， 就可以用 ship 这一选项  


        DEFINE CMD `ac_mapper.php` ship('/proxy/step/ac_mapper.php', '/data/$mday/merge.lst');

        rawLog = load '$input' as (line);   

        schemeData = stream rawLog through CMD as (platForm, userKey, reqType, catId);


$mday, $input 都是参数， ship 从这个英文单词可以看出来，是用来装般的，把要用的东西都装上般  

第一个ac_mapper.php不能用绝对路径，在ship中申请ac_mapper.php中的绝对路径，告诉 pig 从哪里去取文件  

装上船后，就可以用了，如在 ac_mapper.php中读取 merge.lst文件 


        function readmacid(){
            $handle = @fopen("merge.lst", "r");
            $index = 1;
            if ($handle) {
                while (($buffer = fgets($handle, 4096)) !== false) {
                        list($platform,$mac, $ukey) = explode("\t",trim($buffer));
                        if(!empty($mac)){
                                $GLOBALS['mac2id'][$mac] = $index; 
                        }
                        if(!empty($ukey)){
                                $GLOBALS['ukey2id'][$ukey] = $index; 
                        }
                        $index++;
            }
            if (!feof($handle)) {
                        echo "Error: unexpected fgets() fail\n";
                        exit();
                }
                fclose($handle);
            }
        }


`@fopen("merge.lst","r")`这样就可以读取 merge.lst的文件了，因为 装船后，会把这些文件放在一个 workspace 中  


这个功能让我想起了前东西的一个 Dquery 系统，里面可以加载 依赖文件  

[http://pig.apache.org/docs/r0.7.0/piglatin_ref2.html#UDF+Statements](http://pig.apache.org/docs/r0.7.0/piglatin_ref2.html#UDF+Statements)  
[http://ofps.oreilly.com/titles/9781449302641/advanced_pig_latin.html](http://ofps.oreilly.com/titles/9781449302641/advanced_pig_latin.html)  
[http://wiki.apache.org/pig/PigStreamingFunctionalSpec](http://wiki.apache.org/pig/PigStreamingFunctionalSpec)  
