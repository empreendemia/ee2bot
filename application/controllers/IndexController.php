<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		  $ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		  $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else {
		  $ip = $_SERVER['REMOTE_ADDR'];
		}

        if ($ip != '127.0.0.1' && $ip != '74.200.74.193') {
            $this->_redirect('http://www.empreendemia.com.br');
        }
    }

    public function indexAction()
    {
        // action body
    }


}



