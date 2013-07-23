---
layout: post
category : php
tags : [php]
---
{% include JB/setup %}

在php 5.2中，是只有 bin2hex，但是没有hex2bin这样的函数的，因为使用的php版本的问题，所以得自己实现 hex2bin，得自己先看 bin2hex是怎么样实现的才可以。

bin2hex的实现思想很简单，就是每次读出一个字节，因为二进制与16进制是可以直接转换的，如0xab，在字节中，a是对应的高位，b是对应的底位，且a,b各占四个bit位。

取出高位

    result[j++] = hexconvtab[old[i] >> 4]; 

取出底位

    result[j++] = hexconvtab[old[i] & 15]; 


然后计算此值在16进制中分别对应什么值

        static char hexconvtab[] = "0123456789abcdef";
        static char *php_bin2hex(const unsigned char *old, const size_t oldlen, size_t *newlen)
        { 
            register unsigned char *result = NULL;
            size_t i, j;          

            result = (unsigned char *) safe_emalloc(oldlen * 2, sizeof(char), 1);

            for (i = j = 0; i < oldlen; i++) {
                result[j++] = hexconvtab[old[i] >> 4]; 
                result[j++] = hexconvtab[old[i] & 15]; 
            }
            result[j] = '\0';     

            if (newlen)           
                *newlen = oldlen * 2 * sizeof(char); 

            return (char *)result;
        }

hex2bin
先还原高位，然后再还原底位。

        static char *php_hex2bin(const unsigned char *old, const size_t oldlen, size_t *newlen)
        {
            size_t target_length = oldlen >> 1;
            register unsigned char *str = (unsigned char *)safe_emalloc(target_length, sizeof(char), 1);
            size_t i, j;
            for (i = j = 0; i < target_length; i++) {
                char c = old[j++];
                if (c >= '0' && c <= '9') {
                    str[i] = (c - '0') << 4;
                } else if (c >= 'a' && c <= 'f') {
                    str[i] = (c - 'a' + 10) << 4;
                } else if (c >= 'A' && c <= 'F') {
                    str[i] = (c - 'A' + 10) << 4;
                } else {
                    efree(str);
                    return NULL;
                }
                c = old[j++];
                if (c >= '0' && c <= '9') {
                    str[i] |= c - '0';
                } else if (c >= 'a' && c <= 'f') {
                    str[i] |= c - 'a' + 10;
                } else if (c >= 'A' && c <= 'F') {
                    str[i] |= c - 'A' + 10;
                } else {
                    efree(str);
                    return NULL;
                }
            }
            str[target_length] = '\0';

            if (newlen)
                *newlen = target_length;

            return (char *)str;
        }

