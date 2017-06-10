<?php 
namespace app\sendemail;
use lib\Task;
use lib\Db;
use lib\common\SystemFun;
/**
 * @author     村长<8044023@qq.com>
 * @copyright  TimePHP
 * @license    https://github.com/qq8044023/timePHP
 */
class sendemailTask extends Task{
    public function run(){
        $config=$this->getConfig();
        $recipient = $config["EMAIL"]["GET_EMAIL"];
        $date = date("Ymd");
        $sql_arr=array();
        $dir=$config["BAKDB"]["DBDIR"];//备份数据库存放路径
        foreach ($config["BAKDB"]["DBNAME"] as $dbName){
            $sql_arr[]=$dir.$dbName."_".$date.".sql";
        }
        if(is_array($recipient)){
            foreach ($recipient as $r){
                $this->send_mail($r,'报表-'.date("Y-m-d H:i:s"),'日报表-'.date("Y-m-d H:i:s",time()),'<p>邮件来了测试</p>',$sql_arr);
            }
        }else{
            $this->send_mail($recipient,'报表-'.date("Y-m-d H:i:s"),'日报表-'.date("Y-m-d H:i:s",time()),'<p>邮件来了测试</p>',$sql_arr);
        }

    }
    /**
     * 邮件发送
     * @param unknown $to
     * @param unknown $name
     * @param string $subject
     * @param string $body
     * @param unknown $attachment  */
    public function send_mail($to, $name, $subject = '', $body = '', $attachment = null){
        //引用插件
        SystemFun::import("extend@PHPMailer@PHPMailerAutoload");
        //获取 配置文件的邮件信息
        $config=$this->getConfig()["EMAIL"];
        $mail = new \PHPMailer(); //PHPMailer对象
        $mail->IsSMTP();
        $mail->CharSet='UTF-8'; //设置邮件的字符编码，这很重要，不然中文乱码
        $mail->SMTPAuth = true;                  //开启认证
        $mail->Port = $config['SMTP_PORT'];
        $mail->Host = $config['SMTP_HOST'];
        $mail->Username = $config['SMTP_USER'];
        $mail->Password = $config['SMTP_PASS'];
        //$mail->IsSendmail(); //如果没有sendmail组件就注释掉，否则出现“Could  not execute: /var/qmail/bin/sendmail ”的错误提示
        $mail->AddReplyTo($config['FROM_EMAIL'],$config['FROM_NAME']);//回复地址
        $mail->From = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];
        $mail->FromName = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];
        $mail->AddAddress($to, $name);
        $mail->Subject  = $subject;
        $mail->Body = $body;
        $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; //当邮件不支持html时备用显示，可以省略
        $mail->WordWrap   = 80; // 设置每行字符串的长度
        if(is_array($attachment)){ // 添加附件
            foreach ($attachment as $file){
                echo $file;
                is_file($file) && $mail->AddAttachment($file);
            }
        }
        //$mail->AddAttachment("f:/test.png");  //可以添加附件
        $mail->IsHTML(true);
        return $mail->Send() ? true : $mail->ErrorInfo;
    }
}