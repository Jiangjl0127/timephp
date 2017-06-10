<?php 
/**
 * 配置文件
 * @author     村长<8044023@qq.com>
 * @copyright  TimePHP
 * @license    https://github.com/qq8044023/timePHP
 *   */
return [
    "TASK"=>array(//任务相关配置【必填项】
        "name"=>"backup",//进程名
        "count"=>1,//进程数 默认为1，如果要开启多进程 请开启redis 使用队列
        "status"=>-1,//1 启动 -1停止
        "processType"=>false,//默认false  （该选项只有系统进程才会用到）
        /**
         * 格式
         * m    1,3,5,12,25 12:20:00      每个月的几号几点执行
         * w    1,3,5 12:20:00      一周的1,3,5 12:20:00执行一次     0是星期天
         * d    12:20:00            每天12:20:00执行一次
         * h    2                   每2小时执行一次
         * i    2                   每2分钟执行一次
         * s    3                   每3秒执行一次
         *   */
        "timeType"=>"h",//时间类型  m号  w周  d天  h小时     i分钟 s秒
        "timePeriod"=>"20",//时间
    ),
    "EMAIL"=>array(
        'SMTP_HOST'   => 'smtp.163.com', //SMTP服务器
        'SMTP_PORT'   => '25', //SMTP服务器端口
        'SMTP_USER'   => '15640380127@163.com', //SMTP服务器用户名
        'SMTP_PASS'   => 'asdf87020125', //SMTP服务器密码
        'FROM_EMAIL'  => '15640380127@163.com', //发件人EMAIL
        'FROM_NAME'   => '管理员', //发件人名称
        'REPLY_EMAIL' => '', //回复EMAIL（留空则为发件人EMAIL）
        'REPLY_NAME'  => '', //回复名称（留空则为发件人名称）
        "GET_EMAIL"   => array('jiangjialin@lnxzd.com'),//接收邮箱地址
    ),
    "DB"=>array(
        'DB_TYPE' => 'mysql',
        'DB_HOST' => '127.0.0.1',
        'DB_NAME' => 'homegardenTest',
        'DB_USER' => 'root',
        'DB_PWD' => 'LNxzd200017',
        'DB_PORT' => '3306',
        'DB_CODE'=>'utf8'
    ),
    "BAKDB"=>array(
        "DBDIR"=>"/home/wwwroot/timePHP-master/tmp/",//需要备份的路径
        "DBNAME"=>array("R1","R2")//需要备份的数据库
    )
];
