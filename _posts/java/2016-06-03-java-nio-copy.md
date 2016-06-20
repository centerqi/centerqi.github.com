---
layout: post
category : java 
tags : [nio]
---
{% include JB/setup %}


一直在读hadoop的源码，今天读到io部分，hadoop对io的处理是用native来实现的。

如 copyFileUnbuffered 的实现。


	/**
	   * Unbuffered file copy from src to dst without tainting OS buffer cache
	   *
	   * In POSIX platform:
	   * It uses FileChannel#transferTo() which internally attempts
	   * unbuffered IO on OS with native sendfile64() support and falls back to
	   * buffered IO otherwise.
	   *
	   * It minimizes the number of FileChannel#transferTo call by passing the the
	   * src file size directly instead of a smaller size as the 3rd parameter.
	   * This saves the number of sendfile64() system call when native sendfile64()
	   * is supported. In the two fall back cases where sendfile is not supported,
	   * FileChannle#transferTo already has its own batching of size 8 MB and 8 KB,
	   * respectively.
	   *
	   * In Windows Platform:
	   * It uses its own native wrapper of CopyFileEx with COPY_FILE_NO_BUFFERING
	   * flag, which is supported on Windows Server 2008 and above.
	   *
	   * Ideally, we should use FileChannel#transferTo() across both POSIX and Windows
	   * platform. Unfortunately, the wrapper(Java_sun_nio_ch_FileChannelImpl_transferTo0)
	   * used by FileChannel#transferTo for unbuffered IO is not implemented on Windows.
	   * Based on OpenJDK 6/7/8 source code, Java_sun_nio_ch_FileChannelImpl_transferTo0
	   * on Windows simply returns IOS_UNSUPPORTED.
	   *
	   * Note: This simple native wrapper does minimal parameter checking before copy and
	   * consistency check (e.g., size) after copy.
	   * It is recommended to use wrapper function like
	   * the Storage#nativeCopyFileUnbuffered() function in hadoop-hdfs with pre/post copy
	   * checks.
	   *
	   * @param src                  The source path
	   * @param dst                  The destination path
	   * @throws IOException
	   */
	  public static void copyFileUnbuffered(File src, File dst) throws IOException {
	    if (nativeLoaded && Shell.WINDOWS) {
	      copyFileUnbuffered0(src.getAbsolutePath(), dst.getAbsolutePath());
	    } else {
	      FileInputStream fis = null;
	      FileOutputStream fos = null;
	      FileChannel input = null;
	      FileChannel output = null;
	      try {
		fis = new FileInputStream(src);
		fos = new FileOutputStream(dst);
		input = fis.getChannel();
		output = fos.getChannel();
		long remaining = input.size();
		long position = 0;
		long transferred = 0;
		while (remaining > 0) {
		  transferred = input.transferTo(position, remaining, output);
		  remaining -= transferred;
		  position += transferred;
		}
	      } finally {
		IOUtils.cleanup(LOG, output);
		IOUtils.cleanup(LOG, fos);
		IOUtils.cleanup(LOG, input);
		IOUtils.cleanup(LOG, fis);
	      }
	    }
	  }



会先确认是否加载了so，然后确认是否在windows平台上，如果满足这两点，就会调用 copyFileUnbuffered0

copyFileUnbuffered0的实现如下


	JNIEXPORT void JNICALL
	Java_org_apache_hadoop_io_nativeio_NativeIO_copyFileUnbuffered0(
	JNIEnv *env, jclass clazz, jstring jsrc, jstring jdst)
	{
	#ifdef UNIX
	  THROW(env, "java/lang/UnsupportedOperationException",
	    "The function copyFileUnbuffered0 should not be used on Unix. Use FileChannel#transferTo instead.");
	#endif

	#ifdef WINDOWS
	  LPCWSTR src = NULL, dst = NULL;

	  src = (LPCWSTR) (*env)->GetStringChars(env, jsrc, NULL);
	  if (!src) goto cleanup; // exception was thrown
	  dst = (LPCWSTR) (*env)->GetStringChars(env, jdst, NULL);
	  if (!dst) goto cleanup; // exception was thrown
	  if (!CopyFileEx(src, dst, NULL, NULL, NULL, COPY_FILE_NO_BUFFERING)) {
	    throw_ioe(env, GetLastError());
	  }

	cleanup:
	  if (src) (*env)->ReleaseStringChars(env, jsrc, src);
	  if (dst) (*env)->ReleaseStringChars(env, jdst, dst);
	#endif
	}


其实java里有很多种方法可以对文件进行复制，但是我发现用channel是最快的，在java中对文件进行复制的方法很多，我总结了一下主流的方法。


## 直接用流来处理

	private static void copyFileUsingFileStreams( File source, File dest  ) throws IOException {

	long start = System.nanoTime();
	long end;


	InputStream in = null;
	OutputStream out = null;

	try{

	    in = new FileInputStream(source);
	    out = new FileOutputStream(dest);

	    byte[] buf = new byte[1024];
	    int bytesRead = 0;

	    while( (bytesRead = in.read(buf)) > 0 ){
		out.write(buf,0,bytesRead);
	    }

	}
	finally{

	    if(in != null){
		in.close();
	    }

	    if(out != null){
		out.close();
	    }

	}
	end = System.nanoTime();
	System.out.println("Time taken by FileStreams Copy = "  + (end - start));



	}

## 用java7自带的api

	private static void copyFileUsingJava7Files(File source, File dest)  throws IOException{

		long start = System.nanoTime();
		long end;
		Files.copy(source.toPath(), dest.toPath());
		end = System.nanoTime();
		System.out.println("Time taken by FileStreams Copy = "  + (end - start));


	}


## 用Channel

	private static void copyFileChannel(File src, File dst) throws IOException{

	long start = System.nanoTime();
	long end;



	FileInputStream fis = null;
	FileOutputStream fos = null;
	FileChannel input = null;
	FileChannel output = null;

	try {
	    fis = new FileInputStream(src);
	    fos = new FileOutputStream(dst);
	    input = fis.getChannel();
	    output = fos.getChannel();
	    long remaining = input.size();
	    long position = 0;
	    long transferred = 0;
	    while (remaining > 0) {
		transferred = input.transferTo(position, remaining, output);
		remaining -= transferred;
		position += transferred;
	    }
	} finally {
	    output.close();
	    fos.close();
	    input.close();
	    fis.close();
	}

	end = System.nanoTime();
	System.out.println("Time taken by FileStreams Copy = "  + (end - start));

	}



