<?php 
/**
 * 公用函数
 * @author     村长<8044023@qq.com>
 * @copyright  TimePHP
 * @license    https://github.com/qq8044023/timePHP
 *   */


function selectInfoSql($date, $status){
    $sql = sprintf("SELECT 
            order_from.orderId id,
            vegetable_lng_lat.id site_id,
            vegetable_lng_lat.entityaddressId site_code,
            order_address.user_id user_id,
            order_address.relation_name user_name,
            order_address.relation_phone user_phone,
            order_address.address_type delivery_type,
            order_from.transactionTime `time`,
            order_from.modificationTime pay_time,
            '%s' delivery_date,
            order_address.address_des delivery_address 
        FROM 
            order_from 
        INNER JOIN order_address ON order_address.id = order_from.orderId 
        LEFT JOIN vegetable_lng_lat ON vegetable_lng_lat.id = order_address.eid AND abs(vegetable_lng_lat.address_Type - 1) = order_address.address_type 
        WHERE 
            order_from.distributionTime LIKE CONCAT('%s', '%%') 
        AND order_from.`status` = '%d';",$date,$date,$status);
   echo $sql;
   return $sql;
}

function selectDetailSql($date, $status){
    $sql = sprintf("SELECT 
            order_from.orderId order_id,
            order_from.transactionTime `time`,
            order_from.modificationTime pay_time,
            '%s' delivery_date,
            order_detail.pid product_id,
            order_detail.productId product_code,
            order_detail.productAmout number 
        FROM 
            order_from 
        INNER JOIN order_detail ON order_detail.orderId = order_from.orderId 
        WHERE 
            order_from.distributionTime LIKE CONCAT('%s', '%%') 
        AND order_from.`status` = '%d';",$date,$date,$status);
   echo $sql;
   return $sql;
}

function insertInfoSql($data){
    $sql = "replace into order_info(`id`,`time`,`pay_time`,`site_id`,`site_code`,`user_id`,`user_name`,`user_phone`,`delivery_type`,`delivery_date`,`delivery_address`) values";
    foreach ($data as $d){
        $sql .= sprintf("('%s','%s','%s','%s','%.6s','%s','%s','%s','%d','%s','%s'), ",$d['id'],$d['time'],$d['pay_time'],$d['site_id'],$d['site_code'],$d['user_id'],$d['user_name'],$d['user_phone'],$d['delivery_type'],$d['delivery_date'],$d['delivery_address']);
    }
    $sql = substr($sql,0,-2) . ';';
   echo $sql;
    return $sql;
}

function insertDetailSql($data){
    $sql = "replace into order_detail(`order_id`,`time`,`pay_time`,`delivery_date`,`product_id`,`product_code`,`number`) values";
    foreach ($data as $d){
        $sql .= sprintf("('%s','%s','%s','%s','%s','%s','%d'), ",$d['order_id'],$d['time'],$d['pay_time'],$d['delivery_date'],$d['product_id'],$d['product_code'],$d['number']);
    }
    $sql = substr($sql,0,-2) . ';';
   echo $sql;
    return $sql;
}

function insertSiteProductSql($date){
    $sql = sprintf("INSERT INTO order_site_product (
                `site_id`,
                `site_code`,
                `worker_x`,
                `worker_y`,
                `worker_z`,
                `product_id`,
                `product_code`,
                `number`,
                `delivery_date`
            ) SELECT
                site.id,
                site.code,
                MAX(CASE task.job WHEN 'x' THEN task.worker_id END) worker_x,
                MAX(CASE task.job WHEN 'y' THEN task.worker_id END) worker_y,
                MAX(CASE task.job WHEN 'z' THEN task.worker_id END) worker_z,
                order_detail.product_id,
                order_detail.product_code,
                SUM(distinct order_detail.number) `number`,
                order_detail.delivery_date
            FROM
                order_detail
            INNER JOIN order_info ON order_info.id = order_detail.order_id
            LEFT JOIN site ON site.id = order_info.site_id
            LEFT JOIN task on task.task_id = site.`code`
            where order_detail.delivery_date = '%s' and site.order_type = 1 and task.job in ('x','y','z') and task.additional = 0 
            GROUP BY order_detail.order_id;",$date);
    echo $sql;
    return $sql;
}

function insertProductSql($date){
    $sql = sprintf("INSERT INTO order_product (
                `date`,
                `product_id`,
                `product_code`,
                `require_number`
            ) SELECT
                delivery_date date,
                product_id,
                product_code,
                SUM(number)
            FROM
                order_detail
            WHERE
                delivery_date = '%s'
            GROUP BY
                product_id;",$date);
    echo $sql;
    return $sql;
}

function insertPurchaseRequireSql($date){
    $sql = sprintf("INSERT INTO purchase_require (
                `date`,
                `category_id`,
                `number`
            ) SELECT 
                order_detail.delivery_date,
                product.category_class3,
                order_detail.number * product.number
            FROM 
                order_detail 
            INNER JOIN product ON product.id = order_detail.product_id 
            WHERE 
                order_detail.delivery_date = '%s' 
            GROUP BY 
                product.category_class3;",$date);
   echo $sql;
   return $sql;
}

function insertPurchaseSql($date){
    $sql = sprintf("INSERT INTO purchase (
                `date`,
                `category_id`,
                `type`,
                `require_number`,
                `require_weight`
            ) SELECT 
                purchase_require.date,
                purchase_require.category_id,
                stock.type,
                SUM(purchase_require.number),
                FORMAT(SUM(purchase_require.number * stock.weight) / 500, 2)
            FROM
                purchase_require
            INNER JOIN stock ON stock.category_id = purchase_require.category_id
            WHERE 
                purchase_require.date = '%s' 
            GROUP BY 
                purchase_require.category_id;",$date);
   echo $sql;
   return $sql;
}

function updateWorkerWSql($date){
    $sql = sprintf("UPDATE purchase 
            INNER JOIN (
                SELECT 
                    task_id,
                    worker_id
                FROM 
                    task 
                WHERE 
                    job = 'w' and additional = '1' 
            ) task ON task.task_id = purchase.category_id 
            SET worker_w = task.worker_id 
            WHERE 
                purchase.date = '%s'",$date);
   echo $sql;
   return $sql;
}

function updateWorkerXSql($date){
    $sql = sprintf("UPDATE order_info 
            INNER JOIN (
                SELECT 
                    task_id,
                    worker_id
                FROM 
                    task 
                WHERE 
                    job = 'x' 
            ) task ON task.task_id = order_info.site_code 
            SET worker_x = task.worker_id 
            WHERE 
                order_info.delivery_date = '%s'",$date);
   echo $sql;
   return $sql;
}

function updateWorkerYSql($date){
    $sql = sprintf("UPDATE order_info 
            INNER JOIN ( 
                SELECT 
                    task_id,
                    worker_id
                FROM 
                    task 
                WHERE 
                    job = 'y' 
            ) task ON task.task_id = order_info.site_code 
            SET worker_y = task.worker_id 
            WHERE 
                order_info.delivery_date = '%s'",$date);
   echo $sql;
   return $sql;
}

function updateWorkerZSql($date){
    $sql = sprintf("UPDATE order_info
            INNER JOIN ( 
                SELECT 
                    task_id,
                    worker_id,
                    additional 
                FROM 
                    task 
                WHERE 
                    job = 'z' 
            ) task ON task.task_id = order_info.site_code and abs(task.additional - 1) = order_info.delivery_type 
            SET worker_z = task.worker_id 
            WHERE 
                order_info.delivery_date = '%s'",$date);
   echo $sql;
   return $sql;
}

?>