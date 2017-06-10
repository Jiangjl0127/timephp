<?php 
namespace app\sendmessage;
use lib\Task;
use lib\Db;
use lib\common\SystemFun;
/**
 * @author     村长<8044023@qq.com>
 * @copyright  TimePHP
 * @license    https://github.com/qq8044023/timePHP
 */
class sendmessageTask extends Task{
    public function run()
    {
        $db = Db::setConfig($this->getConfig()["DB"]);
        $query = $db->table("message")->where("code is null")->limit(10)->select();
        if ($query) {
            foreach ($query as $q){
                $data = $this->sendmessage($q['name'],$q['address'],$q['phone']);
                $db->table("message")->where(array("id"=>$q['id']))->save($data);
            }
        }
    }

    public function sendmessage($username,$address,$phone){
        SystemFun::import("extend@Alidayu@top@TopClient");
        SystemFun::import("extend@Alidayu@top@ResultSet");
        SystemFun::import("extend@Alidayu@top@RequestCheckUtil");
        SystemFun::import("extend@Alidayu@top@TopLogger");
        SystemFun::import("extend@Alidayu@top@request@AlibabaAliqinFcSmsNumSendRequest");

		$client = new \TopClient;
        $client->appkey = "23423512"; //$appkey
        $client->secretKey = "248452cae29958580d12a3a1f300fce4"; //$secret
		$req = new \AlibabaAliqinFcSmsNumSendRequest;
		$req->setExtend("");
		$req->setSmsType("normal");
		$req->setSmsFreeSignName("自家菜园");
		$req->setSmsParam("{\"username\":\"".$username."\",\"address\":\"".$address."\"}");
		$req->setRecNum($phone);
		$req->setSmsTemplateCode("SMS_53205290");
		$resp = $client->execute($req);
        $resp = get_object_vars($resp);
		if(isset($resp['result'])){
            return array("code"=>"\"0\"","msg"=>"\"发送成功\"","sendtime"=>"\"".date('Y-m-d H:i:s')."\"");
        }else{
            return array("code"=>"\"".$resp['sub_code']."\"","msg"=>"\"".$resp['sub_msg']."\"","sendtime"=>"\"".date('Y-m-d H:i:s')."\"");
        }
    }
}
