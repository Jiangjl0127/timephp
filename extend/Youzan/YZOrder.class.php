<?php
namespace Lib\Youzan;
require_once __DIR__ . '/lib/YZSignClient.php';
use YZSignClient;
class YZOrder{
	public function get(){
        $appId = '9dc706651fc25df536'; //请填入你有赞店铺后台-营销-有赞API的AppId
        $appSecret = 'f35cae02f811a9ca020ccfcac0da9464';//请填入你有赞店铺后台-营销-有赞API的AppSecret
        $client = new YZSignClient($appId, $appSecret);

        $method = 'youzan.trades.sold.get';//要调用的api名称
        $methodVersion = '3.0.0';//要调用的api版本号

        $params = [
            'type' => 'ALL',
            'status' => 'WAIT_BUYER_CONFIRM_GOODS',
        ];

        /*一次只能查询一种状态。 可选值：
            TRADE_NO_CREATE_PAY（没有创建支付交易）
            WAIT_BUYER_PAY（等待买家付款）
            WAIT_GROUP（等待成团，即：买家已付款，等待成团）
            WAIT_SELLER_SEND_GOODS（等待卖家发货，即：买家已付款）
            WAIT_BUYER_CONFIRM_GOODS（等待买家确认收货，即：卖家已发货）
            TRADE_BUYER_SIGNED（买家已签收）
            TRADE_CLOSED（付款以后用户退款成功，交易自动关闭）
            ALL_WAIT_PAY（包含：WAIT_BUYER_PAY、TRADE_NO_CREATE_PAY）
            ALL_CLOSED（所有关闭订单）
        */

        $files = [];
        return $client->post($method, $methodVersion, $params, $files);
	}
}
?>