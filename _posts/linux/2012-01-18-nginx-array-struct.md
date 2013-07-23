---
layout: post
category : linux
tags : [nginx]
---
{% include JB/setup %}

nginx中的内存管理，离不开两个最重要的struct
truct ngx_array_s {
    void        *elts;
    ngx_uint_t   nelts;
    size_t       size;
    ngx_uint_t   nalloc;
    ngx_pool_t  *pool;
};
    typedef struct ngx_pool_s        ngx_pool_t;
    struct ngx_pool_s {
        ngx_pool_data_t       d; //内存池的数据块信息
        size_t                max; //内存池的数据块的最大值
        ngx_pool_t           *current; //指向当前内存池
        ngx_chain_t          *chain; //
        ngx_pool_large_t     *large; //大块内存链表
        ngx_pool_cleanup_t   *cleanup; //挂载一些内存池释放的时候，同时释放的资源
        ngx_log_t            *log;
    };  

    typedef struct {
        u_char               *last; //当前内存分配结束位置，即下一段可分配内存的起始位置
        u_char               *end; //内存池的结束位置
        ngx_pool_t           *next; //内存池里有多块内存，连接到下一块内存
        ngx_uint_t            failed; //内存池分配失败次数
    } ngx_pool_data_t;      //内存池的数据块分配权信息 
    

也离不开这两个函数  


    ngx_create_pool
    ngx_palloc_block


[nginx 源码分析-内存池](http://www.alidata.org/archives/1390 'nginx 源码分析-内存池')
[nginx 内存池管理](http://blog.csdn.net/livelylittlefish/article/details/6586946 'nginx 内存池管理')

