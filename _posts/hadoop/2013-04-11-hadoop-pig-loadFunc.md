---
layout: post
category : hadoop 
tags : [hadoop]
---
{% include JB/setup %}

hadoop pig 设计的还是很不错的，可以写 UDF 

每一个统计基本上都是要对原始日志进行切分，把想要的一些字段 EXTRACT 提取出来  

日志有着基本的模式  

    "mac:50:A4:C8:D7:10:7D"|"build:5141bc99"|"network:mobile"|"version:2.4.1"|"id:taobao22935952431"|

基本上是 key, value对，自定义一个 load function ,指定 key,就可以获取 对应的value，在 pig 中可以使用

    REGISTER /jar/kload.jar;
    AA = LOAD '/log/load.log' USING kload.KoudaiLoader('mac,build') AS (mac,build);
    DUMP AA;

输出结果

    (50:A4:C8:D7:10:7D,5141bc99)

koudaiLoader是自己实现的一个 Load function,输出为要获取的key,输出为key所对应的 value

        package kload;
        import java.io.IOException;
        import java.util.*;
         
        import org.apache.hadoop.io.Text;
        import org.apache.hadoop.mapreduce.*;
        import org.apache.hadoop.mapreduce.lib.input.FileInputFormat;
        import org.apache.pig.*;
        import org.apache.pig.backend.executionengine.ExecException;
        import org.apache.pig.backend.hadoop.executionengine.mapReduceLayer.*;
        import org.apache.pig.data.*;

        public class KoudaiLoader  extends LoadFunc{
                protected RecordReader recordReader = null;
                private String fieldDel = "";
                private String[] reqFildList;
                private ArrayList<Object> mProtoTuple = null;
                private TupleFactory mTupleFactory = TupleFactory.getInstance();
                private static final int BUFFER_SIZE = 1024;

                public KoudaiLoader() {
                }

                public KoudaiLoader(String delimiter) {
                        this();
                        if(delimiter == null || delimiter.length() == 0){
                                throw new RuntimeException("empty delimiter");
                        }
                        this.reqFildList=delimiter.split(",");
                }
                @Override
                public Tuple getNext() throws IOException {
                        try {
                                Map<String,String> tmpMap = new HashMap<String,String>();
                                List lst = new ArrayList<String>();
                                boolean flag = recordReader.nextKeyValue();
                                int i = 0;

                                if (!flag) {
                                        return null;
                                }
                                Text value = (Text) recordReader.getCurrentValue();
                                
                                tmpMap = this.sourceToMap(value.toString());
                                if( tmpMap == null || tmpMap.size() == 0 ){
                                    return null;
                                }

                                for (String s :this.reqFildList){
                                    String item = tmpMap.get(s); 
                                    if(item == null || item.length() == 0){
                                            item = "";
                                    }
                                        lst.add(i++, item);
                                }
                                return TupleFactory.getInstance().newTuple(lst);
                        } catch (InterruptedException e) {
                                throw new ExecException("Read data error", PigException.REMOTE_ENVIRONMENT, e);
                        }
                }



                public Map<String,String> sourceToMap(String pline){
                        String line = pline;
                        int strLen = 0;
                        String[] strArr;
                        String[] strSubArr;
                        Map<String,String> mapLog = new HashMap<String,String>();
                        if(pline == null || pline.length() <= 2){
                            return mapLog;
                        }

                        line = line.trim();
                        strLen = line.length();
                        line = line.substring(1,strLen -1);
                        strArr = line.split("\"\\|\"");
                        if(strArr.length == 0){
                            return mapLog;
                        }

                        for(String s:strArr){
                                if(s != null && s.length() != 0){
                                        strSubArr = s.split(":",2); 
                                        if(strSubArr.length == 2){
                                                mapLog.put(strSubArr[0],strSubArr[1]);
                                        }
                                }
                        }
                        return mapLog;
                }
                @Override
                public void setLocation(String s, Job job) throws IOException {
                    FileInputFormat.setInputPaths(job, s);
                }

                @Override
                public InputFormat getInputFormat() throws IOException {
                    return new PigTextInputFormat();
                }

                @Override
                public void prepareToRead(RecordReader recordReader, PigSplit pigSplit) throws IOException {
                    this.recordReader = recordReader;
                }

        }

编译

    javac -cp /usr/local/webserver/pig/pig-0.9.2.jar:.  KoudaiLoader.java

打成jar包

    jar -cf kload.jar kload

用pig在本地模式下运行

    java -cp /usr/local/webserver/pig/pig-0.9.2.jar:/jar/kload.jar org.apache.pig.Main -x local kload.pig


