<?php 
namespace app\update;
use lib\Task;
use lib\Db;
use lib\common\SystemFun;
use PDOException;
/**
 * @author     村长<8044023@qq.com>
 * @copyright  TimePHP
 * @license    https://github.com/qq8044023/timePHP
 */
class updateTask extends Task{
    public function run(){
        $this->go();
    }

    public function go(){
        $db = Db::setConfig($this->getConfig()["DB"]);
        $model = $db->table()->model();

        $time = time();
        $date = Date('Y-m-d',$time);
        $hour = Date('H',$time);
        if($hour == '00'){
            $date_0 = Date('Y-m-d',strtotime("$date -1 day"));
            $date_1 = $date;
        }else{
            $date_0 = $date;
            $date_1 = Date('Y-m-d',strtotime("$date +1 day"));
        }
        $sql = "select queue_id,update_table,update_id,update_type from update_queue where (update_type = '0' and execution_time = \"$date_0\") or (update_type = '1' and execution_time = \"$date_1\") and result is null";
        $queue = $model->query($sql);
        $err_queue = 0;
        if($queue){
            foreach ($queue as $q){
                $result = $this->exec($q['update_table'],$q['update_id'],$q['update_type']);
                if($result != 1){
                    $err_queue++;
                }
                $id = $q['queue_id'];
                $model->query("update update_queue set result = \"$result\" where queue_id = $id");
            }
            if($err_queue){
                $model->query("insert into message(`username`,`address`,`phone`) values('DBA阁下','数据延时更新办事处有".$err_queue."条事件需要您亲自督办','15640380127')");
            }
        }
    }

    private function exec($table,$id,$type){
        $db = Db::setConfig($this->getConfig()["DB"]);
        $model = $db->table()->model();
        $compare = array(
            'products'=>'id',
            'banner'=>'banner_id',
            'theme'=>'theme_id',
            'theme_product'=>'theme_product_id',
        );
        if(!isset($compare[$table])){
            return "没有设定compare";
        }
        $uId = $compare[$table];
        $date = date('Y-m-d H:i:s');
        // 事务处理
        $model->beginTrans();

        if($type && $table == 'product'){
            $product_id = $model->query("select product_id from product where id = $id");
            try{
                $model->query("update product set is_used='0' where product_id = $product_id");
            }catch (PDOException  $e){
                $model->rollBackTrans();
                return $e->getMessage();
            }
        }

        try{
            $result = $model->query("update $table set is_used=".$type.($type?" ,on_time=\"".$date:" ,off_time=\"".$date)."\" where ".$uId." = $id");
        }catch (PDOException $e){
            $model->rollBackTrans();
            return $e->getMessage();
        }
        $model->commitTrans();
        return $result;
    }

}