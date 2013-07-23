---
layout: post
category : hadoop 
tags : [hadoop]
---
{% include JB/setup %}

##hadoop pig udf scheme

如果不指定 scheme 当你返回一个tuple里面有大于1个fields的时候，

就必须指定schemea 不然多个field就当作一个field


        register myudfs.jar;
        A = load 'student_data' as (name: chararray, age: int, gpa: float);
        B = foreach A generate flatten(myudfs.Swap(name, age)), gpa;
        C = foreach B generate $2;
        D = limit B 20;
        dump D

This script will result in the following error cause by line 4 ( C = foreach B
generate $2;).


    java.io.IOException: Out of bound access. Trying to access non-existent column: 2. Schema
{bytearray,gpa: float} has 2 column(s).


This is because Pig is only aware of two columns in B while line 4 is requesting the third
column of the tuple. (Column indexing in Pig starts with 0.)
The function, including the schema, looks like this:


下面实现了一个schema,输出为4个参数，输出为两个参数，在android上面要用imei和mac去生成一个ukey,在ios平台上，要用 mac和openudid去生成一个ukey 

最后返回的是一个platform,ukey

        package kload;
        import java.io.IOException;
        import org.apache.pig.EvalFunc;
        import org.apache.pig.data.Tuple;
        import org.apache.pig.data.TupleFactory;
        import org.apache.pig.impl.logicalLayer.schema.Schema;
        import org.apache.pig.data.DataType;
         /**
          *translate mac,imei,openudid to key
          */
         public class KoudaiFormateUkey extends EvalFunc<Tuple>{
             private String ukey = null;
             private String platform = null;
             public Tuple exec(Tuple input) throws IOException {
                 if (input == null || input.size() == 0)
                     return null;
                 try{
                     String platform = (String)input.get(0);
                     String mac = (String)input.get(1);
                     String imei= (String)input.get(2);
                     String openudID = (String)input.get(3);
                     this.getUkey(platform,mac,imei,openudID);
                     if(this.platform == null || this.ukey == null){
                         return null;
                     }
                     Tuple output = TupleFactory.getInstance().newTuple(2);
                     output.set(0, this.platform);
                     output.set(1, this.ukey);
                     return output;
                 }catch(Exception e){
                     throw new IOException("Caught exception processing input row ", e);
                 }
             }
             private String getUkey(String platform, String mac, String imei, String openudID){
                 String tmpStr = null;
                 String ukey = null;
                 int pType=-1;
                 if(platform == null){
                     return null;
                 }
                 tmpStr = platform.toUpperCase();
                 if(tmpStr.indexOf("IPHONE") != -1){
                     this.platform = "iphone";
                     pType = 1001; 
                 }else if(tmpStr.indexOf("ANDROID") != -1){
                     this.platform = "android";
                     pType = 1002; 
                 }else if(tmpStr.indexOf("IPAD") != -1){
                     this.platform = "ipad";
                     pType = 1003; 
                 }else{
                     this.platform = "unknow";
                     pType = 1004; 
                 }

                 switch(pType){
                     case 1001:
                         case 1003:
                         if(mac == null && openudID == null){
                             return null;
                         }
                     ukey = String.format("%s_%s",mac,openudID);
                     break;

                     case 1002:
                         if(mac == null && imei== null){
                             return null;
                         }
                     ukey = String.format("%s_%s",mac,imei);
                     break;

                     case 1004:
                         if(mac == null && imei== null && openudID == null){
                             return null;
                         }
                     ukey = String.format("%s_%s_%s",mac,imei,openudID);
                     break;

                     default:
                     break;
                 }
                 if  (ukey == null || ukey.length() == 0){
                     return null;
                 }
                 this.ukey = ukey.toUpperCase();
                 return this.ukey;
             }


             public Schema outputSchema(Schema input) {
                 try{
                     Schema tupleSchema = new Schema();
                     tupleSchema.add(input.getField(0));
                     tupleSchema.add(input.getField(1));
                     return new Schema(new
                             Schema.FieldSchema(getSchemaName(this.getClass().getName().toLowerCase(),
                                     input),tupleSchema, DataType.TUPLE));
                 }catch (Exception e){
                     return null;
                 }
             }
        }
