# proxy
一个带缓存的反向代理（php版本）

## 一些部署须知
1、nginx 或者apache需要把所有不存在的资源重定向到index.php访问
eg:

                    if (!-e $request_filename) {
                                    rewrite ^(.*)$ /index.php last;
                                    break;
                    }

2、文件目录caches 需要带写读权限

3、code里面_url 自定义到你喜欢proxy的website去畅游。

4、你的服务器需要可以访问外网，你才能通过这个脚本访问到外面网站。 可以打开http://g.html48.com 浏览看看

5、更多信息，请移步https://github.com/human2312/Reverse-Proxy
