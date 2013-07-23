---
layout: post
category : linux
tags : [c]
---
{% include JB/setup %}

读nginx代码时，分析其内存管理时，有如下一段代码

    void *
    ngx_memalign(size_t alignment, size_t size, ngx_log_t *log)
    {
        void  *p;
        int    err;

        err = posix_memalign(&p, alignment, size);

        if (err) {
            ngx_log_error(NGX_LOG_EMERG, log, err,
                    "posix_memalign(%uz, %uz) failed", alignment, size);
            p = NULL;
        }

        ngx_log_debug3(NGX_LOG_DEBUG_ALLOC, log, 0,
                "posix_memalign: %p:%uz @%uz", p, size, alignment);

        return p;
    }


简单点说，就是确保如下公式成立

    p%alignment==0

成立，而alignment必须是 2的幂的倍数


[为什么要内存对齐](http://bbs.chinaunix.net/forum.php?mod=viewthread&tid=3767556 '为什么要内存对齐')     

[posix_memalign的具体介绍](http://www.kernel.org/doc/man-pages/online/pages/man3/posix_memalign.3.html 'posix_memalign的具体介绍')  

[内存对齐的方式](http://blog.csdn.net/hairetz/article/details/4084088 '内存对齐的方式')

[简要说明](http://simohayha.iteye.com/blog/277707 '简要说明')
