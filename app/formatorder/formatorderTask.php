<?php 
namespace app\formatorder;
use lib\Task;
use lib\Db;
/*use lib\common\SystemFun;*/
use PDOException;
/**
 * @author     村长<8044023@qq.com>
 * @copyright  TimePHP
 * @license    https://github.com/qq8044023/timePHP
 */
class formatorderTask extends Task{
    public function run(){
        date_default_timezone_set('PRC'); //默认时区
        $this->go();
    }

    public function go(){
        $db = Db::setConfig($this->getConfig()["HOMEGARDEN"]);
        $model = $db->table()->model();

        $time = time();
        $date = Date('Y-m-d',$time);
        $hour = Date('H',$time);
        if($hour == '23'){
            $date = Date('Y-m-d',strtotime("$date +1 day"));
        }

        try{
			$info = $model->query(selectInfoSql($date,2)); // echo '==查询订单==';
			$detail = $model->query(selectDetailSql($date,2)); // echo '==查询订单详情==';
			if($info) {
                $db = Db::setConfig($this->getConfig()["TTCCW"]);
                $model = $db->table()->model();
                $model->beginTrans();
                // 插入订单
				$model->query(insertInfoSql($info)); // echo '==插入格式化订单==';
				$model->query(insertDetailSql($detail)); // echo '==插入格式化订单详情==';
                // 插入大包
                $model->query(insertSiteProductSql($date)); // echo '==订单分品==';
                // 产品分包统计
				$model->query(insertProductSql($date)); // echo '==订单分包==';
                // 采购需求
                $model->query(insertPurchaseRequireSql($date)); // echo '==更新采购需求==';
                // 采购统计
                $model->query(insertPurchaseSql($date)); // echo '==生成采购订单==';

                $model->query(updateWorkerWSql($date)); // echo '==匹配采购人员==';
                $model->query(updateWorkerXSql($date)); // echo '==匹配配货人员==';
                $model->query(updateWorkerYSql($date)); // echo '==匹配物流人员==';
                $model->query(updateWorkerZSql($date)); // echo '==匹配配送人员==';
                $model->commitTrans();
                echo 'DOME';
			}
        }catch (PDOException  $e){
            $model->rollBackTrans();
            echo '导入订单失败！';
        }
    }

}