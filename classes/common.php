<?php

class Common {

    protected $db;
    protected $get;
    protected $post;

    function __construct() {
        $this->db = new Database();
        $this->get = $this->getQueryString();
        $this->post = $_POST;
        
        \Stripe\Stripe::setApiKey(API_SECRET_KEY);
    }

    function pr($post, $exit = false) {
        if (is_array($post)) {
            echo '<pre>';
            print_r($post);
            echo '</pre>';
        } else {
            echo $post;
        }

        if ($exit)
            exit;
    }
    
    function getQueryString() {
        $get = $_GET;
        unset($get['page'], $get['class'], $get['method']);
        return $get;
    }
    

    function dieError($msg) {
        $ary = array('error' => $msg);
        echo json_encode($ary);
        exit;
    }

    function dieSuccess($msg, $ary = '') {
        $aray = array('success' => $msg);
        if (!empty($ary) && is_array($ary))
            $aray = array_merge($aray, $ary);
        echo json_encode($aray);
        exit;
    }
    
    function convertToCents($amount) {
        return (floatval($amount) * 100);
    }
}

?>
