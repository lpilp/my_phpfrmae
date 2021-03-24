<?php
defined('HOMEBASE') OR exit('No direct script access allowed');
class DB_Driver_Pdo {
    protected $_pdo = null;
    protected $table;

    // 数据库主键
    protected $primary = 'id';
    // WHERE拼装后的条件
    protected $filter = '';
    protected $order = '';
    protected $limit = '';
    protected $select = '*';
    // Pdo bindParam()绑定的参数集合
    protected $param = array();
    function __construct($params){
        $this->db_connect($params);
    }
    public function db_connect($params){
        $dsn = $this->_format_dsn($params);
        try {
            $option = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC);
            $this->_pdo = new PDO($dsn, $params['username'], $params['password'], $option);
        } catch (PDOException $e) {
            throw new PdoConnectionException('can not connected db, please check it');
        }
    }

    protected function _format_dsn($params) {

        $dbtype = empty(@$params['dbtype']) ? 'mysql': $params['dbtype'];
        $charset = empty(@$params['charset']) ? 'utf8': $params['charset'];
        $port = empty(@$params['port']) ? 3306: $params['port'];
        switch ($dbtype) {
            case 'mysql':
                return sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s',$params['hostname'],$port,$params['database'],$charset);
                break;
            case 'mssql':
            case 'sqlsrv':
                return sprintf('sqlsrv:Server=%s,%d;Database=%s;charset=%s',$params['hostname'],$port,$params['database'],$charset);
                break;
            case 'pgsql':
                return sprintf('pgsql:host=%s;port=%d;dbname=%s;charset=%s',$params['hostname'],$port,$params['database'],$charset);
                break;
            default :
                throw new DbDsnNotExistException("please config the $dbtype dsn");            
        }            
    }
    /**
     * 执行自定义的sql,切记保证参数的安全性，不建议直接使用
     *
     * @param string $sql
     * @return PDOStatement
     */
    public function query($sql){
        return $this->_pdo->query($sql);
    }
    public function get_connect(){
        return $this->_pdo;
    }
    /**
     * 查询条件拼接，使用方式：
     *
     * $this->where(['id = 1','and title="Web"', ...])->fetch();
     * 为防止注入，建议通过$param方式传入参数：
     * $this->where(['id = :id'], [':id' => $id])->fetch();
     *
     * @param array $where 条件
     * @return $this 当前对象
     */
    public function where2($where = array(), $param = array()){
        if ($where) {
            $this->filter .= ' WHERE ';
            $this->filter .= implode(' ', $where);

            $this->param = $param;
        }

        return $this;
    }
    protected function _format_where_one($key,$val){
        $key = trim($key);
        if(preg_match("/(\s|<|>|!|=)/i", $key,$arr)) {
            $pos = strpos($key,$arr[1]);
            $fkey = substr($key,0,$pos);
            $fop = trim(substr($key, $pos));
    
            if(is_null($val)){
                if($fop == '!='){
                    $format = " $fkey is not null ";
                } else {
                    $format = " $fkey is null ";
                }
                $fvalue = null;        
            } else {
                $format = "$fkey $fop :$fkey";
                $fvalue = trim($val);
            }
    
        } else {
            if(is_null($val)){
                $fkey = $key;
                $format = " $key is null ";
                $fvalue = null;
            } else {
                $fkey = $key;
                $format = "$key = :$key";
                $fvalue = trim($val);
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
            if (!is_null($fvalue)) { //null 的操作的话,就不要再绑定参数了
                $this->param[$fkey] = $fvalue;
            }
        }
        $this->filter = 'where '.implode(' and ', $filter_array);
        return $this;
    }
    /**
     * 拼装排序条件，使用方式：
     *
     * $this->order(['id DESC', 'title ASC', ...])
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
        $this->param = array();
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
        $sth = $this->_pdo->prepare($sql);
        $sth = $this->_bind_param($sth, $this->param);
        $sth->execute();
        $this->_clean_params(); 
        return $sth->fetch();
    }
    /**
     * fetch result array
     *
     * @param string $tablename
     * @return array
     */
    public function fetch_all($tablename) {
        $sql = sprintf("select %s from `%s` %s %s %s", $this->select, $tablename, $this->filter,$this->order, $this->limit);
        $sth = $this->_pdo->prepare($sql);
        $sth = $this->_bind_param($sth, $this->param);
        $sth->execute();
        $this->_clean_params(); 
        return $sth->fetchAll();
    }

    /**
     * delete some data
     *
     * @param string $tablename
     * @return int
     */
    public function delete($tablename) {
        $sql = sprintf("delete from `%s` %s", $tablename, $this->filter);
        $sth = $this->_pdo->prepare($sql);
        $sth = $this->_bind_param($sth,  $this->param);
        $sth->execute();
        $this->_clean_params(); 
        return $sth->rowCount();
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
        $sth = $this->_pdo->prepare($sql);
        $sth = $this->_bind_param($sth, $data);
        // $sth = $this->_bind_param($sth, $this->param);
        $sth->execute();
        $this->_clean_params(); 
        return $this->_pdo->lastInsertId();
    }

    // 修改数据
    public function update($data,$tablename) {
        $sql = sprintf("update `%s` set %s %s", $tablename, $this->_format_update($data), $this->filter);
        // echo $sql."\n";
        $sth = $this->_pdo->prepare($sql);
        $sth = $this->_bind_param($sth, $data);
        $sth = $this->_bind_param($sth, $this->param);
        $sth->execute();
        $this->_clean_params(); 
        return $sth->rowCount();
    }

    /**
     * 占位符绑定具体的变量值
     * @param PDOStatement $sth 要绑定的PDOStatement对象
     * @param array $params 参数
     * @return PDOStatement
     */
    protected function _bind_param(PDOStatement $sth, $params = array()) {
        // print_r($params);
        if(empty($params)){
            return $sth;
        }
        foreach ($params as $param => &$value) {
            $param = is_int($param) ? $param + 1 : ':' . ltrim($param, ':');
            $sth->bindParam($param, $value);
        }

        return $sth;
    }


    // 将数组转换成插入格式的sql语句
    protected function _format_insert($data) {
        $fields = array();
        $names = array();
        // print_r($data);die();
        foreach ($data as $key => $value) {
            $fields[] = sprintf("`%s`", $key);
            $names[] = sprintf(":%s", $key);
        }

        $field = implode(',', $fields);
        $name = implode(',', $names);

        return sprintf("(%s) values (%s)", $field, $name);
    }

    // 将数组转换成更新格式的sql语句
    protected function _format_update($data) {
        $fields = array();
        foreach ($data as $key => $value) {
            $fields[] = sprintf("`%s` = :%s", $key, $key);
        }

        return implode(',', $fields);
    }
}