<?php 
/**
 * 公用函数
 * @author     村长<8044023@qq.com>
 * @copyright  TimePHP
 * @license    https://github.com/qq8044023/timePHP
 *   */

function getSelectSql($table,$date){
    $sql = "";
    switch($table){
        case 'address':
            $sql = sprintf("SELECT
                        oa.*
                    FROM
                        order_address oa
                    LEFT JOIN order_from of ON of.orderId = oa.id
                    WHERE
                        of.distributionTime LIKE concat('%s', '%%')
                    AND of.`status` = '2'",$date);
            break;
        case 'detail':
            $sql = sprintf("SELECT
                        od.*
                    FROM
                        order_detail od
                    LEFT JOIN order_from of ON of.orderId = od.orderId
                    WHERE
                        of.distributionTime LIKE concat('%s', '%%')
                    AND of.`status` = '2'",$date);
            break;
        case 'from':
            $sql = sprintf("SELECT
                        *
                    FROM
                        order_from of
                    WHERE
                        of.distributionTime LIKE concat('%s', '%%')
                    AND of.`status` = '2'",$date);
            break;
    }
    return $sql;
}

function getInsertSql($table,$data){
    $sql = "insert into ";
    switch($table){
        case 'address':
            $sql .= "order_address(`id`,`address_id`,`user_id`,`address_type`,`entityAddressName`,`address_des`,`relation_phone`,`relation_name`,`sign`) values";
            foreach ($data as $d){
                $sql .= sprintf("('%s','%s','%s','%s','%s','%s','%s','%s','%s'), ",$d['id'],$d['address_id'],$d['user_id'],$d['address_type'],$d['entityAddressName'],$d['address_des'],$d['relation_phone'],$d['relation_name'],$d['sign']);
            }
            break;
        case 'detial':
            $sql .= "order_detail(`orderId`,`productId`,`productAmout`,`productPrice`,`productPicture`,`productName`) values";
            foreach ($data as $d){
                $sql .= sprintf("('%s','%s','%s','%s','%s','%s'), ",$d['orderId'],$d['productId'],$d['productAmout'],$d['productPrice'],$d['productPicture'],$d['productName']);
            }
            break;
        case 'from':
            $sql .= "order_from(`orderId`,`status`,`payType`,`deFlag`,`transactionTime`,`userId`,`productPrice`,`relityPrice`,`coupponSn`,`couponMoney`,`balanceMoney`,`vegetableMoney`,`freight`,`order_address_id`,`backVegetable`,`transactionNumber`,`modificationTime`,`remark`,`distributionTime`,`confirmReceiptTime`) values";
            foreach ($data as $d){
                $sql .= sprintf("('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s'), ",$d['orderId'],$d['status'],$d['payType'],$d['deFlag'],$d['transactionTime'],$d['userId'],$d['productPrice'],$d['relityPrice'],$d['coupponSn'],$d['couponMoney'],$d['balanceMoney'],$d['vegetableMoney'],$d['freight'],$d['order_address_id'],$d['backVegetable'],$d['transactionNumber'],$d['modificationTime'],$d['remark'],$d['distributionTime'],$d['confirmReceiptTime']);
            }
            break;
    }
    return substr($sql,0,-2) . ';';
}
?>