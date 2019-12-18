<?php
set_time_limit(60);

$_url = "https://www.google.com/";

$proxy = new proxy();
$html = $proxy->run($_url);
echo $html;
exit;

class proxy {

    public $suffix = array("bmp","jpg","png","jpeg","gif","pcx");
    public $fileName = "caches/";

    /**
     * 运行
     * @param $_url
     * @return bool|false|mixed|string|null
     */
    public function run($_url) {
        $url = $_url.$_SERVER["REQUEST_URI"];
        $html = $this->getCache($url);
        $html = $html != null ? $html : $this->getWeb($url);
        $html = $this->isImg($url,$html);
        $html = $this->stripUrl($_url,$html);
        return $html;
    }

    /**
     * 伪造参数仿浏览器获取远程数据
     * @param $curlurl
     * @return bool|string
     */
    private function getWeb($curlurl) {
        $ch = curl_init();
        $referurl = $curlurl;
        $ip =  $_SERVER["REMOTE_ADDR"] != "" ? $_SERVER["REMOTE_ADDR"] : mt_rand(11, 191).".".mt_rand(0, 240).".".mt_rand(1, 240).".".mt_rand(1, 240);   //随机ip
        $agentarry=[
            //PC端的UserAgent
            "safari 5.1 – MAC"=>"Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11",
            "safari 5.1 – Windows"=>"Mozilla/5.0 (Windows; U; Windows NT 6.1; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50",
            "Firefox 38esr"=>"Mozilla/5.0 (Windows NT 10.0; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0",
            "IE 11"=>"Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; .NET4.0C; .NET4.0E; .NET CLR 2.0.50727; .NET CLR 3.0.30729; .NET CLR 3.5.30729; InfoPath.3; rv:11.0) like Gecko",
            "IE 9.0"=>"Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0",
            "IE 8.0"=>"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)",
            "IE 7.0"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)",
            "IE 6.0"=>"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)",
            "Firefox 4.0.1 – MAC"=>"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:2.0.1) Gecko/20100101 Firefox/4.0.1",
            "Firefox 4.0.1 – Windows"=>"Mozilla/5.0 (Windows NT 6.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1",
            "Opera 11.11 – MAC"=>"Opera/9.80 (Macintosh; Intel Mac OS X 10.6.8; U; en) Presto/2.8.131 Version/11.11",
            "Opera 11.11 – Windows"=>"Opera/9.80 (Windows NT 6.1; U; en) Presto/2.8.131 Version/11.11",
            "Chrome 17.0 – MAC"=>"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_0) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11",
            "傲游（Maxthon）"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Maxthon 2.0)",
            "腾讯TT"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; TencentTraveler 4.0)",
            "世界之窗（The World） 2.x"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)",
            "世界之窗（The World） 3.x"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; The World)",
            "360浏览器"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; 360SE)",
            "搜狗浏览器 1.x"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; SE 2.X MetaSr 1.0; SE 2.X MetaSr 1.0; .NET CLR 2.0.50727; SE 2.X MetaSr 1.0)",
            "Avant"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Avant Browser)",
            "Green Browser"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)",
            //移动端口
            "safari iOS 4.33 – iPhone"=>"Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_3_3 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8J2 Safari/6533.18.5",
            "safari iOS 4.33 – iPod Touch"=>"Mozilla/5.0 (iPod; U; CPU iPhone OS 4_3_3 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8J2 Safari/6533.18.5",
            "safari iOS 4.33 – iPad"=>"Mozilla/5.0 (iPad; U; CPU OS 4_3_3 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8J2 Safari/6533.18.5",
            "Android N1"=>"Mozilla/5.0 (Linux; U; Android 2.3.7; en-us; Nexus One Build/FRF91) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1",
            "Android QQ浏览器 For android"=>"MQQBrowser/26 Mozilla/5.0 (Linux; U; Android 2.3.7; zh-cn; MB200 Build/GRJ22; CyanogenMod-7) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1",
            "Android Opera Mobile"=>"Opera/9.80 (Android 2.3.4; Linux; Opera Mobi/build-1107180945; U; en-GB) Presto/2.8.149 Version/11.10",
            "Android Pad Moto Xoom"=>"Mozilla/5.0 (Linux; U; Android 3.0; en-us; Xoom Build/HRI39) AppleWebKit/534.13 (KHTML, like Gecko) Version/4.0 Safari/534.13",
            "BlackBerry"=>"Mozilla/5.0 (BlackBerry; U; BlackBerry 9800; en) AppleWebKit/534.1+ (KHTML, like Gecko) Version/6.0.0.337 Mobile Safari/534.1+",
            "WebOS HP Touchpad"=>"Mozilla/5.0 (hp-tablet; Linux; hpwOS/3.0.0; U; en-US) AppleWebKit/534.6 (KHTML, like Gecko) wOSBrowser/233.70 Safari/534.6 TouchPad/1.0",
            "UC标准"=>"NOKIA5700/ UCWEB7.0.2.37/28/999",
            "UCOpenwave"=>"Openwave/ UCWEB7.0.2.37/28/999",
            "UC Opera"=>"Mozilla/4.0 (compatible; MSIE 6.0; ) Opera/UCWEB7.0.2.37/28/999",
            "微信内置浏览器"=>"Mozilla/5.0 (Linux; Android 6.0; 1503-M02 Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/37.0.0.0 Mobile MQQBrowser/6.2 TBS/036558 Safari/537.36 MicroMessenger/6.3.25.861 NetType/WIFI Language/zh_CN",
            // ""=>"",

        ];
        $useragent = $_SERVER["HTTP_USER_AGENT"] != "" ? $_SERVER["HTTP_USER_AGENT"] : $agentarry[array_rand($agentarry,1)];  //随机浏览器useragent
        $header = array(
            'CLIENT-IP:'.$ip,
            'X-FORWARDED-FOR:'.$ip,
        );    //构造ip
        curl_setopt($ch, CURLOPT_URL, $curlurl); //要抓取的网址
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_REFERER, $referurl);  //模拟来源网址
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent); //模拟常用浏览器的useragent
        $page_content = curl_exec($ch);
        curl_close($ch);
        return $page_content;
    }

    /**
     * 遇到图片类型直接输出图片类型
     * @param $url
     * @param $html
     * @return mixed
     */
    private function isImg($url,$html) {
        if(in_array($this->getExtension($url),$this->suffix)) {
            $this->doCache($url,$html);
            header("Content-Type: image/jpeg;text/html; charset=utf-8");
            echo $html;
            exit;
        }
        return $html;
    }

    /**
     * 获取文件后缀
     * @param $filename
     * @return string
     */
    private function getExtension($filename){
        return strtolower(pathinfo(parse_url( $filename, PHP_URL_PATH ), PATHINFO_EXTENSION ));
    }

    /**
     * 全站替换原URL
     * @param $_url
     * @param $html
     * @return mixed
     */
    private function stripUrl($_url,$html) {
        return str_replace($_url,$_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"]."/",$html);
    }

    /**
     * 优先返回cache内容
     * @param $url
     * @return false|string|null
     */
    private function getCache($url) {
        $fileName = $this->fileName . md5(base64_encode($url));
        if(file_exists($fileName)) {
            return file_get_contents($fileName);
        }
        return null;
    }

    /**
     * 文件资源存到本地cache ，请确保已存在fileName文件夹，和文件夹权限设置正确
     * @param $url
     * @param $html
     */
    private function doCache($url,$html) {
        $fileName = $this->fileName . md5(base64_encode($url));
        if(!file_exists($fileName)) {
            if($fp=fopen($fileName,'w')){
                fwrite($fp,$html);
                fclose($fp);
            }
        }
    }
}