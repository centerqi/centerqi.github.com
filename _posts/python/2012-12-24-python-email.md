---
layout: post
category : python
tags : [python]
---
{% include JB/setup %}

python 类库真是很齐全  并且性能也还可以，我主要用python来处理文本，主要工作是数据抽取(etl)
因为工司用的是QQ 企业邮箱，并且smtp没有开 ssl

        SMTP_SERVER = 'smtp.exmail.qq.com'
        SMTP_PORT = 25

        sender = 'xxxx@xxx.com'
        recipient = 'xxx@xxx.com'
        subject = 'subject'
        body = 'test mail'
        smtp_pass = 'pass'
        "Sends an e-mail to the specified recipient."

        body = "" + subject + ""

        headers = ["From: " + sender,
                    "Subject: " + subject,
                    "To: " + recipient,
                    "MIME-Version: 1.0",
                    "Content-Type: text/html"]
        headers = "\r\n".join(headers)

        session = smtplib.SMTP(SMTP_SERVER, SMTP_PORT)

        session.ehlo()
        #session.starttls() 因为没开ssl
        session.ehlo
        session.login(sender, smtp_pass)

        session.sendmail(sender, recipient, headers + "\r\n\r\n" + body)
        session.quit()

邮件基本上要添加附件，或者html邮件，或者指定邮件编码  

    msg = email.mime.Multipart.MIMEMultipart()
    msg['Subject'] = "Subject" 
    msg['From'] = 'xxx@gmail.com'
    msg['To'] = 'xxx@gmail.com'

    #发送附件
    fp=open(filename,'rb')
    content=fp.read()
    att = email.mime.application.MIMEApplication(content,_subtype="csv")
            fp.close()
            att.add_header('Content-Disposition','attachment',filename=os.path.basename(file))
            msg.attach(att)

    #发送html邮件,csvTohtml是自己写的一个function，把csv文件转换成html格式的table
    csvhtml=csvTohtml(file)
    body = email.mime.Text.MIMEText(csvhtml.decode('utf8').encode('gbk'),_charset='gb2312',_subtype='html')
            msg.attach(body)
 
 
    #打包发送
    s = smtplib.SMTP('smtp.exmail.qq.com')
    s.ehlo()
    s.login('xxx@xx.com','passwd')
    s.sendmail('xxx@xx.com',to, msg.as_string())
    s.quit()


[smtplib](http://docs.python.org/2/library/email-examples.html 'smtplib')  
[gmail python smtp](http://segfault.in/2010/12/sending-gmail-from-python/ 'gmail python smtp')  
[python smtp](http://www.drewconway.com/zia/?p=2707 'python smtp')  
[python send mail attachment,html mail](http://www.cnblogs.com/xiaowuyi/archive/2012/03/17/2404015.html 'python send mail attachment,html mail')


