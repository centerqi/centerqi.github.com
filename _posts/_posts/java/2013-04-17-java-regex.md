---
layout: post
category : java 
tags : [java]
---
{% include JB/setup %}

adoop pig 是用java 写的，要写 udf，不能不用 java, 正则也是必须的  

        import java.util.regex.Matcher;
        import java.util.regex.Pattern;

        public class UrlMatch{
                public static void main(String[] args){
                        String url=args[0];
                        String patternString = ".*(item).*|.*(taoke).*|.*(street).*|.*(product).*|.*(recommend).*|.*(search).*|.*(upload).*|.*(list).*"; 

                        Pattern pattern = Pattern.compile(patternString,Pattern.CASE_INSENSITIVE);
                        Matcher matcher = pattern.matcher(url);

                        while(matcher.find()){
                                int start = matcher.start();
                                int end = matcher.end();
                                System.out.println(String.format("start:%d end:%d",start,end));
                                System.out.println(url.substring(start,end));
                        } 

                        if(matcher.matches()){
                                System.out.println("Match");
                                int g = matcher.groupCount();
                                if(g > 0){
                                        for(int i= 0; i <= g; i++){
                                                System.out.println(String.format("groutIndex:%d,%s",i,matcher.group(i)));
                                        }
                                }
                        }
                } 
        }


java 的正则find 方法和 matches方法 

matches 可以匹配每一个子表达示 

        groutIndex:0,http://ishopping/list.do
        groutIndex:1,null
        groutIndex:2,null
        groutIndex:3,null
        groutIndex:4,null
        groutIndex:5,null
        groutIndex:6,null
        groutIndex:7,null
        groutIndex:8,list

没有匹配的都返回 null
