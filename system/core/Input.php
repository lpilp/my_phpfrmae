<?php
defined('HOMEBASE') OR exit('No direct script access allowed');
// class Input

class RT_Input {
    /**
     * Undocumented function
     *
     * @param string $key
     * @param string $type
     * @param boolean $required
     * @param string $default
     * @return string|int
     */
    public function get($key, $type='str', $required = false, $default='') {
        $value = isset($_GET[$key]) ? $_GET[$key] :'';
        if (empty($value) && !$required) {
            return $default;
        }
        if ($type=='int') {
            return intval($value);
        } else if($type=='url'){
            $value = str_replace(array('<','>',"'",'"'," ","\t"),'',$value);
        }
        return htmlentities($value, ENT_QUOTES);
    }
    /**
     * Undocumented function
     *
     * @param string $key
     * @param string $type
     * @param boolean $required
     * @param string $default
     * @return string|int
     */
    public function post($key, $type='str', $required = false, $default='') {

        $value = isset($_POST[$key]) ? $_POST[$key] :'';
        if (empty($value) && !$required) {
            return $default;
        }
        if ($type=='int') {
            return intval($value);
        } else if($type=='url'){
            $value = str_replace(array('<','>',"'",'"'," ","\t"),'',$value);
        }
        return htmlentities($value, ENT_NOQUOTES);
    }   
}