<?php
defined('HOMEBASE') OR exit('No direct script access allowed');
class ItemModel extends RT_Model{
    protected $table = "item";
    function __construct() {
        parent::__construct();
        $this->db = $this->load->database('mysqli');
    }

    public function add_item($nick, $msg){
        $data = array();
        $data['nick'] = $nick;
        $data['msg'] = $msg;
        $data['ctime'] = $data['uptime']= time();
        return $this->_add($data);
    }
    public function update_time($id){
        $find = array();
        $find['id'] = $id;
        $update = array();
        $update['uptime'] = time();
        $result = $this->_update($update,$find);
        if($result===false){
            return $false;
        }
        return true;
    }

    public function update_some($update, $find){
        $result = $this->_update($update,$find);
        if($result===false){
            return $false;
        }
        return true;
    }
    public function lists($find,$page=1,$pagesize=10){
        $page = intval($page);
        if($page<=1){
            $page = 1;
        }
        $pagesize = intval($pagesize);
        $pagesize = ($pagesize>100 ||  $pagesize<2) ? 10 : $pagesize;
        $offset = $pagesize * ($page-1);
        $result = $this->_fetch_all($find, '*', 'id desc', $pagesize, $offset);
        return $result;
    }

    public function detail($id){
        $find = array();
        $find['id'] = $id;
        $result = $this->_fetch($find, '*');
        return $result;
    }

    public function delete_one($id){
        $find['id'] = $id;
        return $this->_delete($find);
    }
    public function delete_some($find){
        return $this->_delete($find);
    }
}