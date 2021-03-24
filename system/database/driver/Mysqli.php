<?php
defined('HOMEBASE') OR exit('No direct script access allowed');
/**
 * 该类没有用 mysqli::prepare 是因为在bind_parma的时候不像pdo那样是key，value
 * 而是一串，这样就容易出现不可预知的顺序问题
 * 
 */
class DB_Driver_Mysqli {
    protected $_mysqli = null;
    protected $table;

    // 数据库主键
    protected $primary = 'id';
    // WHERE拼装后的条件
    protected $filter = '';
    protected $order = '';
    protected $limit = '';
    protected $select = '*';
    function __construct($params){
        $this->db_connect($params);
    }
    public function db_connect($params) {        
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $charset = empty(@$params['charset']) ? 'utf8': $params['charset'];
        $port = empty(@$params['port']) ? 3306: $params['port'];

        $this->_mysqli = new mysqli($params['hostname'], $params['username'], $params['password'], $params['database'],$port);
        if ($this->_mysqli->connect_errno) {
            throw new SqliConnectionException();
        }
        $this->_mysqli ->set_charset($charset);
    }


    /**
     * 执行自定义的sql,切记保证参数的安全性，不建议直接使用
     *
     * @param string $sql
     * @return mysql_result
     */
    public function query($sql){
        return $this->_mysqli->query($sql);
    }
    public function get_connect(){
        return $this->_mysqli;
    }
    protected function _format_where_one($key,$val){
        $key = trim($key);
        if(preg_match("/(\s|<|>|!|=)/i", $key,$arr)) {
            $pos = strpos($key,$arr[1]);
            $fkey = substr($key,0,$pos);
            $fop = trim(substr($key, $pos));
    
            if(is_null($val)){
                if($fop == '!='){
                    $format = " `$fkey` is not null ";
                } else {
                    $format = " `$fkey` is null ";
                }
                $fvalue = null;        
            } else {
                $fvalue = $this->_escape_str($val);
                if($fvalue == intval($fvalue)){
                    $format = " `$fkey` $fop $fvalue";
                } else{
                    $format = " `$fkey` $fop '$fvalue'";
                }
            }
        } else {
            if(is_null($val)){
                $fkey = $key;
                $format = " `$key` is null ";
                $fvalue = null;
            } else {
                $fkey = $key;
                $fvalue = $this->_escape_str($val);
                if($fvalue == intval($fvalue)){
                    $format = " `$fkey` = $fvalue";
                } else{
                    $format = " `$fkey` = '$fvalue'";
                }
            }
        }    
        return array(
            $fkey ,$fvalue, $format
        );
    }
    /**
     * format where filter, only support and 
     * @todo or 
     *
     * @param array $find
     * @return this
     */
    public function where( $find = array() ){
        if(empty($find)){
            return $this;
        }
        if(!is_array($find)){
            throw new SqlFormatException('find is not array');
        }
        $filter_array = array();
        foreach($find as $key =>$val){
            list($fkey, $fvalue, $format) = $this->_format_where_one($key, $val);
            $filter_array[] = $format;
        }
        $this->filter = 'where '.implode(' and ', $filter_array);
        return $this;
    }
    /**
     *
     * @param array $order 排序条件
     * @return $this
     */
    public function order($order = array()) {
        if(!empty($order)) {
            if(is_string($order)){
                $order = $this->_filter_order($order);
                $this->order = "order by $order";
            } else if(is_array($order)){
                $order = array_map('_filter_order',$order);
                $this->order = ' order by '.implode(',',$order);
            } else {
                $this->order = "order by id desc"; //default
            }
        }
        return $this;
    }

    public function limit($limit,$offset = 0){
        $limit = intval($limit);
        $offset = intval($offset);
        $this->limit = " limit $offset, $limit ";
        return $this;
    }
    /**
     * 去掉可能存在注入的关键字
     *
     * @param [type] $value
     * @return void
     */
    protected function _filter_order($value){
        return str_replace(array("'",'"',';'),'',$value);
    }
    /**
     * clear the params and filter
     *
     * @return void
     */
    protected function _clean_params(){
        $this->filter = '';
        $this->order = '';
        return true;
    }
    /**
     * select string
     *
     * @param string $select
     * @return this
     */
    public function select($select){
        if(empty($select) ){
            $select = '*';
        }
        $this->select = $select;
        return $this;
    }
    /**
     * fetch one result
     *
     * @param int $tablename
     * @return array
     */
    public function fetch($tablename) {
        $sql = sprintf("select %s from `%s`  %s %s", $this->select, $tablename, $this->filter,$this->limit);
        echo $sql."\n";
        $result = $this->_mysqli->query($sql);
        $result = $this->_mysqli->query($sql);
        if(!$result){
            return false;
            // throw new SqliQueryException();
        }
        return $this->_fetch($result);


    }
    protected function _fetch($result){
        if($row = $result->fetch_assoc()){
            return $row;
        }
        return array();
    }
    protected function _fetch_all($result){
        $ret = array();
        while ($row = $result->fetch_assoc()) {
            $ret[] = $row;
        }
        return $ret ;
    }
    /**
     * fetch result array
     *
     * @param string $tablename
     * @return array
     */
    public function fetch_all($tablename) {
        $sql = sprintf("select %s from `%s` %s %s %s", $this->select, $tablename, $this->filter,$this->order, $this->limit);
        echo $sql."\n";
        $result = $this->_mysqli->query($sql);
        if(!$result){
            return false;
            // throw new SqliQueryException();
        }
        return $this->_fetch_all($result);
    }

    /**
     * delete some data
     *
     * @param string $tablename
     * @return int
     */
    public function delete($tablename) {
        $sql = sprintf("delete from `%s` %s", $tablename, $this->filter);
        $result = $this->_mysqli->query($sql);
        if($result){
            return true;
        } else {
            return false;
            // throw new SqliDelException();
        }
        return true;
    }

    /**
     * add some data
     *
     * @param array $data
     * @param string $tablename
     * @return void
     */
    public function add($data,$tablename) {
        $sql = sprintf("insert into `%s` %s", $tablename, $this->_format_insert($data));
        $result = $this->_mysqli->query($sql);
        if($result){
            return $this->_mysqli->insert_id;
        } else {
            return false;
            // throw new SqliAddException();
        }
        return false;
    }

    // 修改数据
    public function update($data,$tablename) {
        $sql = sprintf("update `%s` set %s %s", $tablename, $this->_format_update($data), $this->filter);
        // echo $sql."\n";
        $result = $this->_mysqli->query($sql);
        if($result){
            return true;
        } else {
            return false;
            // throw new SqliUpdateException();
        }
    }

    // 将数组转换成插入格式的sql语句
    protected function _format_insert($data) {
        $fields = array();
        $names = array();
        // print_r($data);die();
        foreach ($data as $key => $value) {
            $value = $this->_escape_str($value);
            $fields[] = sprintf("`%s`", $key);
            $names[] = sprintf("'%s'", $value);
        }

        $field = implode(',', $fields);
        $name = implode(',', $names);
        return sprintf("(%s) values (%s)", $field, $name);
    }

    // 将数组转换成更新格式的sql语句
    protected function _format_update($data) {
        $fields = array();
        foreach ($data as $key => $value) {
            $value = $this->_escape_str($value);
            $fields[] = sprintf("`%s` = '%s'", $key, $value);
        }

        return implode(',', $fields);
    }
    protected function _escape_str($str) {
        $str = trim($str);
        return $this->_mysqli->real_escape_string($str);
    }
}