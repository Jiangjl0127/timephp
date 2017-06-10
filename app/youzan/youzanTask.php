<?php 
namespace app\youzan;
use lib\Task;
use lib\Db;
use lib\common\SystemFun;
/**
 * @author     村长<8044023@qq.com>
 * @copyright  TimePHP
 * @license    https://github.com/qq8044023/timePHP
 */
class youzanTask extends Task{
    public function run(){
        $this->newOrder();
    }

    public function newOrder(){
        $result = $this->get();
        if($result){
            $order = array();
            $data = $result['response']['trades'];
            $weekarray=array("日","一","二","三","四","五","六"); //先定义一个数组
            foreach ($data as $d){
                $fetch = $d['fetch_detail'];
                $order[$d['tid']] = array('from'=>array(),'detail'=>array(),'address'=>array());
                $order[$d['tid']]['address'] = array(
                    'id'=>$d['tid'],
                    'user_id'=>$fetch['fetcher_mobile'],
                    'address_type'=>'1',
                    'entityAddressName'=>$fetch['shop_name'],
                    'relation_phone'=>$fetch['fetcher_mobile'],
                    'relation_name'=>$fetch['fetcher_name'],
                );
                $orders = $d['orders'];
                foreach ($orders as $o){
                    $order[$d['tid']]['detail'][] = array(
                        'orderId'=>$d['tid'],
                        'productId'=>$o['outer_item_id'],
                        'productAmout'=>$o['num'],
                        'productPrice'=>$o['price'],
                        'productPicture'=>$o['pic_path'],
                        'productName'=>$o['title'],
                    );
                }
                $payTime = date('Y-m-d',strtotime($d['pay_time']));
                $order[$d['tid']]['from'] = array(
                    'orderId'=>$d['tid'],
                    'status'=>'2',
                    'payType'=>'3',
                    'transactionTime'=>$d['created'],
                    'userId'=>$fetch['fetcher_mobile'],
                    'productPrice'=>$d['price'],
                    'relityPrice'=>$d['price'],
                    'transactionNumber'=>$d['transaction_tid'],
                    'modificationTime'=>$d['pay_time'],
                    'distributionTime'=>date('Y-m-d',strtotime("$payTime +1 day"))." 星期".$weekarray[date("w",strtotime("$payTime +1 day"))]."/ ".$fetch['fetch_time'],
                );
            }
            if($order){
                $db = Db::setConfig($this->getConfig()["DB"]);
                $orderId = array_keys($order);
                $oId = $db->table()->model()->query("select orderId from order_from where orderId in (\"".implode('","',$orderId)."\")");
                $orderId = array_diff($orderId,$oId?array_column($oId,'orderId'):array());
                if($orderId){
                    $order_address = array();
                    $order_detail = array();
                    $order_from = array();
                    foreach ($orderId as $id){
                        $order_address[] = $order[$id]['address'];
                        $order_detail = array_merge($order_detail,$order[$id]['detail']);
                        $order_from[] = $order[$id]['from'];
                    }
                    $sql_address = 'INSERT INTO `order_address`(`id`,`user_id`,`address_type`,`entityAddressName`,`relation_phone`,`relation_name`) VALUES ';
                    foreach ($order_address as $a){
                        $sql_address .= "(\"".$a['id']."\",\"".$a['user_id']."\",\"".$a['address_type']."\",\"".$a['entityAddressName']."\",\"".$a['relation_phone']."\",\"".$a['relation_name']."\"),";
                    }
                    $sql_detail = 'INSERT INTO `order_detail`(`orderId`,`productId`,`productAmout`,`productPrice`,`productPicture`,`productName`) VALUES ';
                    foreach ($order_detail as $d){
                        $sql_detail .= "(\"".$d['orderId']."\",\"".$d['productId']."\",\"".$d['productAmout']."\",\"".$d['productPrice']."\",\"".$d['productPicture']."\",\"".$d['productName']."\"),";
                    }
                    $sql_from = 'INSERT INTO `order_from`(`orderId`,`status`,`payType`,`transactionTime`,`userId`,`productPrice`,`relityPrice`,`transactionNumber`,`modificationTime`,`distributionTime`) VALUES ';
                    foreach ($order_from as $f){
                        $sql_from .= "(\"".$f['orderId']."\",\"".$f['status']."\",\"".$f['payType']."\",\"".$f['transactionTime']."\",\"".$f['userId']."\",\"".$f['productPrice']."\",\"".$f['relityPrice']."\",\"".$f['transactionNumber']."\",\"".$f['modificationTime']."\",\"".$f['distributionTime']."\"),";
                    }
                    $db->table()->model()->beginTrans();
                    $oa = $db->table()->model()->query(rtrim($sql_address,','));
                    $od = $db->table()->model()->query(rtrim($sql_detail,','));
                    $of = $db->table()->model()->query(rtrim($sql_from,','));
                    exit("操作成功");
                }
                exit("没有新数据");
//                $this->ajaxReturn(array('code'=>'200','msg'=>'没有新数据'));
            }
        }
        exit("没有数据");
//        $this->ajaxReturn(array('code'=>'200','msg'=>'没有数据'));
    }

    public function get(){
        SystemFun::import("extend@Youzan@lib@YZSignClient");
        $appId = '9dc706651fc25df536'; //请填入你有赞店铺后台-营销-有赞API的AppId
        $appSecret = 'f35cae02f811a9ca020ccfcac0da9464';//请填入你有赞店铺后台-营销-有赞API的AppSecret
        $client = new \YZSignClient($appId, $appSecret);

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