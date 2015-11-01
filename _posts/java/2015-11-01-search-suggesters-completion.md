---
layout: post
category : java 
tags : [elasticsearch]
---
{% include JB/setup %}


发现 ES 变的很强大了， 他已经支持suggestion的功能了，今天测试了一下他的Completion Suggester，Completion Suggester 其实应该是prefix suggester.

没有去看源代码，理论上应该是TIRE这种结构的一个变种。

suggestion的场景有两种，一种是自动完成，比如你输入中文`心`的时候，与`心`相关的词都会出来，如`心脏` `心痛` 之类的，还有一点，就是中文是有拼音的，

当输入 `x`的时候，应该与`心` 之间存在一个对应。


定义一个sug

    curl -XDELETE 'http://localhost:9200/sug/'

    curl -XPUT "http://localhost:9200/sug/" -d'
    {
       "mappings": {
          "disease": {
             "properties" : {
                "suggest" : { "type" : "completion",
                              "index_analyzer" : "simple",
                              "search_analyzer" : "simple"
                }
            }
          }
       }
    }'

创建sug的索引


    curl -X PUT 'localhost:9200/sug/disease/1?refresh=true' -d '{
        "suggest" : {
            "input": [ "心脏","xz" ],
            "output": "心脏",
            "weight" : 1
        }
    }'



搜索效果测试

当用户输入`xe`换者`小儿`时，效果如下所示

    curl -X POST 'localhost:9200/sug/_suggest?pretty' -d '{
        "song-suggest" : {
            "text" : "xe",
            "completion" : {
                "field" : "suggest"
            }
        }
    }'
    {
      "_shards" : {
        "total" : 5,
        "successful" : 5,
        "failed" : 0
      },
      "song-suggest" : [ {
        "text" : "xe",
        "offset" : 0,
        "length" : 2,
        "options" : [ {
          "text" : "小儿维生素A缺乏病",
          "score" : 4.0
        }, {
          "text" : "小儿维生素B2缺乏病",
          "score" : 4.0
        }, {
          "text" : "小儿维生素C缺乏病",
          "score" : 4.0
        }, {
          "text" : "小儿维生素E缺乏病",
          "score" : 4.0
        }, {
          "text" : "小儿共济失调毛细血管扩张...",
          "score" : 2.0
        } ]
      } ]
    }

[https://www.elastic.co/guide/en/elasticsearch/reference/1.3/search-suggesters-completion.html#search-suggesters-completion]('https://www.elastic.co/guide/en/elasticsearch/reference/1.3/search-suggesters-completion.html#search-suggesters-completion' https://www.elastic.co/guide/en/elasticsearch/reference/1.3/search-suggesters-completion.html#search-suggesters-completion)


[https://qbox.io/blog/quick-and-dirty-autocomplete-with-elasticsearch-completion-suggest](https://qbox.io/blog/quick-and-dirty-autocomplete-with-elasticsearch-completion-suggest 'https://qbox.io/blog/quick-and-dirty-autocomplete-with-elasticsearch-completion-suggest')
