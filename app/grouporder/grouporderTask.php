<?php 
namespace app\grouporder;
use lib\Task;
use lib\Db;
use PDOException;
/**
 * @author     村长<8044023@qq.com>
 * @copyright  TimePHP
 * @license    https://github.com/qq8044023/timePHP
 */
class grouporderTask extends Task{
    public function run()
    {
        $this->updateOrder('address');
        $this->updateOrder('detail');
        $this->updateOrder('from');
    }

    private function updateOrder($table){
        $time = time();
        $date = Date('Y-m-d',$time);
        $hour = Date('H',$time);
        if($hour == '00'){
            $date = Date('Y-m-d',strtotime("$date -1 day"));
        }

        $db = Db::setConfig($this->getConfig()["HOMEGARDEN"]);
        $model = $db->table()->model();
        $data = $model->query(getSelectSql($table,$date));
        $db = Db::setConfig($this->getConfig()["HORSEMEN"]);
        $model = $db->table()->model();
        try{
            $model->beginTrans();
            $model->query(getInsertSql($table,$data));
            $model->commitTrans();
            echo 'DOME';
        }catch(PDOException $e){
            $model->rollBackTrans();
            echo "==导入".$table."订单失败==";
        }
    }

}