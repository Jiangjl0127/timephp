<?php
    include "TopSdk.php";
    date_default_timezone_set('Asia/Shanghai'); 

//    $httpdns = new HttpdnsGetRequest;
//    $client = new ClusterTopClient("4272","0ebbcccfee18d7ad1aebc5b135ffa906");
//    $client->gatewayUrl = "http://api.daily.taobao.net/router/rest";
//    var_dump($client->execute($httpdns,"6100e23657fb0b2d0c78568e55a3031134be9a3a5d4b3a365753805"));

    $c = new TopClient;
    $c->appkey = "23423512"; //$appkey
    $c->secretKey = "248452cae29958580d12a3a1f300fce4"; //$secret
    $req = new AlibabaAliqinFcSmsNumSendRequest;
    $req->setExtend("");
    $req->setSmsType("normal");
    $req->setSmsFreeSignName("自家菜园");
    $req->setSmsParam("{\"username\":\"李哲\",\"address\":\"唐轩中心\"}");
    $req->setRecNum("15640380127");
    $req->setSmsTemplateCode("SMS_53205290");
    echo $c->execute($req);
?>