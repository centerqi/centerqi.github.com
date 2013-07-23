---
layout: post
category : php
tags : [php, md5, raw]
---
{% include JB/setup %}

好久没有写 php 程序了，微信后台的数据格式是xml， php 中处理这种东西还是比较方便  


微信要求的格式如下

    <xml>
     <ToUserName><![CDATA[toUser]]></ToUserName>
     <FromUserName><![CDATA[fromUser]]></FromUserName>
     <CreateTime>12345678</CreateTime>
     <MsgType><![CDATA[news]]></MsgType>
     <ArticleCount>2</ArticleCount>
     <Articles>
     <item>
     <Title><![CDATA[title1]]></Title> 
     <Description><![CDATA[description1]]></Description>
     <PicUrl><![CDATA[picurl]]></PicUrl>
     <Url><![CDATA[url]]></Url>
     </item>
     <item>
     <Title><![CDATA[title]]></Title>
     <Description><![CDATA[description]]></Description>
     <PicUrl><![CDATA[picurl]]></PicUrl>
     <Url><![CDATA[url]]></Url>
     </item>
     </Articles>
     <FuncFlag>1</FuncFlag>
     </xml> 

因为要 添加CDATA类型,simpleXml不支持，得另外想办法, 在stackoverflow上面找到有人这样写

1. dom_import_simplexml是从xml中把 simpleXMLElement转换成 DOMELEMENT  

2. DOMDocument 有createCDATASection 方法  

个人感觉方便的一点就是simpleXml与DOM打通了,这个真是方便,官方有这么一句话  

    PHP has a mechanism to convert XML nodes between SimpleXML and DOM formats. This example shows how one might change a DOM element to SimpleXML.



    class SimpleXMLExtended extends SimpleXMLElement
    {
        public function addCData($cdata_text)
        {   
            $node = dom_import_simplexml($this); 
            $no   = $node->ownerDocument; 
            $node->appendChild($no->createCDATASection($cdata_text)); 
        }   
    } 

#增加子节点也很方便

    $xml        = new SimpleXMLExtended("<xml/>"); 
    $xml->ToUserName = NULL;
    $xml->ToUserName->addCData('ToUserName');

#如果要增加子节点的子节点，必须用 addChild  

    $Articles = $xml->addChild('Articles');

    $item0 = $Articles->addChild('item');

    $item0->Title = NULL;
    $item0->Title->addCData('Title');

#最后返回 xml的文档

    $xmlStr = $xml->asXML(); 

    <xml>
    <ToUserName><addCData>ToUserName</addCData></ToUserName>
    <FromUserName><![CDATA[FromUserName]]></FromUserName>
    <CreateTime>1363937514</CreateTime>
    <MsgType><![CDATA[MsgType]]></MsgType>
    <Articles>
    <item>
    <Title><![CDATA[Title]]></Title>
    <Description><![CDATA[Description]]></Description>
    <PicUrl><![CDATA[PicUrl]]></PicUrl>
    <Url><![CDATA[PicUrl]]></Url>
    </item>

    <item>
    <Title><![CDATA[Title]]></Title>
    <Description><![CDATA[Description]]></Description>
    <PicUrl><![CDATA[PicUrl]]></PicUrl>
    <Url><![CDATA[PicUrl]]></Url>
    </item>
    </Articles>
    </xml>


[simplexml](http://www.php.net/manual/en/book.simplexml.php 'simplexml处理xml真的是很方便')


[怎么样加入 CDATA](http://stackoverflow.com/questions/6260224/how-to-write-cdata-using-simplexmlelement '怎么样加入 CDATA')


