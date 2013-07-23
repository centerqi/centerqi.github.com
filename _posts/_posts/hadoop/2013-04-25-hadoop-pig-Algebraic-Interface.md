---
layout: post
category : hadoop
tags : [hadoop]
---
{% include JB/setup %}

仔细看了一下hadoop pig 的udf 文档  在 Algebraic interface 设计上还是可以学习的。 

一些聚合函数，如 SUM, COUNT 都得实现 Algebraic 接口 

此接口要实现 三个方法，这三个方法都是返回具体实现的 class name

并且这些 class name都要实现 exec方法 


        public interface Algebraic{
                public String getInitial();
                public String getIntermed();
                public String getFinal();
        }


看 pig built in COUNT 的实现  

这几个方法都可以对应对相关的hadoop 的map combine,reduce

map 对应 Initial 

combine 对应 Intermed 

reduce 对应 reduce

发现 java 的内部静态内还是很有用的  

    public class COUNT extends EvalFunc<Long> implements Algebraic{
        public Long exec(Tuple input) throws IOException {return count(input);}
        public String getInitial() {return Initial.class.getName();}
        public String getIntermed() {return Intermed.class.getName();}
        public String getFinal() {return Final.class.getName();}
        static public class Initial extends EvalFunc<Tuple> {
                public Tuple exec(Tuple input) throws IOException {return
                        TupleFactory.getInstance().newTuple(count(input));}
        }
        static public class Intermed extends EvalFunc<Tuple> {
                public Tuple exec(Tuple input) throws IOException {return
                        TupleFactory.getInstance().newTuple(sum(input));}
        }
        static public class Final extends EvalFunc<Long> {
                public Tuple exec(Tuple input) throws IOException {return sum(input);}
        }
        static protected Long count(Tuple input) throws ExecException {
                Object values = input.get(0);
                if (values instanceof DataBag) return ((DataBag)values).size();
                else if (values instanceof Map) return new Long(((Map)values).size());
        }
        static protected Long sum(Tuple input) throws ExecException, NumberFormatException {
                DataBag values = (DataBag)input.get(0);
                long sum = 0;
                for (Iterator (Tuple) it = values.iterator(); it.hasNext();) {
                        Tuple t = it.next();
                        sum += (Long)t.get(0);
                }
                return sum;
        }
    }



