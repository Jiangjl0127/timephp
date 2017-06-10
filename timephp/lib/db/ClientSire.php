<?php
namespace lib\db;
/**
 * @author     村长<8044023@qq.com>
 * @copyright  TimePHP
 * @license    https://github.com/qq8044023/timePHP
 **/
abstract class ClientSire{
    //数据库表
    protected $name;
    //条件
    protected $where=array();
    //筛选字段
    protected $filed="*";
    //选择一条信息
    protected $Db=null;
    //limit
    protected $limit=null;
    //sql语句
    protected $sql=null;
    //排序
    protected $order=null;
    //分组
    protected $group=null;
    abstract public function find();
    abstract public function gogog();
    abstract public function select();
    abstract public function add($data=[]);
    abstract public function delete();
    abstract public function save($cols=[]);
    abstract public function model();
    abstract public function getLastSql();
    //初始化
    public function __construct($table,$config){
        $this->name=$table;
        $this->connect($config);//
    }
    public function where($where=""){
        if (is_array($where)){
            $this->where=array_merge($where,$this->where);
        }else{
            $this->where=$where;
        }
        return $this;
    }
    public function filed($filed=null){
        $this->filed=$filed;
        return $this;
    }
    public function limit($limit=null){
        $this->limit=$limit;
        return $this;
    }
    public function order($order){
        $this->order=$order;
        return $this;
    }
    public function group($group){
        $this->group=$group;
        return $this;
    }
}