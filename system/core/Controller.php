<?php
defined('HOMEBASE') OR exit('No direct script access allowed');
class RT_Controller{
    protected $input=null;
    protected $load = null;
    protected $_tpl_data = array();
    protected $_controller = 'welcome';
    protected $_action = 'index';

    function __construct($controller, $action) {
        $this->_controller = $controller;
        $this->_action = $action;
        $this->input = new RT_Input();
        $this->load = get_loader();
    }

    protected function assign($key, $value) {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->_tpl_data[$k] = $v;
            }
        } else {
            $this->_tpl_data[$key] = $value;
        }
        return true;
	}

	protected function display($page = 'main', $data = array()) {
        $data = array_merge($this->_tpl_data, $data);
        if (!isset($data['tpl'])) {
            $class = strtolower($this->_controller);
            $method = strtolower($this->_action);
            // $data['tpl'] = "{$class}/{$method}";
            $data['tpl'] = $class.DIRECTORY_SEPARATOR.$method;
        }
        $this->load->view($page, $data);
	}
	/**
	 * 成功信息
	 *
	 * @param int $status
	 * @param string $msgS
	 * @param array $data
	 */
	protected function success($data = array(), $status = 0, $msg = 'OK') {
        $result = array(
            'status' => $status,
            'msg' => $msg,
            'data' => $data,
        );
        header('Content-Type:application/json; charset=utf-8');
        die(json_encode($result));

	}

	/**
	 * 错误信息
	 *
	 * @param int $status
	 * @param string $msg
	 * @param array $data
	 */
	protected function error($status = 1, $msg = '', $data= array()) {
        $result = array(
            'status' => $status,
            'msg' => $msg,
            'data' => $data,
        );
        header('Content-Type:application/json; charset=utf-8');
        die(json_encode($result));

	}

}