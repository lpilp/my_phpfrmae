<?php
defined('HOMEBASE') OR exit('No direct script access allowed');
class RT_Model{
    protected $table ='';
    protected $db = null;
    protected $load = null;
    function __construct(){
        $this->load = get_loader();       
    } 
    
    protected function _add($data){
        $result =  $this->db->add($data,$this->table);
        return $result;
    }
    protected function _delete($find){
        $result =  $this->db->where($find)->delete($this->table);
        return $result;
    }
    protected function _update($data, $find){
        $result =  $this->db->where($find)->update($data,$this->table);
        return $result;
    }
    protected function _fetch($find, $select='*',$order = 'id desc'){
        $result =  $this->db->select($select)->where($find)->order($order)->limit(1)->fetch($this->table);
        if($result){
            return $result;
        }
        return array();
    }
    protected function _fetch_all($find, $select='*',$order = 'id desc',$limit = 10,$offset=0){
        $result =  $this->db->select($select)->where($find)->order($order)->limit($limit,$offset)->fetch_all($this->table);
        if($result){
            return $result;
        }
        return array();
    }
}