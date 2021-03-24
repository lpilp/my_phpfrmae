<?php
defined('HOMEBASE') OR exit('No direct script access allowed');
class Item extends RT_Controller{

    public function index(){
        $this->assign('name','Framework');
        $this->display();       
    }
    public function test($a=1, $b=2){
        // var_dump($a,$b);
        // $this->load->help('xxx');
        $this->assign('name','Framework');
        $this->display();
    }

    public function testerror(){
        throw new FileNotExistException('message');
        
    }
    public function testall() {
        $itemModel = $this->load->model('item');
        // $itemModel2 = $this->load->model('item');
        // print_r($itemModel);
        // add 
        // $id1 = $itemModel->add_item('张三','zszszszs');
        // $id2 = $itemModel->add_item('李d四','lslslslsl');

        // var_dump($id2);die();
        // list
        /* $detail = $itemModel->detail(9);
        var_dump($detail);
        // die();
        $find['id<'] = 10;
        $lists = $itemModel->lists($find,1,2);
        print_r($lists);
        $lists = $itemModel->lists($find,1,4);
        print_r($lists); */
        /* $find['id<='] = 15;
        $update['uptime'] = 12345678901;
        var_dump($itemModel->update_some($update, $find));

        var_dump($itemModel->update_time(6)); */
        $find = array();
        $find['id<='] = 10; 
        $itemModel->delete_some($find);
    }
}