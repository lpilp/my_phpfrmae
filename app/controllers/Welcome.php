<?php
defined('HOMEBASE') OR exit('No direct script access allowed');
class Welcome extends RT_Controller{

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
}